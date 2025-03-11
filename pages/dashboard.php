<?php
session_start();
include_once '..\assets\php\script\connect.php';
include_once '..\assets\php\script\queries.php';


// Проверяем, если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем информацию о пользователе
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id); // Запрос для получения данных пользователя

// Определяем роль пользователя
$role = $user['role']; 
$roles = [
    0 => 'Администратор',
    1 => 'Менеджер',
    2 => 'Оператор',
];
// Функции для получения данных в зависимости от роли (например, для диспетчера или админа)
$departments = getDepartments();
$users = getUsers();

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <link rel="stylesheet" href="..\assets\css\style.css">
</head>
<body>
    <div class="container">
        <h2>Панель управления</h2>
        
        <!-- Панель навигации -->
        <nav>
            <ul>
                <li><a href="dashboard.php">Главная</a></li>
                <li><a href="manage_users.php">Управление пользователями</a></li>
                <li><a href="manage_details.php">Управление деталями</a></li>
                <li><a href="javascript:void(0);" onclick="logout()">Выйти</a></li>
            </ul>
        </nav>

        <!-- Контент для разных ролей -->
        <div class="role-section">
            <?php if ($role === '0'): // Администратор ?>
                <h3>Добро пожаловать, Администратор!</h3>
                <p>Вы можете управлять всеми пользователями и настройками системы.</p>
                
                <h4>Пользователи</h4>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Логин</th>
                        <th>Роль</th>
                        <th>Цех</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= $roles[$user['role']] ?></td>
                        <td><?= htmlspecialchars($user['department_name']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php elseif ($role === '1'): // Диспетчер ?>
                <h3>Добро пожаловать, Диспетчер!</h3>
                <p>Вы можете управлять отправленными деталями и контролировать их перемещение.</p>

                <h4>Детали, отправленные на другие цеха</h4>
                <table>
                    <tr>
                        <th>Тип детали</th>
                        <th>Количество</th>
                        <th>Цех назначения</th>
                    </tr>
                    <!-- Здесь выводим детали, отправленные диспетчером -->
                </table>
            <?php elseif ($role === '2'): // Начальник смены ?>
                <h3>Добро пожаловать, Начальник смены!</h3>
                <p>Вы можете получать информацию о поступивших деталях и подтверждать их получение.</p>

                <h4>Поступившие детали</h4>
                <table>
                    <tr>
                        <th>Тип детали</th>
                        <th>Количество</th>
                        <th>Цех отправитель</th>
                        <th>Подтвердить</th>
                    </tr>
                    <!-- Здесь выводим поступившие детали для подтверждения -->
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script src ="..\assets\js\logout.js"></script>
</body>
</html>
