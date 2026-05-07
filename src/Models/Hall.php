<?php
// src/Models/Hall.php

namespace App\Models;

use Database;
use PDO;
use Exception;

class Hall {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM halls ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM halls WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($name, $rows, $cols) {
        try {
            $this->db->beginTransaction();

            $capacity = $rows * $cols;

            // 1. Insert Hall
            $stmt = $this->db->prepare("INSERT INTO halls (name, capacity) VALUES (:name, :capacity)");
            $stmt->execute([
                'name' => $name,
                'capacity' => $capacity
            ]);
            $hallId = $this->db->lastInsertId();

            // 2. Generate Seats
            $seatStmt = $this->db->prepare("INSERT INTO seats (hall_id, row_num, seat_num, type) VALUES (:hall_id, :row_num, :seat_num, :type)");
            for ($r = 1; $r <= $rows; $r++) {
                for ($c = 1; $c <= $cols; $c++) {
                    $type = ($r == $rows) ? 'vip' : 'standard';
                    $seatStmt->execute([
                        'hall_id' => $hallId,
                        'row_num' => $r,
                        'seat_num' => $c,
                        'type' => $type
                    ]);
                }
            }

            $this->db->commit();
            return $hallId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $name) {
        $stmt = $this->db->prepare("UPDATE halls SET name = :name WHERE id = :id");
        return $stmt->execute(['name' => $name, 'id' => $id]);
    }

    public function delete($id) {
        // ON DELETE CASCADE in MySQL automatically deletes child rows in seats table
        $stmt = $this->db->prepare("DELETE FROM halls WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
