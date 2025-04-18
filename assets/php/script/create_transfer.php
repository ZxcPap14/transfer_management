<?php
include_once 'connect.php';
session_start();

$part_id = $_POST['part_id'];
$quantity = $_POST['quantity'];
$from_department = $_POST['from_department']; // Извлекаем значение из скрытого поля
$to_department = $_POST['target_department'];
$order_number = $_POST['order_number'];
$dispatcher_id = $_SESSION['user_id'];

if (!$part_id || !$quantity || !$from_department || !$to_department || !$order_number) {
    echo json_encode(['status' => 'error', 'message' => 'Не все поля заполнены']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO transfers (part_id, quantity, from_department_id, to_department_id, user_id, status, order_number)
    VALUES (?, ?, ?, ?, ?, 'ожидает подтверждения', ?)
");
$stmt->execute([$part_id, $quantity, $from_department, $to_department, $dispatcher_id, $order_number]);

echo json_encode(['status' => 'success', 'message' => 'Перемещение создано']);
