<?php
require_once 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['id'], $data['name'], $data['nomenclature_number'])) {
    echo json_encode(['status' => 'error', 'message' => 'Неверный запрос']);
    exit;
}

$id = $data['id'];
$name = $data['name'];
$nomenclature_number = $data['nomenclature_number'];

$stmt = $pdo->prepare("UPDATE parts SET name = ?, nomenclature_number = ? WHERE id = ?");
$stmt->execute([$name, $nomenclature_number, $id]);

echo json_encode(['status' => 'success', 'message' => 'Деталь обновлена']);
