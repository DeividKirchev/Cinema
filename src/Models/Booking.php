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

            // 1. Create the reservation
            $stmt = $this->db->prepare("
                INSERT INTO reservations (showtime_id, customer_email, total_price, promo_code_id, payment_method, status)
                VALUES (:showtime_id, :customer_email, :total_price, :promo_code_id, :payment_method, 'confirmed')
            ");
            $stmt->execute([
                'showtime_id' => $data['showtime_id'],
                'customer_email' => $data['customer_email'],
                'total_price' => $data['total_price'],
                'promo_code_id' => $data['promo_code_id'] ?? null,
                'payment_method' => $data['payment_method']
            ]);
            $reservationId = $this->db->lastInsertId();

            // 2. Insert reserved seats
            $stmt = $this->db->prepare("
                INSERT INTO reserved_seats (reservation_id, seat_id, ticket_type, price)
                VALUES (:reservation_id, :seat_id, :ticket_type, :price)
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
                    'reservation_id' => $reservationId,
                    'seat_id' => $seat['id'],
                    'ticket_type' => $seat['type'],
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
}
