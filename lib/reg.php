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

    exit(
        'Ошибка подключения к базе: ' .
        $e->getMessage()
    );
}

/*
Проверка на существующий аккаунт
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
Хеширование пароля
*/

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

/*
Сохранение пользователя
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
Отправка письма через Resend API
*/

$resendApiKey = 'ad3761584bcccef40910d3716d8069ef';

$emailData = [
    'from' => 'onboarding@resend.dev',
    'to' => [$email],
    'subject' => 'Добро пожаловать в Легкий Шаг',
    'html' => "
        <h2>Здравствуйте, {$login}!</h2>

        <p>
            Вы успешно зарегистрировались
            в интернет-магазине «Легкий Шаг».
        </p>

        <p>
            Спасибо за регистрацию ❤️
        </p>

        <p>
            Теперь вы можете войти
            в личный кабинет и оформлять заказы.
        </p>
    "
];

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.resend.com/emails',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($emailData),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $resendApiKey,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);

curl_close($ch);

/*
Редирект
*/

header('Location: /account.php');

exit;

?>
