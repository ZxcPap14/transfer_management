<?php
include_once 'connect.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';
$id = $_POST['id'] ?? null;
$name = trim($_POST['name'] ?? '');
$department_number = trim($_POST['code'] ?? '');
if ($action === 'add') {
    if (empty($name) || empty($department_number)) {
        echo json_encode(['status' => 'error', 'message' => 'Название и номер департамента не могут быть пустыми']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO departments (name, department_number) VALUES (?, ?)");
    $stmt->execute([$name, $department_number]);
    echo json_encode(['status' => 'success', 'message' => 'Департамент добавлен']);
    exit;
}

if ($action === 'edit') {
    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Название не может быть пустым']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE departments SET name = ? ,  department_number = ? WHERE id = ?");
    $stmt->execute([$name, $department_number, $id]);
    echo json_encode(['status' => 'success', 'message' => 'Название обновлено']);
    exit;
}

if ($action === 'delete') {
    // Проверка на прикрепленных пользователей
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE department_id = ?");
    $stmt->execute([$id]);
    $userCount = $stmt->fetchColumn();

    if ($userCount > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Нельзя удалить департамент, к которому прикреплены пользователи']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['status' => 'success', 'message' => 'Департамент удалён']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Некорректный запрос']);
