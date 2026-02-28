<?php
// setup_dummy_data.php
require_once 'config/db.php';
require_once 'app/helpers/QRHelper.php';

// Helper for QR
if(!class_exists('QRHelper')){
    // Minimal mock if helper not loaded or depends on app context, 
    // but usually we can include it. 
    // If QRHelper depends on libraries, ensure they are loaded.
    // Assuming QRHelper is standalone or pure PHP.
    // If it uses QROptions etc that are not autoloade here, we might need manual requires.
    // Let's rely on simple file copy for dummy QRs if generation is complex?
    // Or just create empty files.
    class QRHelper {
        public static function generate($code, $filename = null){
            $path = 'uploads/qrcodes/';
            if(!file_exists($path)) mkdir($path, 0777, true);
            $file = $path . ($filename ?? $code) . '.png';
            // Create a dummy image for speed/simplicity or use real if available
            $im = imagecreate(150, 150);
            $bg = imagecolorallocate($im, 255, 255, 255);
            $text_color = imagecolorallocate($im, 0, 0, 0);
            imagestring($im, 5, 20, 60, substr($code, 0, 15), $text_color);
            imagepng($im, $file);
            imagedestroy($im);
            return $file;
        }
    }
}

// Database Connection
$db = Database::getInstance();

echo "<h1>Generando Datos de Prueba...</h1>";

