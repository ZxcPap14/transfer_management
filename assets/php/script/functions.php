<?php
include 'connect.php';

function getDepartments() {
    global $pdo;
    return $pdo->query("SELECT * FROM department")->fetchAll(PDO::FETCH_ASSOC);
}

function getUsers() {
    global $pdo;
    return $pdo->query("SELECT users.*, department.department 
                        FROM users 
                        LEFT JOIN department ON users.department_id = department.id")
               ->fetchAll(PDO::FETCH_ASSOC);
}
?>
