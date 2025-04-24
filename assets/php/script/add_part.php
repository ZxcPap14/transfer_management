<?php
require_once 'connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name'], $data['nomenclature_number'])) {
    echo json_encode(['status' => 'error', 'message' => 'Неверный запрос']);
    exit;
}

$name = trim($data['name']);
$number = trim($data['nomenclature_number']);

if ($name === '' || $number === '') {
    echo json_encode(['status' => 'error', 'message' => 'Заполните все поля']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO parts (name, nomenclature_number ) VALUES (?, ?)");
    $stmt->execute([$name, $number]);
    echo json_encode(['status' => 'success', 'message' => 'Деталь добавлена']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка при добавлении']);
}
