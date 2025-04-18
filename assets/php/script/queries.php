<?php
function getUserById($userId) {
    global $pdo; 
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getNameDepartmentByIdUser($department_id) {
    global $pdo; 
    $stmt = $pdo->prepare("SELECT department_number FROM departments WHERE id = ?");
    $stmt->execute([$department_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['department_number'] : null;
}
function getUserById2($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT users.*, departments.name AS department_name
        FROM users
        LEFT JOIN departments ON users.department_id = departments.id
        WHERE users.id = ?
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getUsers() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT users.*, departments.name AS department_name FROM users LEFT JOIN departments ON users.department_id = departments.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения всех пользователей
function getUsersQuery() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT users.*, departments.name AS department_name 
                           FROM users 
                           LEFT JOIN departments ON users.department_id = departments.id");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения всех цехов
function getDepartmentsQuery() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM departments");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getProductGraph($product_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            s.name AS stage_name,
            p.name AS part_name,
            sp.quantity_required,
            pr.quantity AS product_quantity,
            (sp.quantity_required * pr.quantity) AS total_required
        FROM stages s
        JOIN stage_parts sp ON s.id = sp.stage_id
        JOIN parts p ON sp.part_id = p.id
        JOIN product_plan pr ON s.product_id = pr.product_id
        WHERE s.product_id = ?
        ORDER BY s.id
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Функция для получения информации о пользователе по ID

function logout(){
    session_start(); // Начинаем сессию
    session_unset();
    session_destroy();
    echo json_encode(['status' => 'success']);
exit();
}
function authUserQuery($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getProductScheduleQuery($product_id) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT 
            s.name AS stage,
            p.name AS part,
            p.nomenclature_number AS nomenclature,
            sp.quantity_required AS quantity
        FROM stages s
        JOIN stage_parts sp ON s.id = sp.stage_id
        JOIN parts p ON sp.part_id = p.id
        WHERE s.product_id = ?
        ORDER BY s.name, p.name
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Получение всех цехов
function getDepartments() {
    global $pdo; 
    $stmt = $pdo->prepare("SELECT * FROM departments");
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
function getProducts() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products");  // изменено на 'products'
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения этапов сборки по выбранному изделию
function getStagesByProduct($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM stages WHERE product_id = ?");  // изменено на 'stages'
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для получения всех деталей
function getParts() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM parts");  // изменено на 'parts'
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для добавления новой детали
function addPart($name, $nomenclatureNumber) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO parts (name, nomenclature_number) VALUES (?, ?)");  // изменено на 'parts'
    $stmt->execute([$name, $nomenclatureNumber]);
    return $pdo->lastInsertId();
}

// Функция для добавления нового этапа
function addStage($productId, $name) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO stages (product_id, name) VALUES (?, ?)");  // изменено на 'stages'
    $stmt->execute([$productId, $name]);
    return $pdo->lastInsertId();
}

// Функция для привязки детали к этапу изделия
function addStagePart($stageId, $partId, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO stage_parts (stage_id, part_id, quantity_required) VALUES (?, ?, ?)");  // изменено на 'stage_parts'
    $stmt->execute([$stageId, $partId, $quantity]);
    return $pdo->lastInsertId();
}

// Функция для получения деталей, привязанных к этапу
function getStageParts($stageId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT parts.name AS part_name, parts.nomenclature_number, stage_parts.quantity_required, stage_parts.id AS stage_part_id
                            FROM stage_parts
                            JOIN parts ON stage_parts.part_id = parts.id
                            WHERE stage_parts.stage_id = ?");
    $stmt->execute([$stageId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Функция для удаления привязки детали к этапу
function deleteStagePart($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM stage_parts WHERE id = :id AND NOT EXISTS (
        SELECT 1 FROM request_parts WHERE request_parts.part_id = stage_parts.part_id
    )");
    // Используйте ассоциативный массив для именованных параметров
    $stmt->execute([':id' => $id]);
}
function confirmPartReception($request_part_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE request_parts SET status = 'received' WHERE id = ?");
    return $stmt->execute([$request_part_id]);
}
function getIncomingParts($department_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            p.name AS part_name,
            rp.quantity AS quantity,
            d_from.name AS from_department,
            rp.id AS request_part_id
        FROM request_parts rp
        JOIN parts p ON rp.part_id = p.id
        JOIN requests r ON rp.request_id = r.id
        JOIN departments d_from ON r.from_department_id = d_from.id
        WHERE r.to_department_id = ? AND rp.status = 'pending'
    ");
    $stmt->execute([$department_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getSentPartsByDispatcher($dispatcher_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            p.name AS part_name,
            rp.quantity AS quantity,
            d.name AS destination_department
        FROM request_parts rp
        JOIN parts p ON rp.part_id = p.id
        JOIN requests r ON rp.request_id = r.id
        JOIN departments d ON r.to_department_id = d.id
        WHERE r.created_by = ?
    ");
    $stmt->execute([$dispatcher_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Получить отправленные перемещения
function getTransfersByFromDepartment($departmentId, $order = 'DESC') {
    global $pdo;
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC'; // Защита от SQL-инъекции
    $stmt = $pdo->prepare("
        SELECT t.*, p.name AS part_name, d.name AS to_department_name
        FROM transfers t
        JOIN parts p ON t.part_id = p.id
        JOIN departments d ON t.to_department_id = d.id
        WHERE t.from_department_id = ?
        ORDER BY t.created_at $order
    ");
    $stmt->execute([$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTransfersByToDepartment($departmentId, $order = 'DESC') {
    global $pdo;
    $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC'; // Защита от SQL-инъекции
    $stmt = $pdo->prepare("
        SELECT t.*, p.name AS part_name, d.name AS from_department_name
        FROM transfers t
        JOIN parts p ON t.part_id = p.id
        JOIN departments d ON t.from_department_id = d.id
        WHERE t.to_department_id = ?
        ORDER BY t.created_at $order
    ");
    $stmt->execute([$departmentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




?>
