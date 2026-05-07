<?php
// src/Models/PromoCode.php

namespace App\Models;

use Database;
use PDO;

class PromoCode {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM promo_codes ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM promo_codes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO promo_codes (code, discount_percent, valid_until, is_active)
            VALUES (:code, :discount_percent, :valid_until, :is_active)
        ");
        return $stmt->execute([
            'code' => $data['code'],
            'discount_percent' => $data['discount_percent'],
            'valid_until' => !empty($data['valid_until']) ? $data['valid_until'] : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE promo_codes SET 
                code = :code,
                discount_percent = :discount_percent,
                valid_until = :valid_until,
                is_active = :is_active
            WHERE id = :id
        ");
        return $stmt->execute([
            'code' => $data['code'],
            'discount_percent' => $data['discount_percent'],
            'valid_until' => !empty($data['valid_until']) ? $data['valid_until'] : null,
            'is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            'id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM promo_codes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
