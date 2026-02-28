<?php
// install.php

// Database configuration
// Database configuration
$host = 'localhost';

// Detect Environment
$is_production = (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'iensur.edu.co') !== false || strpos($_SERVER['HTTP_HOST'], 'wickdevs') !== false));

if ($is_production) {
    $db_name = 'wickdevs_biblioteca';
    $user = 'wickdevs_biblioteca';
    $pass = 'o#plxvm6~H(2';
} else {
    $db_name = 'biblioteca_db';
    $user = 'root';
    $pass = '';
}

try {
    // Connect to MySQL server (without DB)
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$db_name' created or already exists.<br>";

    // Connect to the specific DB
    $pdo->exec("USE `$db_name`");

    // Create Users table
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'librarian', 'reader') NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        profile_image VARCHAR(255) DEFAULT 'default.png',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_users);
    echo "Table 'users' created/checked.<br>";
    
    // Check for missing columns in users (Schema Update)
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
    if(!$stmt->fetch()){
        $pdo->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'default.png' AFTER email");
        echo "Column 'profile_image' added to 'users'.<br>";
    }

    // Create Shelves table
    $sql_shelves = "CREATE TABLE IF NOT EXISTS shelves (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_shelves);
    echo "Table 'shelves' created.<br>";

    // Create Books table
    $sql_books = "CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(50) NOT NULL UNIQUE,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(150),
        genre VARCHAR(100),
        shelf_id INT,
        description TEXT,
        cover_image VARCHAR(255),
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (shelf_id) REFERENCES shelves(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql_books);
    echo "Table 'books' created.<br>";

    // Create Book Copies table (For individual tracking if needed, otherwise simplified logic uses `stock` in books and `loans` tracks copies abstractly or via specific ID if we implement copy tracking. 
    // The prompt says 'Cada copia física debe tener su propio ID y QR independiente'. So we DO need a copies table.)
    $sql_copies = "CREATE TABLE IF NOT EXISTS book_copies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        book_id INT NOT NULL,
        unique_code VARCHAR(50) NOT NULL UNIQUE,
        status ENUM('available', 'loaned', 'lost', 'damaged') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_copies);
    echo "Table 'book_copies' created.<br>";

    // Create Loans table
    $sql_loans = "CREATE TABLE IF NOT EXISTS loans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        copy_id INT NOT NULL,
        user_id INT NOT NULL,
        librarian_id INT NOT NULL,
        loan_date DATE NOT NULL,
        due_date DATE NOT NULL,
        return_date DATE,
        status ENUM('active', 'returned', 'overdue') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (copy_id) REFERENCES book_copies(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (librarian_id) REFERENCES users(id)
    )";
    $pdo->exec($sql_loans);
    echo "Table 'loans' created.<br>";

    // Create Fines table
    $sql_fines = "CREATE TABLE IF NOT EXISTS fines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        loan_id INT NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_fines);
    echo "Table 'fines' created.<br>";

    // Create Settings table
    $sql_settings = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value VARCHAR(255) NOT NULL
    )";
    $pdo->exec($sql_settings);
    echo "Table 'settings' created.<br>";

    // Create Notifications table
    $sql_notifications = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_notifications);
    echo "Table 'notifications' created.<br>";

    // Insert Default Admin
    // Password: Admin
    $password = password_hash('Admin', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'Admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name, email) VALUES ('Admin', ?, 'admin', 'Super Admin', 'admin@library.com')");
        $stmt->execute([$password]);
        echo "Default admin user created (user: Admin, pass: Admin).<br>";
    }

    // Insert Default Settings
    $settings = [
        'loan_days' => '3',
        'max_books' => '1',
        'fine_per_day' => '1000'
    ];
    foreach ($settings as $key => $val) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
    }
    echo "Default settings inserted.<br>";

    echo "Installation completed successfully!";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
