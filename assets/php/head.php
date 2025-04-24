
<?php 
include_once '../assets/php/script/queries.php';
$user_id = $_SESSION['user_id'];

$user = getUserById($user_id);
$role = $user['role'];
?>
<nav>
    <ul>
        <?php if ($role === 'admin'):?>
            <li><a href="dashboard.php">Главная</a></li>
            <li><a href="../pages/manage_users.php">Управление пользователями</a></li>  
            <li><a href="../pagesproduct_graph.php">График сборки </a></li>
            <li><a href="" onclick="logout()">Выйти</a></li>
        <?php elseif ($role === 'dispatcher'):?>
            <li><a href="dashboard.php">Главная</a></li>
            <li><a href="../pages/products.php">Изделия</a></li>
            <li><a href="../pagesproduct_graph.php">График сборки </a></li>
            <li><a href="../pages/view_transfers.php">Трансфер деталей</a></li>
            <li><a href="../pages/manage_orders.php">Бух. Заказы </a></li>
            <li><a href="../pages/manage_parts.php">Каталог деталей</a></li>
            <li><a href="" onclick="logout()">Выйти</a></li>
        <?php elseif ($role === 'shift_manager'):?>
            <li><a href="dashboard.php">Главная</a></li>
            <li><a href="../pages/manage_users.php">Управление пользователями</a></li>  
            <li><a href="../pages/departments.php">Управление цехами</a></li>
            <li><a href="../pages/year_plan.php">Управление планом</a></li>
            <li><a href="../pagesproduct_graph.php">График сборки </a></li>
            <li><a href="../pages/manage_parts.php">Каталог деталей</a></li>
            <li><a href="../pages/assign_parts.php">Этапы сборки</a></li>
            <li><a href="../pages/products.php">Изделия</a></li>
            <li><a href="../pages/view_transfers.php">Трансфер деталей</a></li>
            <li><a href="../pages/manage_orders.php">Бух. Заказы </a></li>
            <li><a href="" onclick="logout()">Выйти</a></li>
        <?php endif;?>
    </ul>
</nav>
<script>
    function logout() {
            fetch('../assets/php/script/logout.php', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = '../index.php';
                } else {
                    alert('Ошибка выхода');
                }
            })
            .catch(() => alert('Ошибка соединения'));
    }
</script>
