<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$code = $data['promo_code'] ?? '';

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Моля, въведете промо код.']);
    exit;
}

$db = Database::getInstance();
$stmt = $db->prepare("SELECT id, discount_percent FROM promo_codes WHERE code = :code AND valid_until >= CURDATE()");
$stmt->execute(['code' => $code]);
$promo = $stmt->fetch();

if ($promo) {
    echo json_encode([
        'success' => true, 
        'discount_percent' => $promo['discount_percent'],
        'promo_id' => $promo['id'],
        'message' => 'Промо кодът е приложен успешно!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Невалиден или изтекъл промо код.']);
}
