<?php
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$products = getProducts();
$selectedProductId = $_GET['product_id'] ?? null;
$stages = $selectedProductId ? getStagesByProduct($selectedProductId) : [];
$parts = getParts();

// Обработка добавления детали
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['part_name'], $_POST['nomenclature_number'])) {
    $name = $_POST['part_name'];
    $nomenclatureNumber = $_POST['nomenclature_number'];
    
    // Добавляем деталь
    $partId = addPart($name, $nomenclatureNumber);
    header("Location: assign_parts.php?product_id={$selectedProductId}");
    exit;
}

// Обработка добавления этапа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stage_name']) && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $stageName = $_POST['stage_name'];
    
    // Добавляем этап
    $stageId = addStage($productId, $stageName);
    header("Location: assign_parts.php?product_id={$productId}");
    exit;
}

// Обработка привязки детали к этапу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stage_id'], $_POST['part_id'], $_POST['quantity'])) {
    $stageId = $_POST['stage_id'];
    $partId = $_POST['part_id'];
    $quantity = $_POST['quantity'];
    
    // Привязываем деталь к этапу
    addStagePart($stageId, $partId, $quantity);
    header("Location: assign_parts.php?product_id={$selectedProductId}");
    exit;
}

// Обработка удаления привязки детали от этапа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_stage_part_id'])) {
    deleteStagePart($_POST['delete_stage_part_id']);
    header("Location: assign_parts.php?product_id={$selectedProductId}");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Привязка деталей к этапам</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include_once '../assets/php/head.php'; ?>

<div class="container">
    <h1>Привязка деталей к этапам изделия</h1>

    <!-- Выбор изделия -->
    <form method="GET" class="form-section">
        <label>Выберите изделие:</label>
        <select name="product_id" onchange="this.form.submit()">
            <option value="">-- Выберите изделие --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['id'] ?>" <?= ($product['id'] == $selectedProductId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($product['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($selectedProductId): ?>
    <!-- Добавление нового этапа -->
    <form method="POST" class="form-section">
        <input type="hidden" name="product_id" value="<?= $selectedProductId ?>">
        <label>Новый этап сборки:</label>
        <input type="text" name="stage_name" required>
        <button type="submit">Добавить этап</button>
    </form>

    <!-- Добавление новой детали -->
    <form method="POST" class="form-section">
        <label>Новая деталь:</label>
        <input type="text" name="part_name" placeholder="Название детали" required>
        <input type="text" name="nomenclature_number" placeholder="Номенклатурный номер" required>
        <button type="submit">Добавить деталь</button>
    </form>

    <!-- Привязка детали к этапу -->
    <form method="POST" class="form-section">
        <input type="hidden" name="product_id" value="<?= $selectedProductId ?>">
        <label>Выберите этап:</label>
        <select name="stage_id" required>
            <?php foreach ($stages as $stage): ?>
                <option value="<?= $stage['id'] ?>"><?= htmlspecialchars($stage['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Выберите деталь:</label>
        <select name="part_id" required>
            <?php foreach ($parts as $part): ?>
                <option value="<?= $part['id'] ?>"><?= htmlspecialchars($part['name']) ?> (<?= $part['nomenclature_number'] ?>)</option>
            <?php endforeach; ?>
        </select>

        <label>Количество:</label>
        <input type="number" name="quantity" min="1" required>

        <button type="submit">Привязать деталь</button>
    </form>

    <!-- Вывод всех деталей по этапам -->
    <h3>Список этапов и деталей</h3>
    <?php foreach ($stages as $stage): ?>
        <h4>Этап: <?= htmlspecialchars($stage['name']) ?></h4>
        <table>
            <tr>
                <th>Деталь</th>
                <th>Номенклатурный номер</th>
                <th>Количество</th>
                <th>Действия</th>
            </tr>
            <?php $stageParts = getStageParts($stage['id']); ?>
            <?php foreach ($stageParts as $sp): ?>
                <tr>
                    <td><?= htmlspecialchars($sp['part_name']) ?></td>
                    <td><?= $sp['nomenclature_number'] ?></td>
                    <td><?= $sp['quantity_required'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                        <input  type="hidden" name="delete_stage_part_id" value="<?= $sp['stage_part_id'] ?>">
                            <button type="submit" onclick="return confirm('Удалить эту запись?')">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
