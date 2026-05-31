<?php
session_start();
try {

    $pdo = new PDO(
        'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
        'root',
        'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die(
        'Ошибка подключения к базе: ' .
        $e->getMessage()
    );
}

/*
Получаем данные формы
*/

$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

/*
Проверка
*/

if (strlen($login) < 2) {
    exit(
        'Логин должен содержать минимум 2 символа'
    );
}

if (strlen($password) < 2) {
    exit(
        'Пароль должен содержать минимум 2 символа'
    );
}

/*
Ищем пользователя
*/

$stmt = $pdo->prepare(
    "SELECT id, NAME, PASSWORD
     FROM regisrtation
     WHERE NAME = ?
     LIMIT 1"
);

$stmt->execute([$login]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

/*
Проверяем пароль
*/

if (
    !$user ||
    !password_verify(
        $password,
        $user['PASSWORD']
    )
) {

    exit(
        'Неверный логин или пароль'
    );
}

/*
Авторизация
*/

setcookie(
    'login',
    $user['NAME'],
    time() + 3600 * 24 * 30,
    '/'
);

header(
    'Location: /user.php'
);

exit;
?>
