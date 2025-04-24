<?php
session_start();
require_once '../assets/php/script/queries.php';
require_once '../assets/php/script/connect.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$parts = getAllParts();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог деталей</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
    <h2>Каталог деталей</h2>
    <?php include_once '../assets/php/head.php'; ?>


    <h1>Добавить новую деталь</h1>
    <form id="addPartForm">
        <label>Название детали:
            <input type="text" name="name" required>
        </label><br>
        <label>Номенклатурный номер:
            <input type="text" name="nomenclature_number" required>
        </label><br>
        <button type="submit">Добавить</button>
    </form>

    <h1>Список деталей</h1>
    <button onclick="toggleEditMode()">Переключить режим просмотра</button>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Номенклатурный номер</th>
            <th>Применяемость (общее количество)</th>
            <th class="edit-mode">Действия</th>
        </tr>
        <?php foreach ($parts as $part): ?>
            <tr>
                <td><?= htmlspecialchars($part['id']) ?></td>

                <td>
                    <span class="view-mode"><?= htmlspecialchars($part['name']) ?></span>
                    <input type="text" class="edit-mode" value="<?= htmlspecialchars($part['name']) ?>" id="name_<?= $part['id'] ?>">
                </td>

                <td>
                    <span class="view-mode"><?= htmlspecialchars($part['nomenclature_number']) ?></span>
                    <input type="text" class="edit-mode" value="<?= htmlspecialchars($part['nomenclature_number']) ?>" id="nomenclature_<?= $part['id'] ?>">
                </td>

                <td>
                    <?= htmlspecialchars($part['total_quantity']) ?>
                </td>

                <td class="edit-mode">
                    <button onclick="updatePart(<?= $part['id'] ?>)">Сохранить</button>
                    <button onclick="deletePart(<?= $part['id'] ?>)">Удалить</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        let isEditMode = false;
        const editElements = document.querySelectorAll('.edit-mode');
         editElements.forEach(el => el.style.display = isEditMode ? 'inline-block' : 'none');
        function toggleEditMode() {
            isEditMode = !isEditMode;
            const editElements = document.querySelectorAll('.edit-mode');
            const viewElements = document.querySelectorAll('.view-mode');

            editElements.forEach(el => el.style.display = isEditMode ? 'inline-block' : 'none');
            viewElements.forEach(el => el.style.display = isEditMode ? 'none' : 'inline-block');
        }

        document.getElementById('addPartForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            console.log('Отправляемые данные:', data); // 🔧 лог перед отправкой

            try {
                const res = await fetch('../assets/php/script/add_part.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });

                console.log('HTTP статус:', res.status); // 🔧 лог статуса

                const result = await res.json();

                console.log('Ответ сервера:', result); // 🔧 лог ответа

                alert(result.message);
                if (result.status === 'success') location.reload();
            } catch (error) {
                console.error('Ошибка запроса:', error); // 🔧 лог ошибок
                alert('Произошла ошибка при добавлении детали. Проверьте консоль.');
            }
        });


        async function updatePart(id) {
            const name = document.getElementById('name_' + id).value;
            const nomenclature_number = document.getElementById('nomenclature_' + id).value;

            const res = await fetch('../assets/php/script/update_part.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id, name, nomenclature_number })
            });

            const result = await res.json();
            alert(result.message);
            if (result.status === 'success') location.reload();
        }

        async function deletePart(id) {
            if (!confirm("Удалить эту деталь?")) return;

            const res = await fetch('../assets/php/script/delete_part.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id })
            });

            const result = await res.json();
            alert(result.message);
            if (result.status === 'success') location.reload();
        }
    </script>
</body>
</html>
