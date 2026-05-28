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
if (!$user) {
    // Если пользователь не найден в БД, перенаправляем на страницу входа
    header('Location: account.php');
    exit;
}
$user_id = $user['id'];

// Получаем количество товаров в избранном (для отображения в меню)
$wishlistCountStmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
$wishlistCountStmt->execute([$user_id]);
$wishlistCount = $wishlistCountStmt->fetch()['count'];

// Получаем избранные товары
$wishlistStmt = $pdo->prepare("
    SELECT p.* 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    WHERE w.user_id = ? 
    ORDER BY w.created_at DESC
");
$wishlistStmt->execute([$user_id]);
$wishlistItems = $wishlistStmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное - Redstore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   
</head>
<body>
    <!-- Шапка (navbar) -->
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="index.php"><img src="images/logot.png" width="125px"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="products.php">Товары</a></li>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="contact.php">Контакты</a></li>
                        <?php   
                        if(isset($_COOKIE['login'])) {
                            echo '<li><a href="user.php">Кабинет пользователя</a></li>';
                        } else {
                            echo '<li><a href="account.php">Аккаунт</a></li>';
                        }
                        ?>
                    </ul>
                </nav>
                <a href="cart.php" class="cart-link">
                    <img src="images/cart.png" width="30px" height="30px">
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
        </div>
    </div>

    <!-- Основной контейнер -->
    <div class="user-container">
        <div class="user-header">
            <div>
                <h1 class="user-welcome">Избранное</h1>
                <p class="user-email">Сохраненные товары <?php echo htmlspecialchars($username); ?></p>
            </div>
            <a href="user.php" class="logout-btn">
                <i class="fa fa-arrow-left"></i> Назад в кабинет
            </a>
        </div>
        
        <!-- Меню пользователя (можно оставить как ссылки на другие разделы) -->
        <div class="user-menu">
            <a href="user.php" class="user-menu-item">
                <i class="fa fa-user"></i> Профиль
            </a>
            <a href="user_orders.php" class="user-menu-item">
                <i class="fa fa-shopping-bag"></i> Мои заказы
            </a>
            <a href="wishlist.php" class="user-menu-item active">
                <i class="fa fa-heart"></i> Избранное
                <?php if ($wishlistCount > 0): ?>
                    <span class="wishlist-count-badge"><?php echo $wishlistCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="user_settings.php" class="user-menu-item">
                <i class="fa fa-cog"></i> Настройки
            </a>
        </div>
        
        <!-- Содержимое избранного -->
        <div class="user-content">
            <h2 class="user-section-title">Избранные товары (<?php echo $wishlistCount; ?>)</h2>
            
            <?php if (count($wishlistItems) > 0): ?>
                <div class="wishlist-grid">
                    <?php foreach ($wishlistItems as $item): 
                        // Очищаем цену от лишних символов, если необходимо
                        $clean_price = floatval(preg_replace('/[^0-9.]/', '', $item['price']));
                    ?>
                        <div class="wishlist-item" data-product-id="<?php echo $item['id']; ?>">
                            <a href="products-details.php?id=<?php echo $item['id']; ?>">
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['item']); ?>">
                            </a>
                            <div class="wishlist-item-content">
                                <h3 class="wishlist-item-title">
                                    <a href="products-details.php?id=<?php echo $item['id']; ?>" style="color: var(--text-dark); text-decoration: none;">
                                        <?php echo htmlspecialchars($item['item']); ?>
                                    </a>
                                </h3>
                                <div class="wishlist-item-price">₽<?php echo number_format($clean_price, 2); ?></div>
                                <div class="wishlist-actions">
                                    <button class="wishlist-add-cart-btn add-to-cart-from-wishlist" 
                                            data-product-id="<?php echo $item['id']; ?>"
                                            data-product-name="<?php echo htmlspecialchars($item['item']); ?>"
                                            data-product-price="<?php echo $clean_price; ?>"
                                            data-product-image="<?php echo htmlspecialchars($item['image']); ?>">
                                        <i class="fa fa-shopping-cart"></i> В корзину
                                    </button>
                                    <button class="wishlist-remove-btn remove-from-wishlist" data-product-id="<?php echo $item['id']; ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-wishlist">
                    <i class="fa fa-heart"></i>
                    <h3>Ваш список избранного пуст</h3>
                    <p>Добавляйте товары в избранное, чтобы не потерять их!</p>
                    <a href="products.php" class="logout-btn" style="margin-top: 20px;">
                        <i class="fa fa-shopping-cart"></i> Перейти к товарам
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Подвал -->
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
    
    <!-- Скрипты -->
    <script>
        // Переключение мобильного меню
        var MenuItems = document.getElementById("MenuItems");
        MenuItems.style.maxHeight = "0px";
        
        function menutoggle() {
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }
        
        // Удаление из избранного
        document.querySelectorAll('.remove-from-wishlist').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const wishlistItem = this.closest('.wishlist-item');
                
                // Отправляем запрос на удаление
                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'removed') {
                        // Удаляем элемент из DOM
                        wishlistItem.style.opacity = '0';
                        wishlistItem.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            wishlistItem.remove();
                            // Обновляем счетчик избранного и заголовок
                            updateWishlistCount();
                        }, 300);
                    } else if (data === 'not_logged_in') {
                        alert('Пожалуйста, войдите в систему');
                        window.location.href = 'account.php';
                    } else {
                        showMessage('Ошибка при удалении', 'error');
                    }
                });
            });
        });
        
        // Добавление в корзину из избранного
        document.querySelectorAll('.add-to-cart-from-wishlist').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const productPrice = this.getAttribute('data-product-price');
                const productImage = this.getAttribute('data-product-image');
                
                // Отправляем запрос на добавление в корзину
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_price=${productPrice}&product_image=${encodeURIComponent(productImage)}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        showMessage('Товар добавлен в корзину!', 'success');
                        // Обновляем счетчик корзины в шапке
                        updateCartCount();
                    } else {
                        showMessage('Товар добавлен в корзину', 'error');
                    }
                });
            });
        });
        
        // Функция обновления счетчика избранного
        function updateWishlistCount() {
            const wishlistCountElement = document.querySelector('.wishlist-count-badge');
            const currentCount = parseInt(wishlistCountElement ? wishlistCountElement.textContent : 0);
            const newCount = Math.max(0, currentCount - 1);
            
            if (wishlistCountElement) {
                if (newCount > 0) {
                    wishlistCountElement.textContent = newCount;
                } else {
                    wishlistCountElement.remove();
                }
            }
            
            // Обновляем заголовок
            const wishlistTitle = document.querySelector('.user-section-title');
            if (wishlistTitle) {
                const match = wishlistTitle.textContent.match(/(\d+)/);
                if (match) {
                    wishlistTitle.textContent = wishlistTitle.textContent.replace(match[0], newCount);
                }
            }
            
            // Если товаров не осталось, показываем сообщение
            if (newCount === 0) {
                const wishlistGrid = document.querySelector('.wishlist-grid');
                if (wishlistGrid) {
                    wishlistGrid.innerHTML = `
                        <div class="no-wishlist" style="grid-column: 1 / -1; text-align: center; padding: 50px;">
                            <i class="fa fa-heart"></i>
                            <h3>Ваш список избранного пуст</h3>
                            <p>Добавляйте товары в избранное, чтобы не потерять их!</p>
                            <a href="products.php" class="logout-btn" style="margin-top: 20px;">
                                <i class="fa fa-shopping-cart"></i> Перейти к товарам
                            </a>
                        </div>
                    `;
                }
            }
        }
        
        // Функция обновления счетчика корзины
        function updateCartCount() {
            fetch('get_cart_count.php')
                .then(response => response.text())
                .then(count => {
                    const cartCountElement = document.querySelector('.cart-count');
                    if (parseInt(count) > 0) {
                        if (cartCountElement) {
                            cartCountElement.textContent = count;
                        } else {
                            const cartLink = document.querySelector('.cart-link');
                            if (cartLink) {
                                const countSpan = document.createElement('span');
                                countSpan.className = 'cart-count';
                                countSpan.textContent = count;
                                cartLink.appendChild(countSpan);
                            }
                        }
                    } else {
                        if (cartCountElement) {
                            cartCountElement.remove();
                        }
                    }
                });
        }
        
        // Функция для показа всплывающих сообщений
        function showMessage(text, type) {
            const message = document.createElement('div');
            message.className = 'cart-message';
            message.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 8px;
                background-color: green;
                font-weight: 600;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideIn 0.4s ease, fadeOut 0.4s 2.5s forwards;
                border: 1px solid #e0e0e0;
            `;

            
            if (type === 'success') {
                message.style.backgroundColor = '#4CAF50';
            } else {
                message.style.backgroundColor = '#f44336';
            }
            
            message.textContent = text;
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.remove();
            }, 3000);
        }
    </script>
    
    <!-- Стили для анимации сообщений -->
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</body>
</html>
