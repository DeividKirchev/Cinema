<?php
// src/Models/Newsletter.php

namespace App\Models;

use Database;
use PDO;

class Newsletter {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function subscribe($email) {
        $stmt = $this->db->prepare("INSERT IGNORE INTO newsletters (email) VALUES (:email)");
        return $stmt->execute(['email' => $email]);
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM newsletters ORDER BY subscribed_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
