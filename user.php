<?php
session_start();
date_default_timezone_set('Asia/Krasnoyarsk');

// Проверка авторизации
if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];

// Подключение к БД
try {
    $pdo = new PDO(
        'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
        'root',
        'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Создаём таблицы, если их нет (безопасно)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(20) NOT NULL UNIQUE,
        user_login VARCHAR(50) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        payment_method VARCHAR(50),
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (PDOException $e) {
    die('Ошибка создания таблиц: ' . $e->getMessage());
}

// Получаем данные пользователя
$userStmt = $pdo->prepare("SELECT id, NAME FROM regisrtation WHERE NAME = ? LIMIT 1");
$userStmt->execute([$username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    setcookie('login', '', time() - 3600, '/');
    header('Location: account.php');
    exit;
}

$user_id = $user['id'];
$isAdmin = ($username === 'admin');

// Избранное
try {
    $wishlistStmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $wishlistStmt->execute([$user_id]);
    $wishlistCount = $wishlistStmt->fetchColumn();
} catch (PDOException $e) {
    $wishlistCount = 0;
}

// Заказы пользователя
try {
    $orderCountStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_login = ?");
    $orderCountStmt->execute([$username]);
    $orderCount = $orderCountStmt->fetchColumn();

    $lastOrderStmt = $pdo->prepare("SELECT order_date FROM orders WHERE user_login = ? ORDER BY order_date DESC LIMIT 1");
    $lastOrderStmt->execute([$username]);
    $lastOrder = $lastOrderStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $orderCount = 0;
    $lastOrder = null;
}

// Админ-статистика
if ($isAdmin) {
    $usersCount = $pdo->query("SELECT COUNT(*) FROM regisrtation")->fetchColumn();
    $productsCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $revenue = $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $recentOrders = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Личный кабинет | Легкий Шаг</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .admin-panel {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .admin-stats {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .admin-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        text-align: center;
        flex: 1 1 200px;
    }
    .admin-card i {
        font-size: 28px;
        color: #ff523b;
        margin-bottom: 10px;
    }
    .profile-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-top: 20px;
    }
    .user-menu {
        display: flex;
        gap: 15px;
        margin: 20px 0;
    }
    .user-menu a {
        background: #ff523b;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
    }
    .user-menu a:hover {
        background: #e8432e;
    }
</style>
</head>
<body>

<div class="user-container">

    <div class="user-header">
        <h1>Добро пожаловать, <?= htmlspecialchars($username) ?> 👋</h1>
        <a href="logout.php" class="logout-btn">Выйти</a>
    </div>

    <!-- Меню кабинета -->
    <div class="user-menu">
        <a href="user.php">Профиль</a>
        <a href="user_orders.php">Мои заказы</a>
        <a href="wishlist.php">Избранное (<?= $wishlistCount ?>)</a>
        <a href="user_settings.php">Настройки</a>
    </div>

    <?php if ($isAdmin): ?>
    <div class="admin-panel">
        <h2>Панель администратора</h2>
        <div class="admin-stats">
            <div class="admin-card">
                <i class="fa fa-users"></i>
                <h3><?= $usersCount ?></h3>
                <p>Пользователей</p>
            </div>
            <div class="admin-card">
                <i class="fa fa-shopping-bag"></i>
                <h3><?= $productsCount ?></h3>
                <p>Товаров</p>
            </div>
            <div class="admin-card">
                <i class="fa fa-shopping-cart"></i>
                <h3><?= $ordersCount ?></h3>
                <p>Заказов</p>
            </div>
            <div class="admin-card">
                <i class="fa fa-rub"></i>
                <h3><?= number_format($revenue, 0, '.', ' ') ?> ₽</h3>
                <p>Выручка</p>
            </div>
        </div>

        <div class="profile-card">
            <h3>Последние заказы</h3>
            <?php foreach ($recentOrders as $order): ?>
                <p>
                    #<?= $order['order_id'] ?>
                    — <?= htmlspecialchars($order['user_login']) ?>
                    — <?= number_format($order['total'], 0, '.', ' ') ?> ₽
                </p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="profile-info">
        <div class="profile-card">
            <h3>Личная информация</h3>
            <p><strong>Имя:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>Избранное:</strong> <?= $wishlistCount ?></p>
            <p><strong>Заказов:</strong> <?= $orderCount ?></p>
            <p><strong>Последний заказ:</strong>
                <?= $lastOrder
                    ? date('d.m.Y H:i', strtotime($lastOrder['order_date']))
                    : 'Нет заказов'
                ?>
            </p>
        </div>
    </div>

</div>

</body>
</html>
