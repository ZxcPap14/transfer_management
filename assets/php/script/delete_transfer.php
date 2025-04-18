<?php
session_start();
require_once 'queries.php';

$data = json_decode(file_get_contents("php://input"), true);
$transferId = $data['id'] ?? null;

if (!$transferId) {
    echo json_encode(["status" => "error", "message" => "ID не передан"]);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM transfers WHERE id = :id");
    $stmt->execute(['id' => $transferId]);
    echo json_encode(["status" => "success", "message" => "Трансфер удалён"]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Ошибка при удалении: " . $e->getMessage()]);
}
    