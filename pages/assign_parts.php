<?php
session_start();
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
// Обработка удаления этапа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_stage_id'])) {
    $stageId = $_POST['delete_stage_id'];
    
    // Удаляем этап
    deleteStage($stageId);
    header("Location: assign_parts.php?product_id={$selectedProductId}");
    exit;
}
// Обработка редактирования применяемости детали
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_stage_part_id'], $_POST['new_quantity'])) {
    $stagePartId = $_POST['edit_stage_part_id'];
    $newQuantity = $_POST['new_quantity'];

    // Редактируем применяемость детали
    updateStagePartQuantity($stagePartId, $newQuantity);
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
    <title>Этапы сборки</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Этапы сборки изделия</h2>
        <?php include_once '../assets/php/head.php'; ?>

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

                <label>Применяемость:</label>
                <input type="number" name="quantity" min="1" required>

                <button type="submit">Привязать деталь</button>
            </form>

            <!-- Редактирование применяемости -->
            <form method="POST" class="form-section">
                <label>Редактирование применяемости:</label>
                <select name="edit_stage_part_id" required>
                    <?php foreach ($stages as $stage): ?>
                        <optgroup label="<?= htmlspecialchars($stage['name']) ?>">
                            <?php $stageParts = getStageParts($stage['id']); ?>
                            <?php foreach ($stageParts as $sp): ?>
                                <option value="<?= $sp['stage_part_id'] ?>"><?= htmlspecialchars($sp['part_name']) ?> - Применяемость: <?= $sp['quantity_required'] ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="new_quantity" min="1" required>
                <button type="submit">Изменить применяемость</button>
            </form>

           <!-- Вывод всех этапов -->
            <h3>Список этапов</h3>
            <?php foreach ($stages as $stage): ?>
                <h4>Этап: <?= htmlspecialchars($stage['name']) ?></h4>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="delete_stage_id" value="<?= $stage['id'] ?>">
                    <button type="submit" onclick="return confirm('Удалить этот этап?')">Удалить этап</button>
                </form>
                <table>
                    <tr>
                        <th>Деталь</th>
                        <th>Номенклатурный номер</th>
                        <th>Применяемость</th>
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
                                    <input type="hidden" name="delete_stage_part_id" value="<?= $sp['stage_part_id'] ?>">
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
