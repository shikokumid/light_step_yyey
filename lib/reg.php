<?php

date_default_timezone_set('Asia/Krasnoyarsk');

$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (strlen($login) < 2) {
    exit('Логин должен содержать минимум 2 символа');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Некорректный email');
}

if (strlen($password) < 6) {
    exit('Пароль должен содержать минимум 6 символов');
}

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);

/*
Проверяем существование email
*/

$stmt = $pdo->prepare(
    "SELECT id
     FROM regisrtation
     WHERE MAIL = ?
     LIMIT 1"
);

$stmt->execute([$email]);

if ($stmt->fetch()) {
    exit('Пользователь с таким email уже зарегистрирован');
}

/*
Хешируем пароль
*/

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/*
Сохраняем пользователя
*/

$stmt = $pdo->prepare(
    "INSERT INTO regisrtation
    (NAME, MAIL, PASSWORD)
    VALUES (?, ?, ?)"
);

$stmt->execute([
    $login,
    $email,
    $passwordHash
]);

/*
Mailtrap API
*/

$apiKey = 'ad3761584bcccef40910d3716d8069ef';

$data = [
    'from' => [
        'email' => 'hello@demomailtrap.co',
        'name' => 'Легкий Шаг'
    ],
    'to' => [
        [
            'email' => $email
        ]
    ],
    'subject' => 'Добро пожаловать в Легкий Шаг',
    'text' =>
        "Здравствуйте, {$login}!\n\n" .
        "Вы успешно зарегистрировались в интернет-магазине «Легкий Шаг».\n\n" .
        "Спасибо за регистрацию!"
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://send.api.mailtrap.io/api/send');
curl_setopt($ch, CURLOPT_POST, true);

curl_setopt(
    $ch,
    CURLOPT_HTTPHEADER,
    [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ]
);

curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    json_encode($data)
);

curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);

$response = curl_exec($ch);

if ($response === false) {
    error_log(curl_error($ch));
}

curl_close($ch);

header('Location: /account.php');
exit;
