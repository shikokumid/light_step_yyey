<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if (strlen($login) < 2) {
    exit('Введите логин');
}

if (strlen($password) < 1) {
    exit('Введите пароль');
}

$stmt = $pdo->prepare("
    SELECT id, NAME, PASSWORD
    FROM regisrtation
    WHERE NAME = ?
    LIMIT 1
");

$stmt->execute([$login]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('Неверный логин или пароль');
}

if (!password_verify($password, $user['PASSWORD'])) {
    exit('Неверный логин или пароль');
}

setcookie(
    'login',
    $user['NAME'],
    time() + 60 * 60 * 24 * 30,
    '/'
);

header('Location: /user.php');
exit;
