<?php
include_once 'connect.php';
$roles = ['admin' => 'Админ', 'dispatcher' => 'Диспетчер', 'shift_manager' => 'Начальник смены'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $role = $_POST['role'];
        $department_id = $_POST['department_id'];
        $fullname = $_POST['fullname'];

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, full_name, department_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $role, $fullname, $department_id]);

        echo json_encode(['status' => 'success', 'message' => 'Пользователь добавлен']);
        exit();
    }

    if (isset($_POST['edit_user'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $department_id = $_POST['department_id'];
        $fullname = $_POST['fullname'];

        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, department_id = ?, full_name = ? WHERE id = ?");
        $stmt->execute([$username, $role, $department_id, $fullname, $id]);

        echo json_encode(['status' => 'success', 'message' => 'Данные пользователя обновлены']);
        exit();
    }

    if (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Пользователь удален']);
        exit();
    }

    if (isset($_POST['change_password'])) {
        $id = $_POST['id'];
        $new_password = md5($_POST['new_password']);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$new_password, $id]);
        echo json_encode(['status' => 'success', 'message' => 'Пароль изменен']);
        exit();
    }

    exit(json_encode(['status' => 'error', 'message' => 'Неверный запрос']));
}

?>