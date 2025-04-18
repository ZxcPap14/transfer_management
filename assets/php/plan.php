<?php
// Подключение к базе данных
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';

// Получаем данные из таблицы product_plan
$query = "SELECT products.name AS product_name, product_plan.year, product_plan.quantity 
          FROM product_plan 
          JOIN products ON product_plan.product_id = products.id
          ORDER BY product_plan.year";
$stmt = $pdo->prepare($query);
$stmt->execute();
$plan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получаем список всех товаров для формы
$query_products = "SELECT id, name FROM products";
$stmt_products = $pdo->prepare($query_products);
$stmt_products->execute();
$products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Существующий план</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Название изделия</th>
                    <th>Год</th>
                    <th>Количество</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plan as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                        <td><?= htmlspecialchars($row['year']); ?></td>
                        <td><?= htmlspecialchars($row['quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>