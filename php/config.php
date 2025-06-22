<?php
$host = 'localhost';
$db   = 'gruzovozoff';   // имя вашей базы
$user = 'root';       // имя пользователя MySQL
$pass = '';   // пароль пользователя MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Ошибка подключения к базе: ' . $e->getMessage());
}
?>
