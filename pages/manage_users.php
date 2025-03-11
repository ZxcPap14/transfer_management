<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== '0') {
    header('Location: ../index.php');
    exit();
}

$users = getUsersQuery();
$departments = getDepartmentsQuery();
$roles = ['0' => 'Админ', '1' => 'Диспетчер', '2' => 'Начальник смены'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
    }

    if (isset($_POST['edit_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $department_id = $_POST['department_id'];

        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, department_id = ? WHERE id = ?");
        $stmt->execute([$username, $role, $department_id, $user_id]);
    }

    if (isset($_POST['change_password'])) {
        $user_id = $_POST['user_id'];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $user_id]);
    }

    exit(json_encode(['status' => 'success']));
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        function toggleEdit(userId) {
            let row = document.getElementById('user-row-' + userId);
            let isEditing = row.classList.toggle('editing');

            let usernameSpan = document.getElementById('username-span-' + userId);
            let usernameInput = document.getElementById('username-input-' + userId);

            let roleSpan = document.getElementById('role-span-' + userId);
            let roleSelect = document.getElementById('role-select-' + userId);

            let departmentSpan = document.getElementById('department-span-' + userId);
            let departmentSelect = document.getElementById('department-select-' + userId);

            let saveButton = document.getElementById('save-btn-' + userId);
            let editButton = document.getElementById('edit-btn-' + userId);

            if (isEditing) {
                usernameSpan.style.display = 'none';
                usernameInput.style.display = 'inline-block';

                roleSpan.style.display = 'none';
                roleSelect.style.display = 'inline-block';

                departmentSpan.style.display = 'none';
                departmentSelect.style.display = 'inline-block';

                saveButton.style.display = 'inline-block';
                editButton.textContent = 'Отмена';
            } else {
                usernameSpan.style.display = 'inline-block';
                usernameInput.style.display = 'none';

                roleSpan.style.display = 'inline-block';
                roleSelect.style.display = 'none';

                departmentSpan.style.display = 'inline-block';
                departmentSelect.style.display = 'none';

                saveButton.style.display = 'none';
                editButton.textContent = 'Редактировать';
            }
        }

        function saveUser(userId) {
            let username = document.getElementById('username-input-' + userId).value;
            let role = document.getElementById('role-select-' + userId).value;
            let department_id = document.getElementById('department-select-' + userId).value;

            let formData = new FormData();
            formData.append('edit_user', '1');
            formData.append('user_id', userId);
            formData.append('username', username);
            formData.append('role', role);
            formData.append('department_id', department_id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(() => location.reload())
            .catch(error => console.error('Ошибка:', error));
        }

        function deleteUser(userId) {
            if (confirm("Вы уверены, что хотите удалить этого пользователя?")) {
                let formData = new FormData();
                formData.append('delete_user', '1');
                formData.append('user_id', userId);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(() => location.reload())
                .catch(error => console.error('Ошибка:', error));
            }
        }

        function changePassword(userId) {
            let newPassword = prompt('Введите новый пароль:');
            if (newPassword) {
                let formData = new FormData();
                formData.append('change_password', '1');
                formData.append('user_id', userId);
                formData.append('new_password', newPassword);

                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(() => alert('Пароль изменен'))
                .catch(error => console.error('Ошибка:', error));
            }
        }
    </script>
</head>
<body>
    <h2>Пользователи</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Логин</th>
            <th>Роль</th>
            <th>Цех</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr id="user-row-<?= $user['id'] ?>">
            <td><?= $user['id'] ?></td>
            <td>
                <span id="username-span-<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></span>
                <input type="text" id="username-input-<?= $user['id'] ?>" value="<?= htmlspecialchars($user['username']) ?>" style="display: none;">
            </td>
            <td>
                <span id="role-span-<?= $user['id'] ?>"><?= $roles[$user['role']] ?></span>
                <select id="role-select-<?= $user['id'] ?>" style="display: none;">
                    <?php foreach ($roles as $key => $role): ?>
                        <option value="<?= $key ?>" <?= $key == $user['role'] ? 'selected' : '' ?>><?= $role ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <span id="department-span-<?= $user['id'] ?>"><?= htmlspecialchars($user['department_name']) ?></span>
                <select id="department-select-<?= $user['id'] ?>" style="display: none;">
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>" <?= $department['id'] == $user['department_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($department['department']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <button id="edit-btn-<?= $user['id'] ?>" onclick="toggleEdit(<?= $user['id'] ?>)">Редактировать</button>
                <button id="save-btn-<?= $user['id'] ?>" onclick="saveUser(<?= $user['id'] ?>)" style="display: none;">Сохранить</button>
                <button onclick="deleteUser(<?= $user['id'] ?>)">Удалить</button>
                <button onclick="changePassword(<?= $user['id'] ?>)">Изменить пароль</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Добавить пользователя</h3>
    <form id="addUserForm" onsubmit="addUser(event)">
        <input type="hidden" name="add_user" value="1">
        <input type="text" name="username" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <select name="role">
            <?php foreach ($roles as $key => $role): ?>
                <option value="<?= $key ?>"><?= $role ?></option>
            <?php endforeach; ?>
        </select>
        <select name="department_id">
            <?php foreach ($departments as $department): ?>
                <option value="<?= $department['id'] ?>"><?= $department['department'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Добавить</button>
    </form>
</body>
</html>
