<?php
session_start();

// Проверяем авторизацию
if (!isset($_COOKIE['login'])) {
    echo 'not_logged_in';
    exit;
}

$username = $_COOKIE['login'];

// Подключение к БД
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Получаем ID пользователя
$userStmt = $pdo->prepare("SELECT id FROM regisrtation WHERE NAME = ?");
$userStmt->execute([$username]);
$user = $userStmt->fetch();

if (!$user) {
    echo 'not_logged_in';
    exit;
}

$user_id = $user['id'];

// Проверяем, передан ли ID товара
if (!isset($_POST['product_id'])) {
    echo 'error';
    exit;
}

$product_id = (int)$_POST['product_id'];

// Проверяем, есть ли уже в избранном
$checkStmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$checkStmt->execute([$user_id, $product_id]);
$exists = $checkStmt->fetch();

if ($exists) {
    // Удаляем
    $deleteStmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $deleteStmt->execute([$user_id, $product_id]);
    echo 'removed';
} else {
    // Добавляем
    $insertStmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())");
    $insertStmt->execute([$user_id, $product_id]);
    echo 'added';
}
