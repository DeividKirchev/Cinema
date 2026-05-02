<?php
// src/Controllers/NewsletterController.php

namespace App\Controllers;

use App\Models\Newsletter;

class NewsletterController {
    private $newsletterModel;

    public function __construct() {
        $this->newsletterModel = new Newsletter();
    }

    public function subscribe() {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(['success' => false, 'error' => 'Invalid email address']);
            return;
        }

        if ($this->newsletterModel->subscribe($email)) {
            echo json_encode(['success' => true, 'message' => 'Subscribed successfully']);
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            echo json_encode(['success' => false, 'error' => 'Could not subscribe']);
        }
    }
}
