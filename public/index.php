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

// Movie API Routes
$router->add('GET', '/api/movies', 'MovieController@index');
$router->add('GET', '/api/movies/details', 'MovieController@details');
$router->add('GET', '/api/movies/featured', 'MovieController@featured');
$router->add('GET', '/api/movies/trending', 'MovieController@trending');
$router->add('GET', '/api/movies/search', 'MovieController@search');

// Newsletter API Routes
$router->add('POST', '/api/newsletter/subscribe', 'NewsletterController@subscribe');

// Booking API Routes
$router->add('GET', '/api/booking/seats', 'BookingController@seats');
$router->add('POST', '/api/booking/validate-promo', 'BookingController@validatePromo');
$router->add('POST', '/api/booking/reserve', 'BookingController@reserve');

// Admin API Routes
$router->add('POST', '/api/admin/login', 'AdminController@login');
$router->add('GET', '/api/admin/reservations', 'AdminController@reservations');
$router->add('GET', '/api/admin/stats', 'AdminController@stats');
$router->add('POST', '/api/admin/movies/create', 'AdminController@createMovie');
$router->add('POST', '/api/admin/movies/update', 'AdminController@updateMovie');
$router->add('DELETE', '/api/admin/movies/delete', 'AdminController@deleteMovie');

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
