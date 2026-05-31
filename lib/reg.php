<?php

session_start();


// Подключаем PHPMailer вручную

require_once dirname(__DIR__) . '/PHPMailer-master/src/Exception.php';
require_once dirname(__DIR__) . '/PHPMailer-master/src/PHPMailer.php';
require_once dirname(__DIR__) . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Получаем данные формы

$login = trim($_POST['login'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (strlen($login) < 2) {
    exit('Логин слишком короткий');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Некорректный email');
}

if (strlen($password) < 6) {
    exit('Пароль должен быть не менее 6 символов');
}

// hash password
$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

// connect db
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

} catch(PDOException $e) {

    die(
        'Ошибка подключения к базе: ' .
        $e->getMessage()
    );
}

/*
Проверяем email
*/
$check = $pdo->prepare(
    "SELECT id
     FROM regisrtation
     WHERE MAIL = ?
     LIMIT 1"
);

$check->execute([$email]);

if ($check->fetch()) {
    exit(
        'Пользователь с таким email уже существует'
    );
}

/*
Сохраняем пользователя
*/
$stmt = $pdo->prepare(
    "INSERT INTO regisrtation (
        NAME,
        MAIL,
        PASSWORD
    ) VALUES (?, ?, ?)"
);

$stmt->execute([
    $login,
    $email,
    $passwordHash
]);

/*
Отправляем письмо
*/
$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';

    $mail->SMTPAuth = true;

    $mail->Username = 'your-email@gmail.com';

    $mail->Password = 'your-app-password';

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        'your-email@gmail.com',
        'Легкий Шаг'
    );

    $mail->addAddress(
        $email,
        $login
    );

    $mail->isHTML(true);

    $mail->Subject =
        'Добро пожаловать в Легкий Шаг';

    $mail->Body = "
        <h2>Здравствуйте, {$login}!</h2>

        <p>
            Вы успешно зарегистрировались
            в интернет-магазине «Легкий Шаг».
        </p>

        <p>
            Теперь вы можете войти
            в личный кабинет и оформлять заказы.
        </p>

        <p>
            С уважением,<br>
            команда «Легкий Шаг»
        </p>
    ";

    $mail->send();

} catch (Exception $e) {

    error_log(
        'Ошибка отправки письма: ' .
        $mail->ErrorInfo
    );
}

/*
Редирект
*/
header('Location: /account.php');

exit;
