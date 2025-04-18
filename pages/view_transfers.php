<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';

$role = $_SESSION['role'];
echo $role;
$userId = $_SESSION['user_id'];
$user = getUserById($userId);
$departmentId = $user['department_id'];

$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

$sentTransfers = getTransfersByFromDepartment($departmentId, $order);
$receivedTransfers = getTransfersByToDepartment($departmentId, $order);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Просмотр перемещений</title>
    <link rel="stylesheet" href="..\assets\css\style.css">
</head>
<body>
<?php
        include_once '..\assets\php\head.php';
        ?>
    <h2>Отправленные заказы</h2>
    <a href="?order=asc">Сортировать по возрастанию даты</a> | <a href="?order=desc">Сортировать по убыванию даты</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Деталь</th>
                <th>Количество</th>
                <th>Получатель</th>
                <th>Бух. заказ</th>
                <th>Статус</th>
                <th>Дата</th>

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
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Полученные заказы</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Деталь</th>
                <th>Количество</th>
                <th>Отправитель</th>
                <th>Бух. заказ</th>
                <th>Статус</th>
                <th>Дата</th>
                <?php if ($role === 'admin' || $role === 'shift_manager'): ?>
                    <th>Действия</th>
                <?php endif; ?>
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

    <script>
    function updateStatus(id, newStatus) {
        fetch('../assets/php/script/update_transfer_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ id: id, status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status === 'success') location.reload();
        });
    }

    function deleteTransfer(id) {
        if (confirm('Вы уверены, что хотите удалить этот трансфер?')) {
            fetch('../assets/php/script/delete_transfer.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') location.reload();
            });
        }
    }
    </script>
</body>
</html>
