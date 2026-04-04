<?php
// public/index.php

session_start();

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../config/database.php';

use App\Core\Router;

$router = new Router();

// Define routes
$router->add('GET', '/', function() {
    echo json_encode(['message' => 'Welcome to CinemaNoir API']);
});

// For demonstration of A1.2
$router->add('GET', '/api/status', function() {
    echo json_encode(['status' => 'online', 'version' => '1.0.0']);
});

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
