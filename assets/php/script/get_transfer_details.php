<?php
include_once 'connect.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID не указан']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            t.*, 
            p.name AS part_name,
            d1.name AS from_department,
            d2.name AS to_department,
            u.full_name AS user_name
        FROM transfers t
        JOIN parts p ON t.part_id = p.id
        JOIN departments d1 ON t.from_department_id = d1.id
        JOIN departments d2 ON t.to_department_id = d2.id
        JOIN users u ON t.user_id = u.id
        WHERE t.id = ?
    ");
    $stmt->execute([$id]);
    $transfer = $stmt->fetch();

    if ($transfer) {
        echo json_encode(['status' => 'success', 'data' => $transfer]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Трансфер не найден']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка: ' . $e->getMessage()]);
}
