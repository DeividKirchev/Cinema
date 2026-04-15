<?php
// src/Models/Movie.php

namespace App\Models;

use Database;
use PDO;

class Movie {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM movies WHERE 1=1";
        $params = [];

        if (!empty($filters['genre'])) {
            $sql .= " AND genre = :genre";
            $params['genre'] = $filters['genre'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO movies (title, description, duration, genre, rating, release_date, director, cast, trailer_url, poster_path, status) 
                VALUES (:title, :description, :duration, :genre, :rating, :release_date, :director, :cast, :trailer_url, :poster_path, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $data['id'] = $id;
        $sql = "UPDATE movies SET 
                title = :title, description = :description, duration = :duration, genre = :genre, 
                rating = :rating, release_date = :release_date, director = :director, 
                cast = :cast, trailer_url = :trailer_url, poster_path = :poster_path, status = :status 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getFeatured() {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE status = 'now playing' ORDER BY release_date DESC LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getTrending($limit = 5) {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE status = 'now playing' ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function search($query) {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE title LIKE :query AND status != 'archived'");
        $stmt->execute(['query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM movies WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
