<?php
require_once 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$order_number = $data['order_number'];
$description = $data['description'];
$zxc = $data['zxc'];
$stmt = $pdo->prepare("UPDATE accounting_orders SET order_number = ?, description = ? WHERE id = ?");
$stmt2 = $pdo->prepare("UPDATE product_plan SET order_number = ? WHERE order_number = ?");
if ($stmt->execute([$order_number, $description, $id]) && $stmt2->execute([$order_number, $zxc])){
    echo json_encode(['status' => 'success', 'message' => 'Заказ обновлен']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка обновления заказа']);
}
