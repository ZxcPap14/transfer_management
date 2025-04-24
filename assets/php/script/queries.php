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
function getDepartmentsByUserId($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT d.id, d.name 
        FROM user_departments ud
        JOIN departments d ON ud.department_id = d.id
        WHERE ud.user_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getUsers() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.username,
            u.role,
            u.full_name,
            GROUP_CONCAT(d.name SEPARATOR ', ') AS department_names
        FROM users u
        LEFT JOIN user_departments ud ON u.id = ud.user_id
        LEFT JOIN departments d ON ud.department_id = d.id
        GROUP BY u.id, u.username, u.role, u.full_name
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getUsersQuery() {
    global $pdo;
    // Запрос, возвращающий имена и ID всех департаментов пользователя
    $stmt = $pdo->prepare("SELECT u.id, u.username, u.full_name, u.role, 
                                  GROUP_CONCAT(d.name ORDER BY d.name) AS department_names,
                                  GROUP_CONCAT(d.id) AS department_ids
                           FROM users u
                           LEFT JOIN user_departments ud ON u.id = ud.user_id
                           LEFT JOIN departments d ON ud.department_id = d.id
                           GROUP BY u.id");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Преобразуем строку с ID в массив
    foreach ($users as &$user) {
        $user['department_ids'] = $user['department_ids'] 
            ? array_map('intval', explode(',', $user['department_ids']))
            : [];
    }

    return $users;
}

function getDepartmentsQuery() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM departments");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getDepartmentById($userId){
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->execute(params: [$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // вернёт массив ID департаментов

}
// Функция для получения списка привязанных цехов к пользователю
function getUserDepartmentIds($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT department_id FROM user_departments WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN); // Возвращает массив ID цехов
}
// Функция для привязки цехов к пользователю
function assignDepartmentsToUser($userId, $departmentIds) {
    global $pdo;

    // Сначала удаляем все старые записи из таблицы user_departments для этого пользователя
    $stmt = $pdo->prepare("DELETE FROM user_departments WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Добавляем новые привязки
    foreach ($departmentIds as $departmentId) {
        $stmt = $pdo->prepare("INSERT INTO user_departments (user_id, department_id) VALUES (?, ?)");
        $stmt->execute([$userId, $departmentId]);
    }
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
function getTransfersByFromDepartment2($departmentId, $order = 'DESC', $statusFilter = null, $orderFilter = null) {
    global $pdo;

    $query = "
        SELECT t.*, 
               p.name AS part_name,
               d_to.name AS to_department_name
        FROM transfers t
        JOIN parts p ON t.part_id = p.id
        JOIN departments d_to ON t.to_department_id = d_to.id
        WHERE t.from_department_id = ?
    ";

    $params = [$departmentId];

    if ($statusFilter !== null && $statusFilter !== '') {
        $query .= " AND t.status = ?";
        $params[] = $statusFilter;
    }

    if ($orderFilter !== null && $orderFilter !== '') {
        $query .= " AND t.order_number LIKE ?";
        $params[] = '%' . $orderFilter . '%';
    }

    $query .= " ORDER BY t.created_at $order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTransfersByToDepartment2($departmentId, $order = 'DESC', $statusFilter = null, $orderFilter = null) {
    global $pdo;

    $query = "
        SELECT t.*, 
               p.name AS part_name,
               d_from.name AS from_department_name
        FROM transfers t
        JOIN parts p ON t.part_id = p.id
        JOIN departments d_from ON t.from_department_id = d_from.id
        WHERE t.to_department_id = ?
    ";

    $params = [$departmentId];

    if ($statusFilter !== null && $statusFilter !== '') {
        $query .= " AND t.status = ?";
        $params[] = $statusFilter;
    }

    if ($orderFilter !== null && $orderFilter !== '') {
        $query .= " AND t.order_number LIKE ?";
        $params[] = '%' . $orderFilter . '%';
    }

    $query .= " ORDER BY t.created_at $order";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllParts() {
    global $pdo;
    $sql = "
        SELECT 
            p.id, 
            p.name, 
            p.nomenclature_number,
            COALESCE(SUM(sp.quantity_required * pp.quantity), 0) AS total_quantity
        FROM parts p
        LEFT JOIN stage_parts sp ON p.id = sp.part_id
        LEFT JOIN stages s ON sp.stage_id = s.id
        LEFT JOIN products pr ON s.product_id = pr.id
        LEFT JOIN product_plan pp ON pr.id = pp.product_id
        GROUP BY p.id, p.name, p.nomenclature_number
        ORDER BY p.name
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getParts() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM parts");  // изменено на 'parts'
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllProducts() {
    global $pdo;
    $sql = "SELECT id, name, designation FROM products";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllAccountingOrdersId() {
    global $pdo;
    $sql = "SELECT order_number FROM accounting_orders";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllProductPlans() {
    global $pdo;
    $sql = "
        SELECT pp.id, p.name AS product_name, pp.year, pp.quantity, pp.order_number
        FROM product_plan pp
        JOIN products p ON pp.product_id = p.id
        JOIN accounting_orders ao ON pp.order_number = ao.order_number
        ORDER BY pp.year DESC, p.name
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAllAccountingOrders() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM accounting_orders ORDER BY id DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addAccountingOrder($order_number, $description, $customer) {
    global $pdo;
    $sql = "INSERT INTO accounting_orders (order_number, description, customer) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$order_number, $description, $customer]);
}

function deleteAccountingOrder($id) {
    global $pdo;
    $sql = "DELETE FROM accounting_orders WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$id]);
}
function addProductPlan($product_id, $year, $quantity, $order_number) {
    global $pdo;
    $sql = "INSERT INTO product_plan (product_id, year, quantity, order_number) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$product_id, $year, $quantity, $order_number]);
}
// Функция для обновления количества детали на этапе
function updateStagePartQuantity($stagePartId, $quantity) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE stage_parts SET quantity_required = :quantity WHERE id = :stagePartId");
    $stmt->execute([
        ':quantity' => $quantity,
        ':stagePartId' => $stagePartId
    ]);
}
// Функция для удаления этапа
function deleteStage($stageId) {
    global $pdo;
    // Удаляем привязки деталей к этапу
    $stmt = $pdo->prepare("DELETE FROM stage_parts WHERE stage_id = :stageId");
    $stmt->execute([':stageId' => $stageId]);
    
    // Удаляем этап
    $stmt = $pdo->prepare("DELETE FROM stages WHERE id = :stageId");
    $stmt->execute([':stageId' => $stageId]);
}
// Получение ID продукта по имени
function getProductIdByName($productName) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM products WHERE name = :name");
    $stmt->execute([':name' => $productName]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $product ? $product['id'] : null;
}

// Добавление нового продукта
function addProduct($productName) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO products (name) VALUES (:name)");
    $stmt->execute([':name' => $productName]);
    return $pdo->lastInsertId();  // Возвращаем ID добавленного продукта
}
function getPartIdByNomenclature($nomenclatureNumber) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM parts WHERE nomenclature_number = :nomenclature_number");
    $stmt->execute([':nomenclature_number' => $nomenclatureNumber]);
    $part = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $part ? $part['id'] : null;
}
function getStageIdByName($stageName, $productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM stages WHERE name = :name AND product_id = :product_id");
    $stmt->execute([':name' => $stageName, ':product_id' => $productId]);
    $stage = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $stage ? $stage['id'] : null;
}


?>
