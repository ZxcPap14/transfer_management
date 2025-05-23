<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';

// Получаем список департаментов
$stmt = $pdo->query("SELECT * FROM departments");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление департаментами</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<h2>Департаменты</h2>
<?php include_once '../assets/php/head.php'; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Номер отдела</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($departments as $dep): ?>
    <tr id="dep-row-<?= $dep['id'] ?>">
        <td><?= $dep['id'] ?></td>

        <td>
            <span id="dep-name-<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></span>
            <input type="text" id="dep-name-input-<?= $dep['id'] ?>" value="<?= htmlspecialchars($dep['name']) ?>" style="display:none;">
        </td>

        <td>
            <span id="dep-code-<?= $dep['id'] ?>"><?= htmlspecialchars($dep['department_number']) ?></span>
            <input type="text" id="dep-code-input-<?= $dep['id'] ?>" value="<?= htmlspecialchars($dep['department_number']) ?>" style="display:none;">
        </td>

        <td>
            <button onclick="toggleEdit(<?= $dep['id'] ?>)">Редактировать</button>
            <button onclick="saveEdit(<?= $dep['id'] ?>)" style="display:none;" id="save-btn-<?= $dep['id'] ?>">Сохранить</button>
            <button onclick="deleteDepartment(<?= $dep['id'] ?>)">Удалить</button>
        </td>
    </tr>
<?php endforeach; ?>

</table>

<h3>Добавить департамент</h3>
<form id="addDepForm" onsubmit="addDepartment(event)">
    <input type="text" name="name" placeholder="Название департамента" required>
    <input type="text" name="number" placeholder="Номер отдела" required>
    <button type="submit">Добавить</button>
</form>

<script>
function toggleEdit(id) {
    const nameSpan = document.getElementById('dep-name-' + id);
    const nameInput = document.getElementById('dep-name-input-' + id);
    const codeSpan = document.getElementById('dep-code-' + id);
    const codeInput = document.getElementById('dep-code-input-' + id);
    const saveBtn = document.getElementById('save-btn-' + id);

    const editing = nameInput.style.display === 'inline-block';

    nameSpan.style.display = editing ? 'inline' : 'none';
    nameInput.style.display = editing ? 'none' : 'inline-block';
    codeSpan.style.display = editing ? 'inline' : 'none';
    codeInput.style.display = editing ? 'none' : 'inline-block';
    saveBtn.style.display = editing ? 'none' : 'inline-block';
}

function saveEdit(id) {
    const name = document.getElementById('dep-name-input-' + id).value;
    const code = document.getElementById('dep-code-input-' + id).value;

    fetch('../assets/php/script/department_actions.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'edit',
            id: id,
            name: name,
            code: code
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') location.reload();
    });
}

function deleteDepartment(id) {
    if (!confirm("Вы уверены, что хотите удалить департамент?")) return;

    fetch('../assets/php/script/department_actions.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'delete',
            id: id
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') location.reload();
    });
}

function addDepartment(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('addDepForm'));

    fetch('../assets/php/script/department_actions.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'add',
            name: formData.get('name'),
            number: formData.get('number')
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') location.reload();
    });
}
</script>
</body>
</html>
