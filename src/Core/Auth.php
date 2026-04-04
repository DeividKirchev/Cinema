<?php
// src/Core/Auth.php

namespace App\Core;

class Auth {
    // Hardcoded admin password for simplicity as per requirements
    private static $adminPassword = 'admin_secret_pass';

    public static function login($username, $password) {
        if ($username === 'admin' && $password === self::$adminPassword) {
            $_SESSION['admin_logged_in'] = true;
            return true;
        }
        return false;
    }

    public static function logout() {
        unset($_SESSION['admin_logged_in']);
        session_destroy();
    }

    public static function isAdmin() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }

    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('Content-Type: application/json');
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['error' => 'Unauthorized access']);
            exit;
        }
    }
}
