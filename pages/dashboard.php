<?php
session_start();
include_once '../assets/php/script/connect.php';
include_once '../assets/php/script/queries.php';


// Проверяем, если пользователь не авторизован, перенаправляем на страницу входа
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем информацию о пользователе
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id); // Запрос для получения данных пользователя
$departments = getDepartmentsByUserId($user_id); // новая функция
$departmentId = $departments[0]['id'] ?? null;
$order = ($_GET['order'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';
$receivedTransfers = getTransfersByToDepartment($departmentId, $order);
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
// Получаем список продуктов
$products = getProducts();
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


        <?php
        include_once '../assets/php/head.php';
        ?>

        <!-- Контент для разных ролей -->
        <div class="role-section">
            <?php if ($role === 'admin'): // Администратор ?>
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
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['department_names']) ?></td>
                        </tr>
                    <?php endforeach; ?>



                </table>
            <?php elseif ($role === 'dispatcher'): // Диспетчер ?>
                <h4>Отправить детали</h4>
                <?php
                // Получаем текущий цех пользователя
                $currentUser = getUserById($_SESSION['user_id']);
                $currentDepartmentId = $currentUser['department_id'];
                $currentDepartmentName = getNameDepartmentByIdUser($currentUser['department_id']) ?? 'Неизвестно';
                ?>

                <form id="sendPartForm">
                    <p><strong>Цех отправитель:</strong> <?= htmlspecialchars($currentDepartmentName) ?></p>
                    <input type="hidden" name="from_department" value="<?= $currentDepartmentId ?>">
                    <label for="part_id">Деталь:</label>
                    <select name="part_id" id="part_id">
                        <?php foreach (getParts() as $part): ?>
                            <option value="<?= $part['id'] ?>"><?= htmlspecialchars($part['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="quantity">Количество:</label>
                    <input type="number" name="quantity" id="quantity" min="1" required>

                    <label for="target_department">Цех назначения:</label>
                    <select name="target_department" id="target_department">
                        <?php foreach ($departments as $department): ?>
                            <?php if ($department['id'] != $currentDepartmentId): ?>
                                <option value="<?= $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <label for="order_number">Номер бухгалтерского заказа:</label>
                    <input type="text" name="order_number" id="order_number" required>

                    <button type="submit">Отправить</button>
                </form>

                <div id="sendPartMessage"></div>

                <h4>График комплектации</h4>
                <label for="product">Выберите изделие:</label>
                <select name="product" id="product" onchange="loadProductDetails()">
                    <option value="">Выберите изделие</option>
                    <?php
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            echo '<option value="' . $product['id'] . '">' . htmlspecialchars($product['name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">Нет доступных изделий</option>';
                    }
                    ?>
                </select>

                <div id="detailsGraph" style="margin-top: 20px; display: none;">
                    <h5>График по деталям</h5>
                    <table id="detailsTable">
                        <thead>
                            <tr>
                                <th>Этап</th>
                                <th>Деталь</th>
                                <th>Количество</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Динамически загружаемые данные -->
                        </tbody>
                    </table>
                </div>

                <script>
                    function loadProductDetails() {
                        var productId = document.getElementById('product').value;

                        if (productId) {
                            var xhr = new XMLHttpRequest();
                            xhr.open("GET", "../assets/php/script/get_product_details.php?product_id=" + productId, true);

                            xhr.onload = function () {
                                if (xhr.status === 200) {
                                    try {
                                        var response = JSON.parse(xhr.responseText);

                                        if (response.status === 'success') {
                                            var tableBody = document.getElementById('detailsTable').getElementsByTagName('tbody')[0];
                                            tableBody.innerHTML = '';

                                            response.details.forEach(function (detail) {
                                                var row = tableBody.insertRow();
                                                row.insertCell(0).textContent = detail.stage;
                                                row.insertCell(1).textContent = detail.name;
                                                row.insertCell(2).textContent = detail.quantity;
                                            });

                                            document.getElementById('detailsGraph').style.display = 'block';
                                        } else {
                                            alert('Ошибка загрузки данных');
                                        }
                                    } catch (e) {
                                        alert('Некорректный ответ от сервера');
                                    }
                                } else {
                                    alert('Ошибка при получении данных');
                                }
                            };

                            xhr.send();
                        } else {
                            document.getElementById('detailsGraph').style.display = 'none';
                        }
                    }

                    // Отправка формы перемещения
                    document.getElementById('sendPartForm').addEventListener('submit', function (event) {
                        event.preventDefault();

                        var formData = new FormData(this);
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "../assets/php/script/create_transfer.php", true);

                        xhr.onload = function () {
                            if (xhr.status === 200) {
                                var response = JSON.parse(xhr.responseText);
                                if (response.status === 'success') {
                                    document.getElementById('sendPartMessage').textContent = 'Детали успешно отправлены!';
                                } else {
                                    document.getElementById('sendPartMessage').textContent = 'Ошибка: ' + response.message;
                                }
                            } else {
                                document.getElementById('sendPartMessage').textContent = 'Ошибка при отправке данных';
                            }
                        };

                        xhr.send(formData);
                    });
                </script>

            <?php elseif ($role === 'shift_manager'): // Начальник смены ?>
                <h3>Добро пожаловать, Начальник смены!</h3>
                <p>Вы можете получать информацию о поступивших деталях и подтверждать их получение.</p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Деталь</th>
                            <th>Количество</th>
                            <th>Отправитель</th>
                            <th>Бух. заказ</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <?php if ($role === 'admin' || $role === 'shift_manager'): ?>
                                <th>Действия</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receivedTransfers as $transfer): ?>
                            <?php if($transfer['status'] == "ожидает подтверждения"): ?>
                            <tr>
                                <td><?= $transfer['id'] ?></td>
                                <td><?= htmlspecialchars($transfer['part_name']) ?></td>
                                <td><?= $transfer['quantity'] ?></td>
                                <td><?= htmlspecialchars($transfer['from_department_name']) ?></td>
                                <td><?= htmlspecialchars($transfer['order_number']) ?></td>
                                <td><?= $transfer['status'] ?></td>
                                <td><?= $transfer['created_at'] ?></td>
                                <?php if ($role === 'admin' || $role === 'shift_manager'): ?>
                                    <td>
                                        <?php if ($role === 'shift_manager' && $transfer['status'] === 'ожидает подтверждения'): ?>
                                            <button onclick="updateStatus(<?= $transfer['id'] ?>, 'отправлено')">Отметить как отправлено</button>
                                        <?php endif; ?>
                                        <?php if ($role === 'admin'): ?>
                                            <?php if ($transfer['status'] !== 'подтверждено'): ?>
                                                <button onclick="updateStatus(<?= $transfer['id'] ?>, 'подтверждено')">Подтвердить</button>
                                            <?php endif; ?>
                                            <button onclick="deleteTransfer(<?= $transfer['id'] ?>)">Удалить</button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>       
                            <?php endif;?>
                            
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
        include_once "../assets/php/plan.php";
        ?>
    </div>
    <script src="..\assets\js\logout.js"></script>

</body>

</html>