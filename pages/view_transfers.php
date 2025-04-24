<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$departments = getDepartmentsByUserId($userId);
$departmentId = $departments[0]['id'] ?? null;

$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$statusFilter = $_GET['status'] ?? null;
$orderFilter = $_GET['order_filter'] ?? null;

$sentTransfers = getTransfersByFromDepartment2($departmentId, $order, $statusFilter, $orderFilter);
$receivedTransfers = getTransfersByToDepartment2($departmentId, $order, $statusFilter, $orderFilter);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр перемещений</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Трансфер деталей</h2>
    <?php include_once '../assets/php/head.php'; ?>

    <form method="GET">
        <label>Статус:
            <select name="status">
                <option value="">Все</option>
                <option value="ожидает подтверждения" <?= $statusFilter == 'ожидает подтверждения' ? 'selected' : '' ?>>Ожидает подтверждения</option>
                <option value="отправлено" <?= $statusFilter == 'отправлено' ? 'selected' : '' ?>>Отправлено</option>
                <option value="подтверждено" <?= $statusFilter == 'подтверждено' ? 'selected' : '' ?>>Подтверждено</option>
            </select>
        </label>
        <label>Бух. заказ:
            <input type="text" name="order_filter" value="<?= htmlspecialchars($orderFilter) ?>">
        </label>
        <button type="submit">Фильтровать</button>
    </form>

    <a href="?order=asc">Сортировать по возрастанию даты</a> | <a href="?order=desc">Сортировать по убыванию даты</a>
    <a href="../assets/php/script/export_transfers.php?order=<?= $order ?>&status=<?= $statusFilter ?>&order_filter=<?= $orderFilter ?>">Экспорт в Excel</a>

    <h1>Отправленные заказы</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Деталь</th><th>Количество</th><th>Получатель</th><th>Бух. заказ</th><th>Статус</th><th>Дата</th><th>Детали</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($sentTransfers as $transfer): ?>
            <tr>
                <td><?= $transfer['id'] ?></td>
                <td><?= htmlspecialchars($transfer['part_name']) ?></td>
                <td><?= $transfer['quantity'] ?></td>
                <td><?= htmlspecialchars($transfer['to_department_name']) ?></td>
                <td><?= htmlspecialchars($transfer['order_number']) ?></td>
                <td><?= $transfer['status'] ?></td>
                <td><?= $transfer['created_at'] ?></td>
                <td><button onclick="showDetails(<?= $transfer['id'] ?>)">Детали</button></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h1>Полученные заказы</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Деталь</th><th>Количество</th><th>Отправитель</th><th>Бух. заказ</th><th>Статус</th><th>Дата</th><th>Детали</th>
                <?php if ($role === 'admin' || $role === 'shift_manager') echo '<th>Действия</th>'; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($receivedTransfers as $transfer): ?>
            <tr>
                <td><?= $transfer['id'] ?></td>
                <td><?= htmlspecialchars($transfer['part_name']) ?></td>
                <td><?= $transfer['quantity'] ?></td>
                <td><?= htmlspecialchars($transfer['from_department_name']) ?></td>
                <td><?= htmlspecialchars($transfer['order_number']) ?></td>
                <td><?= $transfer['status'] ?></td>
                <td><?= $transfer['created_at'] ?></td>
                <td><button onclick="showDetails(<?= $transfer['id'] ?>)">Детали</button></td>
                <?php if ($role === 'admin' || $role === 'shift_manager'): ?>
                <td>
                    <?php if ($role === 'shift_manager' && $transfer['status'] === 'ожидает подтверждения'): ?>
                        <button onclick="updateStatus(<?= $transfer['id'] ?>, 'отправлено')">Отметить как отправлено</button>
                    <?php endif; ?>
                    <?php if ($role === 'admin'): ?>
                        <?php if ($transfer['status'] !== 'подтверждено'): ?>
                            <button onclick="updateStatus(<?= $transfer['id'] ?>, 'подтверждено')">Подтвердить</button>
                        <?php endif; ?>
                        <button onclick="deleteTransfer(<?= $transfer['id'] ?>)">Удалить</button>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <script src="../assets/js/transfer.js"></script>
</body>
</html>
