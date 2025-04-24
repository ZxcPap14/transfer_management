<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Обработка добавления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $nomenclature_number = $_POST['nomenclature_number'];
    $designation = $_POST['designation'];
    $stmt = $pdo->prepare("INSERT INTO products (name, nomenclature_number, designation) VALUES (?, ?, ?)");
    $stmt->execute([$name, $nomenclature_number, $designation]);
    header("Location: products.php");
    exit;
}

// Обработка удаления
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_plan WHERE product_id = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "<script>alert('Невозможно удалить товар, так как для него существует план.');</script>";
        header("Refresh: 0; url=products.php"); 
        exit;
    } else {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: products.php");
        exit;
    }
}

// Обработка редактирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = $_POST['name'];
    $nomenclature_number = $_POST['nomenclature_number'];
    $designation = $_POST['designation'];
    $stmt = $pdo->prepare("UPDATE products SET name = ?, nomenclature_number = ?, designation = ? WHERE id = ?");
    $stmt->execute([$name, $nomenclature_number, $designation, $id]);
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
    <h2>Изделия</h2>
    <?php include_once '../assets/php/head.php'; ?>

    <h1>Добавить изделие</h1>
    <form method="POST">
        <input type="text" name="name" placeholder="Название изделия" required>
        <input type="text" name="nomenclature_number" placeholder="Номенклатурный номер" required>
        <input type="text" name="designation" placeholder="Обозначение" required>
        <button type="submit" name="add_product">Добавить</button>
    </form>

    <h1>Список изделий</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Номенклатурный номер</th>
            <th>Обозначение</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <form method="POST">
                <td><?= $product['id'] ?></td>
                <td>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                </td>
                <td>
                    <input type="text" name="nomenclature_number" value="<?= htmlspecialchars($product['nomenclature_number']) ?>">
                </td>
                <td>
                    <input type="text" name="designation" value="<?= htmlspecialchars($product['designation']) ?>">
                </td>
                <td>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" name="edit_product">Сохранить</button>
                    <a href="?delete=<?= $product['id'] ?>" onclick="return confirm('Удалить изделие?')">Удалить</a>
                </td>
            </form>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>