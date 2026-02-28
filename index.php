<?php
// index.php

// Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start Session
session_start();

// Define Constants
define('APPROOT', __DIR__ . '/app');
// Dynamic URLROOT for network access
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // Normalize slashes
$path = rtrim($path, '/'); // Remove trailing slash
define('URLROOT', $protocol . "://" . $host . $path);
define('SITENAME', 'Biblioteca ENSUR');

// Require Config and Helpers
require_once 'config/db.php';

// Autoloader
spl_autoload_register(function($className){
    // Determine strict class types
    if (file_exists(APPROOT . '/core/' . $className . '.php')) {
        $file = APPROOT . '/core/' . $className . '.php';
    } elseif (strpos($className, 'Controller') !== false) {
        $file = APPROOT . '/controllers/' . $className . '.php';
    } elseif (strpos($className, 'Model') !== false) {
        $file = APPROOT . '/models/' . $className . '.php';
    } elseif (file_exists(APPROOT . '/helpers/' . $className . '.php')) {
        $file = APPROOT . '/helpers/' . $className . '.php';
    } else {
        // Fallback or generic path
        $file = APPROOT . '/models/' . $className . '.php';
    }

    if(file_exists($file)){
        require_once $file;
    }
});

// Initialize Router
$router = new Router();
