<?php
// src/Controllers/MovieController.php

namespace App\Controllers;

use App\Models\Movie;

class MovieController {
    private $movieModel;

    public function __construct() {
        $this->movieModel = new Movie();
    }

    public function index() {
        $genre = $_GET['genre'] ?? null;
        $status = $_GET['status'] ?? null;

        $filters = [];
        if ($genre) $filters['genre'] = $genre;
        if ($status) $filters['status'] = $status;

        $movies = $this->movieModel->getAll($filters);
        
        header('Content-Type: application/json');
        echo json_encode($movies);
    }

    public function details() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Movie ID is required']);
            return;
        }

        $movie = $this->movieModel->getById($id);

        if (!$movie) {
            header("HTTP/1.1 404 Not Found");
            echo json_encode(['error' => 'Movie not found']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode($movie);
    }

    public function featured() {
        $movie = $this->movieModel->getFeatured();
        header('Content-Type: application/json');
        echo json_encode($movie);
    }

    public function trending() {
        $limit = $_GET['limit'] ?? 5;
        $movies = $this->movieModel->getTrending($limit);
        header('Content-Type: application/json');
        echo json_encode($movies);
    }

    public function search() {
        $query = $_GET['q'] ?? '';
        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }
        $movies = $this->movieModel->search($query);
        header('Content-Type: application/json');
        echo json_encode($movies);
    }
}
