<?php
session_start();
include_once 'connect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID не передан']);
    exit;
}

$id = $data['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM transfers WHERE id = ?");
    $stmt->execute([$id]);

    // Логирование
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId) {
        $logStmt = $pdo->prepare("INSERT INTO transfer_logs (transfer_id, user_id, action, timestamp) VALUES (?, ?, ?, NOW())");
        $logStmt->execute([$id, $userId, "Удален трансфер"]);
    }

    echo json_encode(['status' => 'success', 'message' => 'Трансфер удален']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()]);
}
