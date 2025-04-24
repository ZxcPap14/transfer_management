<?php
require_once 'connect.php';
require_once 'queries.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['order_number']) || empty($data['order_number'])) {
    echo json_encode(['status' => 'error', 'message' => 'Укажите номер заказа.']);
    exit;
}

$order_number = trim($data['order_number']);
$customer = trim($data['customer']);
$description = trim($data['description'] ?? '');

if (addAccountingOrder($order_number, $description,$customer) ) {
    echo json_encode(['status' => 'success', 'message' => 'Заказ добавлен.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка при добавлении. Возможно, заказ уже существует.']);
}
