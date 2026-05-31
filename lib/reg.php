<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
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

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

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

    /*
    Проверяем существование email
    */
    $check = $pdo->prepare(
        "SELECT id
         FROM regisrtation
         WHERE MAIL = ?
         LIMIT 1"
    );

    $check->execute([$email]);

    if ($check->fetch()) {
        exit('Пользователь с таким email уже зарегистрирован');
    }

    /*
    Регистрация
    */
    $sql = $pdo->prepare(
        "INSERT INTO regisrtation (
            NAME,
            MAIL,
            PASSWORD
        ) VALUES (?, ?, ?)"
    );

    $sql->execute([
        $login,
        $email,
        $passwordHash
    ]);

} catch (PDOException $e) {

    error_log(
        'DB error: ' . $e->getMessage()
    );

    exit('Ошибка базы данных');
}

/*
Отправка письма
*/
$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = getenv('SMTP_HOST');

    $mail->SMTPAuth = true;

    $mail->Username = getenv('SMTP_USER');

    $mail->Password = getenv('SMTP_PASS');

    $mail->Port = getenv('SMTP_PORT') ?: 587;

    $mail->SMTPSecure =
        PHPMailer::ENCRYPTION_STARTTLS;

    $mail->CharSet = 'UTF-8';

    $mail->setFrom(
        getenv('SMTP_USER'),
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
            Ваш аккаунт успешно зарегистрирован
            в интернет-магазине «Легкий Шаг».
        </p>

        <p>
            Теперь вы можете войти
            в личный кабинет и оформлять заказы.
        </p>

        <br>

        <p>
            С уважением,<br>
            команда «Легкий Шаг»
        </p>
    ";

    $mail->send();

} catch (Exception $e) {

    error_log(
        'Mail error: ' . $mail->ErrorInfo
    );
}
header('Location: /account.php');
exit;
