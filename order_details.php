<?php
// order_details.php
session_start();

// Проверяем авторизацию
if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: user_orders.php');
    exit;
}

$order_id = $_GET['id'];
$username = $_COOKIE['login'];

// Подключение к базе
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Получаем информацию о заказе
$sql = "SELECT * FROM orders WHERE id = ? AND user_login = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id, $username]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: user_orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа - Redstore</title>
    <!-- Добавьте стили аналогично user_orders.php -->
</head>
<body>
    <!-- Содержимое страницы деталей заказа -->
</body>
</html>