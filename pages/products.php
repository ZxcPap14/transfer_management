<?php
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
// Обработка добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO products (name) VALUES (?)");
    $stmt->execute([$name]);
    header("Location: products.php");
    exit;
}

// Обработка удаления
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php");
    exit;
}

// Обработка редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $stmt = $pdo->prepare("UPDATE products SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    header("Location: products.php");
    exit;
}

// Получение всех изделий
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление изделиями</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include_once '../assets/php/head.php'; ?>
    <h1>Изделия</h1>

    <form method="POST">
        <input type="text" name="name" placeholder="Название изделия" required>
        <button type="submit" name="add_product">Добавить</button>
    </form>

    <h2>Список изделий</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <form method="POST">
                <td><?= $product['id'] ?></td>
                <td>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                </td>
                <td>
                    <button type="submit" name="edit_product">Сохранить</button>
                    <a href="?delete=<?= $product['id'] ?>" onclick="return confirm('Удалить изделие?')">Удалить</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
