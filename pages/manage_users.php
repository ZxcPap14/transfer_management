<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$roles = ['admin' => 'Админ', 'dispatcher' => 'Диспетчер', 'shift_manager' => 'Начальник смены'];
$departments = getDepartmentsQuery();

$users = getUsersQuery(); // Эта функция должна включать join с департаментами
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function showAlert(message) {
            alert(message);
        }

        function saveUser(id) {
            const username = document.getElementById('username-' + id).value;
            const role = document.getElementById('role-' + id).value;
            const department = document.getElementById('department-' + id).value;
            const fullname = document.getElementById('full_name-' + id).value;

            const formData = new FormData();
            formData.append('edit_user', 1);
            formData.append('id', id);
            formData.append('username', username);
            formData.append('role', role);
            formData.append('department_id', department);
            formData.append('fullname', fullname);

            fetch('../assets/php/script/users.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    showAlert(data.message);
                    if (data.status === 'success') location.reload();
                });
        }

        function deleteUser(id) {
            if (!confirm('Удалить пользователя?')) return;
            const formData = new FormData();
            formData.append('delete_user', 1);
            formData.append('id', id);

            fetch('../assets/php/script/users.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    showAlert(data.message);
                    if (data.status === 'success') location.reload();
                });
        }

        function changePassword(id) {
            const newPassword = prompt("Введите новый пароль:");
            if (!newPassword) return;

            const formData = new FormData();
            formData.append('change_password', 1);
            formData.append('id', id);
            formData.append('new_password', newPassword);

            fetch('../assets/php/script/users.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => showAlert(data.message));
        }

        function addUser(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('addForm'));

            fetch('../assets/php/script/users.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    showAlert(data.message);
                    if (data.status === 'success') location.reload();
                });
        }
    </script>
</head>
<body>
    <h2>Управление пользователями</h2>
    <?php include_once '../assets/php/head.php'; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Логин</th>
            <th>Роль</th>
            <th>Цех</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><input type="text" id="full_name-<?= $user['id'] ?>" value="<?= htmlspecialchars($user['full_name']) ?>"></td>
            <td><input type="text" id="username-<?= $user['id'] ?>" value="<?= htmlspecialchars($user['username']) ?>"></td>
            <td>
                <select id="role-<?= $user['id'] ?>">
                    <?php foreach ($roles as $key => $val): ?>
                        <option value="<?= $key ?>" <?= $user['role'] === $key ? 'selected' : '' ?>><?= $val ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <select id="department-<?= $user['id'] ?>">
                    <?php foreach ($departments as $dep): ?>
                        <option value="<?= $dep['id'] ?>" <?= $user['department_id'] == $dep['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dep['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <button onclick="saveUser(<?= $user['id'] ?>)">Сохранить</button>
                <button onclick="deleteUser(<?= $user['id'] ?>)">Удалить</button>
                <button onclick="changePassword(<?= $user['id'] ?>)">Изменить пароль</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Добавить пользователя</h3>
    <form id="addForm" onsubmit="addUser(event)">
        <input type="hidden" name="add_user" value="1">
        <input type="text" name="fullname" placeholder="ФИО" required>
        <input type="text" name="username" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <select name="role">
            <?php foreach ($roles as $key => $val): ?>
                <option value="<?= $key ?>"><?= $val ?></option>
            <?php endforeach; ?>
        </select>
        <select name="department_id">
            <?php foreach ($departments as $dep): ?>
                <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Добавить</button>
    </form>
</body>
</html>
