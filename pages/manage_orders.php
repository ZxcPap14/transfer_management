<?php
session_start();
require_once '../assets/php/script/connect.php';
require_once '../assets/php/script/queries.php';

$orders = getAllAccountingOrders();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление бухгалтерскими заказами</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .edit-mode input, .edit-mode textarea { width: 100%; }
    </style>
</head>
<body>
<h2>Бухгалтерские заказы</h2>
<?php include_once '../assets/php/head.php'; ?>

<h2>Добавить заказ</h2>
<form id="addOrderForm">
    <label>Номер заказа:
        <input type="text" name="order_number" required>
    </label><br>
    <label>Заказчик:
        <input type="text" name="customer">
    </label><br>
    <label>Описание:
        <textarea name="description" rows="4" cols="50"></textarea>
    </label><br>
    <button type="submit">Добавить</button>
</form>

<h2>Список заказов</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Номер заказа</th>
        <th>Описание</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($orders as $order): ?>
        <tr id="orderRow-<?= $order['id'] ?>">
            <td><?= $order['id'] ?></td>
            <td class="order_number"><?= htmlspecialchars($order['order_number']) ?></td>
            <td class="description"><?= htmlspecialchars($order['description']) ?></td>
            <td>
                <button onclick="editOrder(<?= $order['id'] ?>)">Изменить</button>
                <button onclick="deleteOrder(<?= $order['id'] ?>)">Удалить</button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
document.getElementById('addOrderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    const res = await fetch('../assets/php/script/add_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });

    const result = await res.json();
    alert(result.message);
    if (result.status === 'success') location.reload();
});

async function deleteOrder(id) {
    if (!confirm('Удалить заказ?')) return;

    const res = await fetch(`../assets/php/script/delete_order.php?id=${id}`, { method: 'GET' });
    const result = await res.json();
    alert(result.message);
    if (result.status === 'success') location.reload();
}
let zxc ="";
function editOrder(id) {
    const row = document.getElementById(`orderRow-${id}`);
    const orderNumber = row.querySelector('.order_number').textContent.trim();
    zxc = orderNumber;
    console.log(zxc);
    const description = row.querySelector('.description').textContent.trim();
    row.querySelector('.order_number').innerHTML = `<input type="text" value="${orderNumber}">`;
    row.querySelector('.description').innerHTML = `<textarea rows="2">${description}</textarea>`;
    
    const actionsCell = row.querySelector('td:last-child');
    actionsCell.innerHTML = `
        <button onclick="saveOrder(${id})">Сохранить</button>
        <button onclick="location.reload()">Отмена</button>
    `;
}

async function saveOrder(id) {
    const row = document.getElementById(`orderRow-${id}`);
    const orderNumber = row.querySelector('.order_number input').value.trim();
    const description = row.querySelector('.description textarea').value.trim();
    const data = { id, order_number: orderNumber, description , zxc};

    const res = await fetch('../assets/php/script/update_order.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });

    const result = await res.json();
    alert(result.message);
    if (result.status === 'success') location.reload();
}
</script>
</body>
</html>
