<?php
session_start();

// Подключаем соединение и запросы
include_once 'connect.php';
include_once 'queries.php';

// Проверяем, был ли запрос POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные
    $data = json_decode(file_get_contents("php://input"));

    // Проверяем, что данные переданы
    if (isset($data->username) && isset($data->password)) {
        $username = $data->username;
        $password = md5($data->password); // Хешируем пароль

        // Используем функцию для авторизации пользователя
        $user = authUserQuery($username, $password);

        // Если пользователь найден
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            echo json_encode(['status' => 'success']); // Ответ в формате JSON
            exit();
        } else {
            // Если пользователь не найден
            echo json_encode(['status' => 'error', 'message' => '1']);
            exit();
        }
    } else {
        // Если данные не были переданы
        echo json_encode(['status' => 'error', 'message' => '2']);
        exit();
    }
} else {
    // Если метод запроса не POST
    echo json_encode(['status' => 'error', 'message' => '3']);
    exit();
}
?>
