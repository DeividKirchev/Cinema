<?php
// src/Models/Actor.php

namespace App\Models;

use Database;
use PDO;

class Actor {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM actors ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM actors WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO actors (name, birth_date, image_url)
            VALUES (:name, :birth_date, :image_url)
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'birth_date' => !empty($data['birth_date']) ? $data['birth_date'] : null,
            'image_url' => !empty($data['image_url']) ? $data['image_url'] : null
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE actors SET 
                name = :name,
                birth_date = :birth_date,
                image_url = :image_url
            WHERE id = :id
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'birth_date' => !empty($data['birth_date']) ? $data['birth_date'] : null,
            'image_url' => !empty($data['image_url']) ? $data['image_url'] : null,
            'id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM actors WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
