<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
$selectedProduct = isset($_GET['product_id']) ? $_GET['product_id'] : null;
$data = $selectedProduct ? getProductGraph($selectedProduct) : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>График комплектующих</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include_once '../assets/php/head.php'; ?>
    <h1>График комплектующих по этапам</h1>

    <form method="get">
        <label>Выберите изделие:
            <select name="product_id" onchange="this.form.submit()">
                <option value="">-- Выберите --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>" <?= ($product['id'] == $selectedProduct ? 'selected' : '') ?>>
                        <?= htmlspecialchars($product['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </form>

    <?php if ($selectedProduct && $data): ?>
        <h2>Детали для изделия</h2>
        <table border="1" cellpadding="5">
            <tr>
                <th>Этап</th>
                <th>Деталь</th>
                <th>На 1 изделие</th>
                <th>Количество изделий</th>
                <th>Всего нужно</th>
            </tr>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['stage_name']) ?></td>
                    <td><?= htmlspecialchars($row['part_name']) ?></td>
                    <td><?= $row['quantity_required'] ?></td>
                    <td><?= $row['product_quantity'] ?></td>
                    <td><?= $row['total_required'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($selectedProduct): ?>
        <p>Нет данных по выбранному изделию.</p>
    <?php endif; ?>
</body>
</html>
