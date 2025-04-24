<?php
include_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Добавление пользователя
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $roles = $_POST['role']; // Массив ролей
        $departments = $_POST['department_id']; // Массив департаментов
        $fullname = $_POST['fullname'];

        // Добавляем пользователя в таблицу users
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $fullname]);
        $user_id = $pdo->lastInsertId();

        // Добавляем роли в таблицу user_role
        foreach ($roles as $role) {
            $stmt = $pdo->prepare("INSERT INTO user_role (user_id, role) VALUES (?, ?)");
            $stmt->execute([$user_id, $role]);
        }

        // Добавляем департаменты в таблицу user_departments
        foreach ($departments as $department) {
            $stmt = $pdo->prepare("INSERT INTO user_departments (user_id, department_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $department]);
        }

        echo json_encode(['status' => 'success', 'message' => 'Пользователь добавлен']);
        exit();
    }

    // Редактирование пользователя
    if (isset($_POST['edit_user'])) {
        $userId = $_POST['id'];
        $username = $_POST['username'];
        $role = $_POST['role'];
        $departments = json_decode($_POST['department_ids'], true); // Получаем массив ID департаментов
        $fullname = $_POST['fullname'];
    
        // Обновляем данные пользователя (например, имя, логин, роль)
        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, full_name = ? WHERE id = ?");
        $stmt->execute([$username, json_encode($role), $fullname, $userId]);
    
        // Привязываем новые цеха
        assignDepartmentsToUser($userId, $departments);
    
        echo json_encode(['status' => 'success', 'message' => 'Данные пользователя успешно обновлены']);
    }
    if (isset($_POST['assign_departments'])) {
        $userId = $_POST['user_id'];
        $departments = json_decode($_POST['departments']); // Массив ID выбранных цехов

        // Добавляем новые привязки, проверяя, что пользователь не привязан к данному цеху
        foreach ($departments as $departmentId) {
            // Проверяем, есть ли уже такая привязка
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_departments WHERE user_id = ? AND department_id = ?");
            $stmt->execute([$userId, $departmentId]);
            $exists = $stmt->fetchColumn();

            // Если привязка уже существует, пропускаем вставку
            if ($exists == 0) {
                // Вставляем новую привязку
                $stmt = $pdo->prepare("INSERT INTO user_departments (user_id, department_id) VALUES (?, ?)");
                $stmt->execute([$userId, $departmentId]);
            }
        }

        echo json_encode(['status' => 'success', 'message' => 'Цеха успешно привязаны']);
        exit();
    }
    if (isset($_POST['remove_departments'])) {
        $userId = $_POST['user_id'];
        $departments = json_decode($_POST['departments']); // Массив ID цехов для отвязки
    
        // Удаляем привязки
        foreach ($departments as $departmentId) {
            $stmt = $pdo->prepare("DELETE FROM user_departments WHERE user_id = ? AND department_id = ?");
            $stmt->execute([$userId, $departmentId]);
        }
    
        echo json_encode(['status' => 'success', 'message' => 'Цеха успешно отвязаны']);
        exit();
    }    

    // Удаление пользователя
    if (isset($_POST['delete_user'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        // Удаляем роли и департаменты пользователя
        $stmt = $pdo->prepare("DELETE FROM user_role WHERE user_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM user_departments WHERE user_id = ?");
        $stmt->execute([$id]);

        echo json_encode(['status' => 'success', 'message' => 'Пользователь удален']);
        exit();
    }

    // Изменение пароля
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
