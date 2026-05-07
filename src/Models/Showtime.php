<?php
// src/Models/Showtime.php

namespace App\Models;

use Database;
use PDO;
use DateTime;

class Showtime {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByMovieAndDate($movie_id, $date) {
        $stmt = $this->db->prepare("
            SELECT s.*, h.name as hall_name 
            FROM showtimes s
            JOIN halls h ON s.hall_id = h.id
            WHERE s.movie_id = :movie_id 
            AND DATE(s.start_time) = :date
            ORDER BY s.start_time ASC
        ");
        $stmt->execute(['movie_id' => $movie_id, 'date' => $date]);
        return $stmt->fetchAll();
    }

    public function getByDate($date) {
        $stmt = $this->db->prepare("
            SELECT s.*, m.title, m.duration, m.genre, m.rating, m.poster_path, h.name as hall_name 
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            JOIN halls h ON s.hall_id = h.id
            WHERE DATE(s.start_time) = :date
            ORDER BY m.title, s.start_time ASC
        ");
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT s.*, m.title, m.poster_path, h.name as hall_name, h.capacity
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            JOIN halls h ON s.hall_id = h.id
            WHERE s.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getReservedSeats($showtime_id) {
        $stmt = $this->db->prepare("
            SELECT seat_id FROM reserved_seats rs
            JOIN reservations r ON rs.reservation_id = r.id
            WHERE r.showtime_id = :showtime_id AND r.status != 'cancelled'
        ");
        $stmt->execute(['showtime_id' => $showtime_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getSeatsByHall($hall_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM seats 
            WHERE hall_id = :hall_id
            ORDER BY row_num ASC, seat_num ASC
        ");
        $stmt->execute(['hall_id' => $hall_id]);
        return $stmt->fetchAll();
    }

    public function getTicketsByReservation($reservation_id) {
        $stmt = $this->db->prepare("
            SELECT rs.*, s.row_num, s.seat_num, h.name as hall_name, m.title as movie_title, st.start_time, m.poster_path, r.uid as reservation_uid, r.customer_email
            FROM reserved_seats rs
            JOIN seats s ON rs.seat_id = s.id
            JOIN reservations r ON rs.reservation_id = r.id
            JOIN showtimes st ON r.showtime_id = st.id
            JOIN halls h ON st.hall_id = h.id
            JOIN movies m ON st.movie_id = m.id
            WHERE rs.reservation_id = :reservation_id
        ");
        $stmt->execute(['reservation_id' => $reservation_id]);
        return $stmt->fetchAll();
    }
}
