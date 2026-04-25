<?php
// src/Controllers/BookingController.php

namespace App\Controllers;

use App\Models\Booking;
use Exception;

class BookingController {
    private $bookingModel;

    public function __construct() {
        $this->bookingModel = new Booking();
    }

    public function seats() {
        $showtimeId = $_GET['showtime_id'] ?? null;

        if (!$showtimeId) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Showtime ID is required']);
            return;
        }

        $seats = $this->bookingModel->getSeatsWithAvailability($showtimeId);
        
        header('Content-Type: application/json');
        echo json_encode($seats);
    }

    public function validatePromo() {
        // Read JSON body
        $input = json_decode(file_get_contents('php://input'), true);
        $code = $input['code'] ?? null;

        if (!$code) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Promo code is required']);
            return;
        }

        $promo = $this->bookingModel->validatePromoCode($code);

        header('Content-Type: application/json');
        if ($promo) {
            echo json_encode(['valid' => true, 'discount' => $promo['discount_percent']]);
        } else {
            echo json_encode(['valid' => false, 'error' => 'Invalid or expired code']);
        }
    }

    public function reserve() {
        // Read JSON body
        $data = json_decode(file_get_contents('php://input'), true);

        // Simple validation
        if (empty($data['showtime_id']) || empty($data['customer_email']) || empty($data['seats'])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['error' => 'Missing required booking data']);
            return;
        }

        try {
            $reservationId = $this->bookingModel->createReservation($data);

            // Simulate email confirmation
            $this->simulateEmail($data['customer_email'], $reservationId);

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'reservation_id' => $reservationId,
                'message' => 'Reservation confirmed. Confirmation email sent.'
            ]);
        } catch (Exception $e) {
            header("HTTP/1.1 409 Conflict");
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function simulateEmail($email, $reservationId) {
        // In a real app, this would use mail() or a library like PHPMailer
        $logMessage = "[" . date('Y-m-d H:i:s') . "] Confirmation email sent to $email for reservation #$reservationId" . PHP_EOL;
        file_put_contents(__DIR__ . '/../../storage/logs/emails.log', $logMessage, FILE_APPEND);
    }
}
