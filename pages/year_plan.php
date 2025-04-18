<?php
// Подключение к базе данных
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Обработка формы для добавления плана
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_plan'])) {
    $product_id = $_POST['product_id'];
    $year = $_POST['year'];
    $quantity = $_POST['quantity'];

    // Вставляем данные в таблицу product_plan
    $stmt = $pdo->prepare("INSERT INTO product_plan (product_id, year, quantity) VALUES (:product_id, :year, :quantity)");
    $stmt->execute(['product_id' => $product_id, 'year' => $year, 'quantity' => $quantity]);

    // Перенаправляем, чтобы обновить страницу
    header('Location: year_plan.php');
    exit;
}

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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>План на год</title>
    <link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<?php include_once '../assets/php/head.php'; ?>
    <div class="container">
        <h1>План на год</h1>

        <!-- Форма для добавления нового плана -->
        <h2>Добавить план</h2>
        <form action="year_plan.php" method="POST">
            <label for="product_id">Товар:</label>
            <select name="product_id" id="product_id" required>
                <option value="">Выберите товар</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="year">Год:</label>
            <input type="number" name="year" id="year" required><br><br>

            <label for="quantity">Количество:</label>
            <input type="number" name="quantity" id="quantity" required><br><br>

            <button type="submit" name="add_plan">Добавить план</button>
        </form>

        <hr>

        <!-- Таблица с планом -->
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
        </table>
    </div>
</body>
</html>
