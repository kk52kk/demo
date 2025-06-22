<?php
session_start();

$login = $_POST['admin_login'];
$password = $_POST['admin_password'];

// Примитивная проверка. Можно заменить на проверку из БД.
if ($login === 'admin' && $password === '12345') {
    $_SESSION['admin'] = true;
    header("Location: admin.php");
    exit();
} else {
    echo "<script>alert('Неверные данные администратора!');window.location.href='login.php';</script>";
}
?>
