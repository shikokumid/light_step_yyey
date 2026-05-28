<?php
session_start();

// Проверяем авторизацию
if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];

// Подключение к базе данных
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Получаем ID пользователя
$userStmt = $pdo->prepare("SELECT id FROM regisrtation WHERE NAME = ?");
$userStmt->execute([$username]);
$user = $userStmt->fetch();
$user_id = $user['id'];

// Получаем количество товаров в избранном
$wishlistCountStmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$wishlistCountStmt->execute([$user_id]);
$wishlistCount = $wishlistCountStmt->fetch()['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабинет пользователя - Легкий шаг</title>
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
                    <a href="index.php"><img src="images/logot.png" width="125px"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="products.php">Продукты</a></li>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="contact.php">Контакты</a></li>
                        <?php   
                        if(isset($_COOKIE['login'])) {
                            echo '<li><a href="/user.php" class="active">Кабинет пользователя</a></li>';
                        } else {
                            echo '<li><a href="account.php">Аккаунт</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
                <a href="cart.php" class="cart-link">
                    <img src="images/cart.png" width="30px" height="30px">
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
        </div>
    </div>

    <div class="user-container">
        <div class="user-header">
            <div>
                <h1 class="user-welcome">Добро пожаловать, <?php echo htmlspecialchars($username); ?>!</h1>
                <p class="user-email">Управляйте своей учетной записью в Легком шаге</p>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fa fa-sign-out"></i> Выйти
            </a>
        </div>
        
        <div class="user-menu">
            <a href="user.php" class="user-menu-item active">
                <i class="fa fa-user"></i> Профиль
            </a>
            <a href="user_orders.php" class="user-menu-item">
                <i class="fa fa-shopping-bag"></i> Мои заказы
            </a>
            <a href="wishlist.php" class="user-menu-item">
                <i class="fa fa-heart"></i> Избранное
                <?php if ($wishlistCount > 0): ?>
                    <span class="wishlist-count"><?php echo $wishlistCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="user_settings.php" class="user-menu-item">
                <i class="fa fa-cog"></i> Настройки
            </a>
        </div>
        
        <div class="user-content">
            <h2 class="user-section-title">Личный кабинет</h2>
            
            <div class="profile-info">
                <div class="profile-card">
                    <h3><i class="fa fa-user-circle"></i> Личная информация</h3>
                    <p><strong>Имя пользователя:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p><strong>Статус аккаунта:</strong> Активен</p>
                    <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y'); ?></p>
                    <p><strong>Количество заказов:</strong> 0</p>
                </div>
                
                <div class="profile-card">
                    <h3><i class="fa fa-shopping-cart"></i> Корзина и заказы</h3>
                    <p><strong>Товаров в корзине:</strong> 
                        <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
                    </p>
                    <p><strong>Товаров в избранном:</strong> <?php echo $wishlistCount; ?></p>
                    <p><strong>Всего заказов:</strong> 0</p>
                    <p><strong>Последний заказ:</strong> Нет заказов</p>
                </div>
                
                <div class="profile-card">
                    <h3><i class="fa fa-star"></i> Активность</h3>
                    <p><strong>Статус покупателя:</strong> Новый клиент</p>
                    <p><strong>Бонусные баллы:</strong> 0</p>
                    <p><strong>Скидка:</strong> 0%</p>
                    <p><strong>Рейтинг:</strong> ☆☆☆☆☆</p>
                </div>
            </div>
            
            <div style="margin-top: 30px;">
                <h3 style="color: var(--primary-color); margin-bottom: 15px;">Быстрые действия</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="products.php" class="logout-btn" style="background-color: var(--primary-color);">
                        <i class="fa fa-shopping-cart"></i> Продолжить покупки
                    </a>
                    <a href="cart.php" class="logout-btn" style="background-color: var(--light-accent); color: var(--text-dark);">
                        <i class="fa fa-shopping-basket"></i> Перейти в корзину
                    </a>
                    <a href="wishlist.php" class="logout-btn" style="background-color: var(--light-accent); color: var(--text-dark);">
                        <i class="fa fa-heart"></i> Перейти в избранное
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3>Скачайте наше приложение</h3>
                    <p>Скачайте приложение для Android и iOS.</p>
                    <div class="app-logo">
                        <img src="images/play-store.png" alt="">
                        <img src="images/app-store.png" alt="">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="images/logoi.png">
                    <p>Наша цель — сделать спорт доступным для всех.</p>
                </div>
                <div class="footer-col-3">
                    <h3>Полезные ссылки</h3>
                    <ul>
                        <li>Купоны</li>
                        <li>Блог</li>
                        <li>Политика возврата</li>
                        <li>Присоединяйтесь к партнёрской программе</li>
                    </ul>
                </div>
                <div class="footer-col-4">
                    <h3>Следите за нами</h3>
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
        // Функция для переключения меню
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
