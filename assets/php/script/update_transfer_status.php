<?php
session_start();
include_once 'connect.php';
include_once 'queries.php';
$data = json_decode(file_get_contents("php://input"), true);
$transferId = $data['id'] ?? null;
$newStatus = $data['status'] ?? null;

if (!$transferId || !$newStatus) {
    echo json_encode(["status" => "error", "message" => "Недостаточно данных"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE transfers SET status = :status WHERE id = :id");
    $stmt->execute([
        'status' => $newStatus,
        'id' => $transferId
    ]);
    echo json_encode(["status" => "success", "message" => "Статус обновлен"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Ошибка базы данных: " . $e->getMessage()]);
}
