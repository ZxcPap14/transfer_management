<?php
include_once 'connect.php';

$productId = $_GET['product_id'] ?? null;

if ($productId) {
    $stmt = $pdo->prepare("
        SELECT s.name AS stage, p.name AS name, sp.quantity_required AS quantity
        FROM stage_parts sp
        JOIN stages s ON sp.stage_id = s.id
        JOIN parts p ON sp.part_id = p.id
        WHERE s.product_id = ?
    ");
    $stmt->execute([$productId]);

    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($details) {
        echo json_encode(['status' => 'success', 'details' => $details]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Нет данных для выбранного изделия']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Не указано изделие']);
}
