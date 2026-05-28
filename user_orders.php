<?php
session_start();

if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Таблица orders (создаётся, если нет)
$pdo->exec("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) NOT NULL UNIQUE,
    user_login VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_login (user_login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_login = ? ORDER BY order_date DESC");
$stmt->execute([$username]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Счётчик избранного
$wishlistCount = 0;
$wishStmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = (SELECT id FROM regisrtation WHERE NAME = ?)");
$wishStmt->execute([$username]);
$wishlistCount = $wishStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы | Легкий шаг</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="index.php"><img src="images/logot.png" width="125px" alt="Логотип"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="products.php">Продукты</a></li>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="contact.php">Контакты</a></li>
                        <?php if (isset($_COOKIE['login'])): ?>
                            <li><a href="/user.php">Кабинет пользователя</a></li>
                        <?php else: ?>
                            <li><a href="account.php">Аккаунт</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="cart.php" class="cart-link">
                    <img src="images/cart.png" width="30px" height="30px" alt="Корзина">
                    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onclick="menutoggle()" alt="Меню">
            </div>
        </div>
    </div>

    <div class="user-container">
        <div class="user-header">
            <div>
                <h1 class="user-welcome">Мои заказы</h1>
                <p class="user-email">История ваших покупок в Легком шаге</p>
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
                <?php if ($wishlistCount > 0): ?>
                    <span class="wishlist-count"><?= $wishlistCount ?></span>
                <?php endif; ?>
            </a>
            <a href="user_settings.php" class="user-menu-item">
                <i class="fa fa-cog"></i> Настройки
            </a>
        </div>

        <div class="user-content">
            <h2 class="user-section-title">История заказов</h2>

            <?php if (!empty($orders)): ?>
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
                                    $status_text = 'Неизвестно';
                            }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></td>
                                <!-- РЕАЛЬНАЯ ЦЕНА В РУБЛЯХ (total уже в рублях после исправления checkout/save_order) -->
                                <td>₽<?= number_format($order['total'], 2, '.', ' ') ?></td>
                                <td>
                                    <span class="order-status <?= $status_class ?>"><?= $status_text ?></span>
                                </td>
                                <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                <td>
                                    <a href="order_details.php?order_id=<?= urlencode($order['order_id']) ?>" class="view-order-btn">
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
                        <img src="images/play-store.png" alt="Google Play">
                        <img src="images/app-store.png" alt="App Store">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="images/logoi.png" alt="Логотип">
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
            <p class="copyright">© 2026 Легкий Шаг. Все права защищены.</p>
        </div>
    </div>

    <script>
        var MenuItems = document.getElementById("MenuItems");
        MenuItems.style.maxHeight = "0px";
        function menutoggle() {
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }

        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.text())
                .then(count => {
                    const cartCountSpan = document.querySelector('.cart-count');
                    if (parseInt(count) > 0) {
                        if (cartCountSpan) {
                            cartCountSpan.textContent = count;
                        } else {
                            const cartLink = document.querySelector('.cart-link');
                            const newSpan = document.createElement('span');
                            newSpan.className = 'cart-count';
                            newSpan.textContent = count;
                            cartLink.appendChild(newSpan);
                        }
                    } else {
                        if (cartCountSpan) cartCountSpan.remove();
                    }
                });
        }
    </script>
</body>
</html>
