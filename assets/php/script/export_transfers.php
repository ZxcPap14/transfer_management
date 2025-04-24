<?php
session_start();
include_once 'connect.php';
include_once 'queries.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=transfers_export.csv');

// BOM для Excel
echo "\xEF\xBB\xBF";

// Получение фильтров
$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$statusFilter = $_GET['status'] ?? '';
$orderFilter = $_GET['order_number'] ?? '';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo "Нет доступа"; exit;
}

$departments = getDepartmentsByUserId($userId);
$departmentId = $departments[0]['id'] ?? null;

$sentTransfers = getTransfersByFromDepartment2($departmentId, $order, $statusFilter, $orderFilter);
$receivedTransfers = getTransfersByToDepartment2($departmentId, $order, $statusFilter, $orderFilter);

// Удаление дубликатов
$allTransfers = [];
$seenIds = [];
foreach (array_merge($sentTransfers, $receivedTransfers) as $row) {
    if (!in_array($row['id'], $seenIds)) {
        $seenIds[] = $row['id'];
        $allTransfers[] = $row;
    }
}

// Поток вывода
$output = fopen('php://output', 'w');

// Заголовки
fputcsv($output, ['ID', 'Деталь', 'Количество', 'Отправитель', 'Получатель', 'Бух. заказ', 'Статус', 'Дата', 'Пользователь'], ';');

// Данные
foreach ($allTransfers as $row) {
    fputcsv($output, [
        $row['id'],
        $row['part_name'] ?? '',
        $row['quantity'] ?? '',
        $row['from_department_name'] ?? '',
        $row['to_department_name'] ?? '',
        $row['order_number'] ?? '',
        $row['status'] ?? '',
        $row['created_at'] ?? '',
        $row['user_name'] ?? ''
    ], ';');
}

fclose($output);
exit;
