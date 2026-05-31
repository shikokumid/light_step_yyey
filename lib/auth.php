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

if (!$login || !$password) {
    exit('Введите логин и пароль');
}

$stmt = $pdo->prepare("
    SELECT *
    FROM regisrtation
    WHERE NAME = ?
    LIMIT 1
");

$stmt->execute([$login]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    exit('Неверный логин или пароль');
}

$dbPassword = $user['PASSWORD'];

/*
Поддержка новых паролей password_hash()
*/
if (password_verify($password, $dbPassword)) {

    setcookie('login', $user['NAME'], time() + 60*60*24*30, '/');

    header('Location: /user.php');
    exit;
}

/*
Поддержка старых md5
*/
$oldSalt = 'lasdl;askd;ak213';

if (md5($oldSalt . $password) === $dbPassword) {

    /*
    Сразу обновляем пароль на password_hash
    */
    $newHash = password_hash($password, PASSWORD_DEFAULT);

    $update = $pdo->prepare("
        UPDATE regisrtation
        SET PASSWORD = ?
        WHERE id = ?
    ");

    $update->execute([
        $newHash,
        $user['id']
    ]);

    setcookie('login', $user['NAME'], time() + 60*60*24*30, '/');

    header('Location: /user.php');
    exit;
}

exit('Неверный логин или пароль');
