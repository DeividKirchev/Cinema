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
            $sql .= " AND genre LIKE :genre";
            $params['genre'] = '%' . $filters['genre'] . '%';
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] == 'archived') {
                $sql .= " AND status = 'archived'";
            } else if ($filters['status'] == 'past') {
                $sql .= " AND status = 'archived'";
            } else if ($filters['status'] == 'coming_soon') {
                $sql .= " AND status = 'coming soon'";
            } else if ($filters['status'] == 'now_playing') {
                $sql .= " AND status = 'now playing'";
            }
        } else {
             $sql .= " AND status != 'archived'"; // Default
        }

        if (!empty($filters['search'])) {
            $sql .= " AND title LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY id DESC";

        if (isset($filters['limit'])) {
            $limit = (int)$filters['limit'];
            $offset = isset($filters['offset']) ? (int)$filters['offset'] : 0;
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCount($filters = []) {
        $sql = "SELECT COUNT(*) FROM movies WHERE 1=1";
        $params = [];

        if (!empty($filters['genre'])) {
            $sql .= " AND genre LIKE :genre";
            $params['genre'] = '%' . $filters['genre'] . '%';
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] == 'archived') {
                $sql .= " AND status = 'archived'";
            } else if ($filters['status'] == 'past') {
                $sql .= " AND status = 'archived'";
            } else if ($filters['status'] == 'coming_soon') {
                $sql .= " AND status = 'coming soon'";
            } else if ($filters['status'] == 'now_playing') {
                $sql .= " AND status = 'now playing'";
            }
        } else {
             $sql .= " AND status != 'archived'"; // Default
        }

        if (!empty($filters['search'])) {
            $sql .= " AND title LIKE :search";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO movies (title, description, duration, genre, rating, release_date, director, cast, trailer_url, poster_path, status, user_rating) 
                VALUES (:title, :description, :duration, :genre, :rating, :release_date, :director, :cast, :trailer_url, :poster_path, :status, :user_rating)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $data['id'] = $id;
        $sql = "UPDATE movies SET 
                title = :title, description = :description, duration = :duration, genre = :genre, 
                rating = :rating, release_date = :release_date, director = :director, 
                cast = :cast, trailer_url = :trailer_url, poster_path = :poster_path, status = :status, user_rating = :user_rating 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getFeatured() {
        // $stmt = $this->db->prepare("SELECT * FROM movies WHERE status = 'now playing' ORDER BY release_date DESC LIMIT 1");
        $stmt = $this->db->prepare("SELECT * FROM movies WHERE title like '%Гладиатор%' LIMIT 1");
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
