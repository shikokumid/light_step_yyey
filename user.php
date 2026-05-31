<?php
session_start();

date_default_timezone_set('Asia/Krasnoyarsk');

if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
|--------------------------------------------------------------------------
| Пользователь
|--------------------------------------------------------------------------
*/

$userStmt = $pdo->prepare("
    SELECT *
    FROM regisrtation
    WHERE NAME = ?
    LIMIT 1
");

$userStmt->execute([$username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    setcookie('login', '', time() - 3600, '/');
    header('Location: account.php');
    exit;
}

$user_id = $user['id'];

$isAdmin = ($username === 'admin');

/*
|--------------------------------------------------------------------------
| Избранное
|--------------------------------------------------------------------------
*/

$wishlistStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM wishlist
    WHERE user_id = ?
");

$wishlistStmt->execute([$user_id]);
$wishlistCount = $wishlistStmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| Заказы пользователя
|--------------------------------------------------------------------------
*/

$orderCountStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM orders
    WHERE user_login = ?
");

$orderCountStmt->execute([$username]);
$orderCount = $orderCountStmt->fetchColumn();

$lastOrderStmt = $pdo->prepare("
    SELECT order_date
    FROM orders
    WHERE user_login = ?
    ORDER BY order_date DESC
    LIMIT 1
");

$lastOrderStmt->execute([$username]);
$lastOrder = $lastOrderStmt->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Админ-панель
|--------------------------------------------------------------------------
*/

if ($isAdmin) {

    $usersCount = $pdo->query("
        SELECT COUNT(*)
        FROM regisrtation
    ")->fetchColumn();

    $productsCount = $pdo->query("
        SELECT COUNT(*)
        FROM products
    ")->fetchColumn();

    $ordersCount = $pdo->query("
        SELECT COUNT(*)
        FROM orders
    ")->fetchColumn();

    $revenue = $pdo->query("
        SELECT COALESCE(SUM(total),0)
        FROM orders
        WHERE status != 'cancelled'
    ")->fetchColumn();

    $recentOrders = $pdo->query("
        SELECT *
        FROM orders
        ORDER BY order_date DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Личный кабинет</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet"
href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<div class="user-container">

    <div class="user-header">
        <h1>
            Добро пожаловать,
            <?= htmlspecialchars($username) ?> 👋
        </h1>

        <a href="logout.php" class="logout-btn">
            Выйти
        </a>
    </div>

    <?php if($isAdmin): ?>

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

            <?php foreach($recentOrders as $order): ?>

                <p>
                    #<?= $order['order_id'] ?>
                    —
                    <?= $order['user_login'] ?>
                    —
                    <?= number_format($order['total'], 0, '.', ' ') ?> ₽
                </p>

            <?php endforeach; ?>

        </div>

    </div>

    <?php endif; ?>


    <div class="profile-info">

        <div class="profile-card">
            <h3>Личная информация</h3>

            <p>
                Имя:
                <?= htmlspecialchars($username) ?>
            </p>

            <p>
                Избранное:
                <?= $wishlistCount ?>
            </p>

            <p>
                Заказов:
                <?= $orderCount ?>
            </p>

            <p>
                Последний заказ:
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
</body>
</html>
