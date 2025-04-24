<?php
require_once 'connect.php';
require_once 'queries.php';

$data = json_decode(file_get_contents("php://input"), true);

// Проверка обязательных полей
if (
    !isset($data['product_id'], $data['year'], $data['quantity'], $data['order_number']) ||
    empty($data['product_id']) || empty($data['year']) || empty($data['quantity']) || empty($data['order_number'])
) {
    echo json_encode(['status' => 'error', 'message' => 'Заполните все поля.']);
    exit;
}

$product_id = intval($data['product_id']);
$year = intval($data['year']);
$quantity = intval($data['quantity']);
$order_number = trim($data['order_number']);

if (addProductPlan($product_id, $year, $quantity, $order_number)) {
    echo json_encode(['status' => 'success', 'message' => 'План успешно добавлен.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка при добавлении плана.']);
}
