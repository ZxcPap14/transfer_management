<?php
session_start();
include_once 'connect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Недостаточно данных']);
    exit;
}

$id = $data['id'];
$newStatus = $data['status'];

try {
    $stmt = $pdo->prepare("UPDATE transfers SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$newStatus, $id]);

    // Логирование
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $logStmt = $pdo->prepare("INSERT INTO transfer_logs (transfer_id, user_id, action, timestamp) VALUES (?, ?, ?, NOW())");
        $logStmt->execute([$id, $userId, "Статус обновлен на $newStatus"]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Статус обновлен']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()]);
}
