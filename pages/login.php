<?php
session_start();
include_once '..\assets\php\script\connect.php';
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="..\assets\css\style.css">
</head>
<body>
    <div class="container">
        <h2>Вход</h2>
        <form onsubmit="event.preventDefault(); login();">
            <input type="text" id="username" name="username" placeholder="Логин" required>
            <input type="password" id="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
        <div id="error-message" style="color: red;"></div>
    </div>
    <script>
         function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            fetch("../assets/php/script/auth.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username: username, password: password }) // Отправляем данные в JSON
            })
            .then(response => response.json()) // Получаем ответ в формате JSON
            .then(data => {
                if (data.status === 'success') {
                    // Перенаправляем на панель управления
                    window.location.href = 'dashboard.php';
                } else {
                    // Выводим ошибку
                    alert(data.message || 'Произошла ошибка при авторизации.');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при авторизации.');
            });
        }
    </script>
</body>
</html>
