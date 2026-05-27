<?php
// auth.php - авторизация пользователя

// Подключаем файл с подключением к базе данных
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 
// Получаем данные из формы
$login = trim(filter_var($_POST['login'], FILTER_SANITIZE_SPECIAL_CHARS));
$password = trim($_POST['password']); // Пароль не фильтруем для хеширования

// Валидация
if(strlen($login) < 2){
    echo'Логин должен содержать минимум 2 символа';
    exit;
}

if(strlen($password) < 2){
    echo'Пароль должен содержать минимум 2 символа';
    exit;
}

// Соль для пароля (храните в отдельном конфигурационном файле в реальном проекте!)
$salt = "lasdl;askd;ak213";

// Хешируем пароль с солью
$hashed_password = md5($salt . $password);

// Ищем пользователя в базе данных
$sql = 'SELECT id FROM regisrtation WHERE NAME = ? AND PASSWORD = ?';
$query = $pdo->prepare($sql);
$query->execute([$login, $hashed_password]);

// Проверяем, найден ли пользователь
if($query->rowCount() == 0){
    echo 'Неверный логин или пароль';
    exit;
}
else{
    setcookie('login', $login, time()+3600 * 24 * 30, '/');
    header('Location: /user.php');
}
?>
