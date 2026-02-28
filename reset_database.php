<?php
// reset_database.php
require_once 'config/db.php';

// Database Connection
$db = Database::getInstance();

echo "<h1>Restableciendo Base de Datos (Modo Producción)...</h1>";
echo "<p style='color:red; font-weight:bold;'>Advertencia: Esta acción borrará todos los préstamos, libros, estanterías y usuarios (excepto Admin).</p>";

if(!isset($_GET['confirm']) || $_GET['confirm'] != 'yes'){
    echo "<a href='reset_database.php?confirm=yes' onclick=\"return confirm('¿Está COMPLETAMENTE SEGURO? Se perderán todos los datos.');\" style='background:red; color:white; padding:10px; text-decoration:none;'>CONFIRMAR BORRADO</a>";
    exit;
}

try {
    // Disable Foreign Key Checks
    $db->query("SET FOREIGN_KEY_CHECKS = 0");

    // Truncate Tables
    echo "Truncating 'fines'...<br>";
    $db->query("TRUNCATE TABLE fines");
    
    echo "Truncating 'loans'...<br>";
    $db->query("TRUNCATE TABLE loans");
    
    echo "Truncating 'book_copies'...<br>";
    $db->query("TRUNCATE TABLE book_copies");
    
    echo "Truncating 'books'...<br>";
    $db->query("TRUNCATE TABLE books");
    
    echo "Truncating 'shelves'...<br>";
    $db->query("TRUNCATE TABLE shelves");

    // Clean Users (Keep ID 1 - Admin)
    echo "Cleaning 'users'...<br>";
    $db->query("DELETE FROM users WHERE id > 1");
    // Reset Auto Increment if possible, or just leave it.

    // Enable Foreign Key Checks
    $db->query("SET FOREIGN_KEY_CHECKS = 1");

    
    // Clean Uploads
    // QRCodes
    $files = glob('uploads/qrcodes/*'); 
    foreach($files as $file){ // iterate files
      if(is_file($file)) unlink($file); // delete file
    }
    
    // Covers
    $files = glob('uploads/covers/*'); 
    foreach($files as $file){ // iterate files
      if(is_file($file)) unlink($file); // delete file
    }
    
    echo "<h2 style='color:green'>Base de datos limpia y lista para producción.</h2>";
    echo "<a href='index.php'>Ir al Inicio</a>";

} catch (Exception $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
