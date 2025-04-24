<?php
session_start();
require_once '../assets/php/script/connect.php';
require_once '../assets/php/script/queries.php';

// Получаем данные для выпадающих списков
$products = getAllProducts();        // функция должна вернуть id, name, designation
$orders = getAllAccountingOrdersId(); // функция должна вернуть order_number
$plans = getAllProductPlans();      // таблица с текущими планами
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление планом</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>План производства</h2>
    <?php
                include_once '../assets/php/head.php';
        ?>
    <h1>Добавить план</h1>
    <form id="addPlanForm">
        <label>Изделие:
            <select name="product_id" required>
                <option value="">-- Выберите изделие --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>">
                        <?= htmlspecialchars($product['name']) ?> (<?= htmlspecialchars($product['designation']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <label>Год:
            <input list="year-options" name="year" id="yearInput" required pattern="\d{4}" maxlength="4">
            <datalist id="year-options">
                <?php for ($y = 2024; $y <= 2030; $y++): ?>
                    <option value="<?= $y ?>">
                <?php endfor; ?>
            </datalist>
        </label><br>

        <label>Количество:
            <input type="number" name="quantity" min="1" required>
        </label><br>

        <label>Бухгалтерский заказ:
            <select name="order_number" required>
                <option value="">-- Выберите заказ --</option>
                <?php foreach ($orders as $order): ?>
                    <option value="<?= htmlspecialchars($order['order_number']) ?>">
                        <?= htmlspecialchars($order['order_number']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label><br>

        <button type="submit">Добавить</button>
    </form>

    <h1>Текущие планы</h1>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Изделие</th>
            <th>Год</th>
            <th>Количество</th>
            <th>Бух. заказ</th>
        </tr>
        <?php foreach ($plans as $plan): ?>
            <tr>
                <td><?= $plan['id'] ?></td>
                <td><?= htmlspecialchars($plan['product_name']) ?></td>
                <td><?= $plan['year'] ?></td>
                <td><?= $plan['quantity'] ?></td>
                <td><?= $plan['order_number'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        document.getElementById('addPlanForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const yearValue = document.getElementById('yearInput').value;
            if (!/^\d{4}$/.test(yearValue) || yearValue < 2000 || yearValue > 2100) {
                alert('Введите корректный год от 2000 до 2100');
                return;
            }

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            console.log('Данные для отправки:', data); // отладка

            const res = await fetch('../assets/php/script/add_plan.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });

            const result = await res.json();
            alert(result.message);
            if (result.status === 'success') location.reload();
        });
    </script>
</body>
</html>
