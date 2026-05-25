<?php
// logout.php
session_start();

// Удаляем куки
if (isset($_COOKIE['login'])) {
    setcookie('login', '', time() - 3600, '/');
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем на главную
header('Location: index.php');
exit;
?>