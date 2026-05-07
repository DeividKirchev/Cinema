<?php
// src/Models/Booking.php

namespace App\Models;

use Database;
use PDO;
use Exception;

class Booking {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getSeatsWithAvailability($showtimeId) {
        // First get showtime details to find the hall
        $stmt = $this->db->prepare("SELECT hall_id FROM showtimes WHERE id = :id");
        $stmt->execute(['id' => $showtimeId]);
        $showtime = $stmt->fetch();

        if (!$showtime) return [];

        $hallId = $showtime['hall_id'];

        // Get all seats for this hall
        $stmt = $this->db->prepare("SELECT * FROM seats WHERE hall_id = :hall_id");
        $stmt->execute(['hall_id' => $hallId]);
        $seats = $stmt->fetchAll();

        // Get reserved seats for this showtime
        $stmt = $this->db->prepare("
            SELECT seat_id FROM reserved_seats rs
            JOIN reservations r ON rs.reservation_id = r.id
            WHERE r.showtime_id = :showtime_id AND r.status != 'cancelled'
        ");
        $stmt->execute(['showtime_id' => $showtimeId]);
        $reservedSeatIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Mark availability
        foreach ($seats as &$seat) {
            $seat['is_available'] = !in_array($seat['id'], $reservedSeatIds);
        }

        return $seats;
    }

    public function validatePromoCode($code) {
        $stmt = $this->db->prepare("
            SELECT * FROM promo_codes 
            WHERE code = :code AND is_active = 1 AND (valid_until IS NULL OR valid_until > NOW())
        ");
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    public function createReservation($data) {
        try {
            $this->db->beginTransaction();

            $resUid = bin2hex(random_bytes(4)); // Short readable UID

            // 1. Create the reservation
            $stmt = $this->db->prepare("
                INSERT INTO reservations (uid, showtime_id, customer_name, customer_email, total_price, promo_code_id, payment_method, status)
                VALUES (:uid, :showtime_id, :customer_name, :customer_email, :total_price, :promo_code_id, :payment_method, 'confirmed')
            ");
            $stmt->execute([
                'uid' => $resUid,
                'showtime_id' => $data['showtime_id'],
                'customer_name' => $data['customer_name'] ?? 'Guest',
                'customer_email' => $data['customer_email'],
                'total_price' => $data['total_price'],
                'promo_code_id' => $data['promo_code_id'] ?? null,
                'payment_method' => $data['payment_method']
            ]);
            $reservationId = $this->db->lastInsertId();

            // 2. Insert reserved seats
            $stmt = $this->db->prepare("
                INSERT INTO reserved_seats (uid, reservation_id, seat_id, ticket_type, price)
                VALUES (:uid, :reservation_id, :seat_id, :ticket_type, :price)
            ");

            foreach ($data['seats'] as $seat) {
                // Double check availability before inserting (Race condition prevention)
                $checkStmt = $this->db->prepare("
                    SELECT 1 FROM reserved_seats rs
                    JOIN reservations r ON rs.reservation_id = r.id
                    WHERE r.showtime_id = :showtime_id AND rs.seat_id = :seat_id AND r.status != 'cancelled'
                ");
                $checkStmt->execute([
                    'showtime_id' => $data['showtime_id'],
                    'seat_id' => $seat['id']
                ]);
                
                if ($checkStmt->fetch()) {
                    throw new Exception("Seat " . $seat['id'] . " is already taken.");
                }

                $stmt->execute([
                    'uid' => bin2hex(random_bytes(8)),
                    'reservation_id' => $reservationId,
                    'seat_id' => $seat['id'],
                    'ticket_type' => $seat['type'] ?? 'standard',
                    'price' => $seat['price']
                ]);
            }

            $this->db->commit();
            return $reservationId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getAll() {
        $stmt = $this->db->query("
            SELECT r.*, s.start_time, m.title as movie_title, h.name as hall_name 
            FROM reservations r
            JOIN showtimes s ON r.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN halls h ON s.hall_id = h.id
            ORDER BY r.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT r.*, s.start_time, m.title as movie_title, h.name as hall_name 
            FROM reservations r
            JOIN showtimes s ON r.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN halls h ON s.hall_id = h.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getSeatsForReservation($reservation_id) {
        $stmt = $this->db->prepare("
            SELECT rs.*, s.row_num, s.seat_num, s.type as seat_type 
            FROM reserved_seats rs
            JOIN seats s ON rs.seat_id = s.id
            WHERE rs.reservation_id = :reservation_id
        ");
        $stmt->execute(['reservation_id' => $reservation_id]);
        return $stmt->fetchAll();
    }

    public function updateReservation($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE reservations SET 
                customer_name = :customer_name, 
                customer_email = :customer_email, 
                payment_method = :payment_method, 
                status = :status,
                total_price = :total_price
            WHERE id = :id
        ");
        return $stmt->execute([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'payment_method' => $data['payment_method'],
            'status' => $data['status'],
            'total_price' => $data['total_price'],
            'id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM reservations WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}

