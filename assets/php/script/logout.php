<?php
session_start(); // Начинаем сессию

// Удаляем все сессионные переменные
session_unset();

// Уничтожаем сессию
session_destroy();

// Перенаправляем на страницу логина
echo json_encode(['status' => 'success']);
exit();
?>
