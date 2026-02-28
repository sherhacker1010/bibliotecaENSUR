<?php
require_once 'config/db.php';

$db = Database::getInstance();

try {
    $sql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'default.png' AFTER email";
    $db->query($sql);
    echo "Column 'profile_image' added successfully.";
} catch (PDOException $e) {
    if(strpos($e->getMessage(), "Duplicate column name") !== false){
        echo "Column 'profile_image' already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
