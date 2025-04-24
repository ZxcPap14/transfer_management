<?php
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Некорректный запрос']);
    exit;
}

$id = $data['id'];

$stmt = $pdo->prepare("DELETE FROM parts WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['status' => 'success', 'message' => 'Деталь удалена']);
