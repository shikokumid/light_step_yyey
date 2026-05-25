<?php
// user_orders.php
session_start();

// Проверяем авторизацию
if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Создаем таблицу orders, если ее нет
$createTableSQL = "
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) NOT NULL UNIQUE,
    user_login VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_login (user_login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$pdo->exec($createTableSQL);

// Получаем заказы пользователя
$sql = "SELECT * FROM orders WHERE user_login = ? ORDER BY order_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
   <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>Легкий шаг | Интернет-магазин</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="index.php"><img src="images/logot.png" width="125px"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="products.php">Продукты</a></li>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="">Контакты</a></li>
                          <?php   
                            if(isset($_COOKIE['login']) ) {
                                echo'<li><a href="/user.php"</a>Кабинет пользователя</li>';

                            }
                            else{
                                echo '<li><a href="account.php">Аккаунт</a></li>';
                            }
                         ?>
                    </ul>
                </nav>
                <a href="cart.php"><img src="images/cart.png" width="30px" height="30px"></a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
            
        </div>
    </div>
    
    <div class="user-container">
        <div class="user-header">
            <div>
                <h1 class="user-welcome">Мои заказы</h1>
                <p class="user-email">История ваших покупок в RedStore</p>
            </div>
            <a href="user.php" class="logout-btn">
                <i class="fa fa-arrow-left"></i> Назад в кабинет
            </a>
        </div>
        
        <div class="user-menu">
            <a href="user.php" class="user-menu-item">
                <i class="fa fa-user"></i> Профиль
            </a>
            <a href="user_orders.php" class="user-menu-item active">
                <i class="fa fa-shopping-bag"></i> Мои заказы
            </a>
            <a href="wishlist.php" class="user-menu-item">
                <i class="fa fa-heart"></i> Избранное
            </a>
            <a href="user_settings.php" class="user-menu-item">
                <i class="fa fa-cog"></i> Настройки
            </a>
        </div>
        
        <div class="user-content">
            <h2 class="user-section-title">История заказов</h2>
            
            <?php if (count($orders) > 0): ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>№ заказа</th>
                            <th>Дата</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Способ оплаты</th>
                            <th>Детали</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php 
                            // Определяем класс статуса
                            $status_class = '';
                            $status_text = '';
                            switch ($order['status']) {
                                case 'pending':
                                    $status_class = 'status-pending';
                                    $status_text = 'Ожидание';
                                    break;
                                case 'processing':
                                    $status_class = 'status-processing';
                                    $status_text = 'В обработке';
                                    break;
                                case 'completed':
                                    $status_class = 'status-completed';
                                    $status_text = 'Завершен';
                                    break;
                                case 'cancelled':
                                    $status_class = 'status-cancelled';
                                    $status_text = 'Отменен';
                                    break;
                                default:
                                    $status_class = 'status-pending';
                                    $status_text = $order['status'];
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>$<?php echo number_format($order['total'], 2); ?></td>
                                <td>
                                    <span class="order-status <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td>
                                    <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="view-order-btn">
                                        <i class="fa fa-eye"></i> Подробнее
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fa fa-shopping-bag" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3>У вас еще нет заказов</h3>
                    <p>Совершите свой первый заказ!</p>
                    <a href="products.php" class="logout-btn" style="margin-top: 20px;">
                        <i class="fa fa-shopping-cart"></i> Перейти к товарам
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
        <!-- Футер -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3>Скачайте наше приложение</h3>
                    <p>Доступно для iOS и Android</p>
                    <div class="app-logo">
                        <img src="images/play-store.png">
                        <img src="images/app-store.png">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="images/logoi.png">
                    <p>Наша цель — сделать спорт доступным и вдохновляющим для каждого</p>
                </div>
                <div class="footer-col-3">
                    <h3>Полезные ссылки</h3>
                    <ul>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="blog.php">Блог</a></li>
                        <li><a href="return-policy.php">Возврат</a></li>
                        <li><a href="contact.php">Контакты</a></li>
                    </ul>
                </div>
                <div class="footer-col-4">
                    <h3>Социальные сети</h3>
                    <ul>
                        <li><a href="https://web.telegram.org"><i class="fa fa-telegram"></i> Telegram</a></li>
                        <li><a href="https://m.vk.com/"><i class="fa fa-vk"></i> Вконтакте</a></li>
                        <li><a href="https://max.ru/"><i class="fa fa-max"></i> Max</a></li>
                        <li><a href="https://www.youtube.com/"><i class="fa fa-youtube"></i> YouTube</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">© 2023 Легкий Шаг. Все права защищены.</p>
        </div>
    </div>
</body>
</html>