try {
    $db->beginTransaction();

    // 1. Create Shelves
    $shelves = [
        ['name' => 'Ciencia Ficción', 'desc' => 'Libros de sci-fi y fantasía'],
        ['name' => 'Historia', 'desc' => 'Historia universal y local'],
        ['name' => 'Tecnología', 'desc' => 'Programación, IA y Hardware'],
        ['name' => 'Literatura Clásica', 'desc' => 'Obras maestras de la literatura']
    ];

    echo "Creating Shelves...<br>";
    $shelfIds = [];
    $stmt = $db->prepare("INSERT INTO shelves (name, description) VALUES (:name, :desc)");
    foreach($shelves as $s){
        $stmt->execute([':name' => $s['name'], ':desc' => $s['desc']]);
        $shelfIds[] = $db->lastInsertId();
    }

    // 2. Create Users (Readers)
    $users = [
        ['user' => 'juan.perez', 'pass' => '123456', 'name' => 'Juan Pérez', 'email' => 'juan@example.com'],
        ['user' => 'maria.gomez', 'pass' => '123456', 'name' => 'María Gómez', 'email' => 'maria@example.com'],
        ['user' => 'carlos.lopez', 'pass' => '123456', 'name' => 'Carlos López', 'email' => 'carlos@example.com'],
        ['user' => 'ana.torres', 'pass' => '123456', 'name' => 'Ana Torres', 'email' => 'ana@example.com']
    ];

    echo "Creating Users...<br>";
    $userIds = [];
    $stmt = $db->prepare("INSERT INTO users (username, password, full_name, email, role, created_at) VALUES (:user, :pass, :name, :email, 'reader', NOW())");
    foreach($users as $u){
        $hash = password_hash($u['pass'], PASSWORD_DEFAULT);
        $stmt->execute([':user' => $u['user'], ':pass' => $hash, ':name' => $u['name'], ':email' => $u['email']]);
        $userIds[] = $db->lastInsertId();
    }

    // 3. Create Books
    $books = [
        ['code' => 'LIB-001', 'title' => 'El Señor de los Anillos', 'author' => 'J.R.R. Tolkien', 'genre' => 'Fantasía', 'stock' => 3],
        ['code' => 'LIB-002', 'title' => 'Cien Años de Soledad', 'author' => 'Gabriel García Márquez', 'genre' => 'Realismo Mágico', 'stock' => 2],
        ['code' => 'LIB-003', 'title' => 'Código Limpio', 'author' => 'Robert C. Martin', 'genre' => 'Tecnología', 'stock' => 5],
        ['code' => 'LIB-004', 'title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'genre' => 'Historia', 'stock' => 4],
        ['code' => 'LIB-005', 'title' => 'Dune', 'author' => 'Frank Herbert', 'genre' => 'Ciencia Ficción', 'stock' => 3]
    ];

    echo "Creating Books and Copies...<br>";
    $bookStmt = $db->prepare("INSERT INTO books (code, title, author, genre, shelf_id, description, stock, created_at) VALUES (:code, :title, :author, :genre, :shelf_id, 'Descripción de prueba para el libro.', :stock, NOW())");
    $copyStmt = $db->prepare("INSERT INTO book_copies (book_id, unique_code, status) VALUES (:book_id, :unique_code, 'available')");

    $copies = []; // Store usable copies for loans

    foreach($books as $idx => $b){
        $shelfId = $shelfIds[$idx % count($shelfIds)];
        $bookStmt->execute([
            ':code' => $b['code'],
            ':title' => $b['title'],
            ':author' => $b['author'],
            ':genre' => $b['genre'],
            ':shelf_id' => $shelfId,
            ':stock' => $b['stock']
        ]);
        $bookId = $db->lastInsertId();

        for($i=1; $i<=$b['stock']; $i++){
            $uniqueCode = $b['code'] . '-C' . $i;
            QRHelper::generate($uniqueCode, $uniqueCode);
            $copyStmt->execute([':book_id' => $bookId, ':unique_code' => $uniqueCode]);
            $copyId = $db->lastInsertId();
            $copies[] = ['id' => $copyId, 'book_id' => $bookId, 'code' => $uniqueCode];
        }
    }

    // 4. Create Loans (Some active, some returned)
    echo "Creating Loans...<br>";
    $loanStmt = $db->prepare("INSERT INTO loans (copy_id, user_id, librarian_id, loan_date, due_date, return_date, status) VALUES (:copy_id, :user_id, 1, :loan_date, :due_date, :return_date, :status)");
    // Admin ID 1 assumed for librarian_id

    // Random active loans
    for($i=0; $i<3; $i++){
        if(empty($copies)) break;
        $copy = array_pop($copies);
        $user = $userIds[$i % count($userIds)];
        
        $loanDate = date('Y-m-d', strtotime('-' . rand(1, 5) . ' days'));
        $dueDate = date('Y-m-d', strtotime($loanDate . ' + 7 days')); // Future
        
        $loanStmt->execute([
            ':copy_id' => $copy['id'],
            ':user_id' => $user,
            ':loan_date' => $loanDate,
            ':due_date' => $dueDate,
            ':return_date' => null,
            ':status' => 'active'
        ]);
        
        // Update Copy Status
        $db->query("UPDATE book_copies SET status = 'loaned' WHERE id = " . $copy['id']);
        // Update Book Stock
        $db->query("UPDATE books SET stock = stock - 1 WHERE id = " . $copy['book_id']);
    }

    // Random returned loans (History)
    for($i=0; $i<5; $i++){
        // We reuse copies that are technically available now, but log past loans
        // Just pick random copy from original full list?
        // Let's assume we use copies that are currently 'available' (the ones still in array or previously popped but returned)
        // For simplicity, just pick a random available book id if I had a list, 
        // but here I popped copies. I'll just create historical records for copies that are 'available' now (the ones remaining in $copies)
        if(empty($copies)) break;
        $copy = $copies[array_rand($copies)]; // Don't pop, just reference
        $user = $userIds[array_rand($userIds)];
        
        $loanDate = date('Y-m-d', strtotime('-30 days'));
        $dueDate = date('Y-m-d', strtotime('-23 days'));
        $returnDate = date('Y-m-d', strtotime('-25 days')); // Returned early
        
        $loanStmt->execute([
            ':copy_id' => $copy['id'],
            ':user_id' => $user,
            ':loan_date' => $loanDate,
            ':due_date' => $dueDate,
            ':return_date' => $returnDate,
            ':status' => 'returned'
        ]);
    }

    $db->commit();
    echo "<h2 style='color:green'>Datos generados correctamente.</h2>";
    echo "<a href='index.php'>Ir al Inicio</a>";

} catch (Exception $e) {
    $db->rollBack();
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
