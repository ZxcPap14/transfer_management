<?php
// Получение всех пользователей с их ролями и цехами
function getUsers() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT users.*, department.department AS department_name FROM users LEFT JOIN department ON users.department_id = department.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения всех пользователей
function getUsersQuery() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT users.*, department.department AS department_name 
                           FROM users 
                           LEFT JOIN department ON users.department_id = department.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения всех цехов
function getDepartmentsQuery() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM department");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения информации о пользователе по ID
function getUserById($userId) {
    global $pdo; 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function authUserQuery($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Получение всех цехов
function getDepartments() {
    global $pdo; 
    $stmt = $pdo->prepare("SELECT * FROM department");
    $stmt->execute();

    // Возвращаем все записи из таблицы в виде массива ассоциативных массивов
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение информации о пользователе по имени и паролю
function getUserByCredentialsQuery() {
    return "SELECT * FROM users WHERE username = ? AND password = ?";
}
// Добавление нового пользователя
function addUserQuery($username, $password, $role, $department_id) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, department_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, md5($password), $role, $department_id]);
}

// Удаление пользователя
function deleteUserQuery($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$user_id]);
}

// Обновление данных пользователя
function updateUserQuery($user_id, $username, $role, $department_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, department_id = ? WHERE id = ?");
    return $stmt->execute([$username, $role, $department_id, $user_id]);
}
?>
