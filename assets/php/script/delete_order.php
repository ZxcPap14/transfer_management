<?php
require_once 'connect.php';
require_once 'queries.php';

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID не передан.']);
    exit;
}

$id = intval($_GET['id']);

if (deleteAccountingOrder($id)) {
    echo json_encode(['status' => 'success', 'message' => 'Заказ удалён.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка при удалении.']);
}
