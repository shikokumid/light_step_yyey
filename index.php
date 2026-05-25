<?php
session_start();

// Подключаемся к PostgreSQL
try {
    $pdo = new PDO(
        'pgsql:host=dpg-d8abfkog4nts73dbvulg-a;dbname=diplom_xi6u;port=5432',
        'shikoku',
        'qSTMZdU1idwuWso7eTiePld8lbkqgHjd'
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем последние товары
$products = [];
try {
    $sql = 'SELECT * FROM products ORDER BY id DESC';
    $query = $pdo->prepare($sql);
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Оставим массив пустым, если ошибка
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Легкий шаг | Интернет-магазин</title>
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
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
            <div class="row">
                <div class="col-2">
                    <h1>Привнесите новый стиль в вашу тренировку!</h1>
                    <p>Успех — это не всегда о величии. Это о постоянстве. Постоянный труд приводит к успеху. Великолепие придет.</p>
                    <a href="products.php" class="btn">Исследовать сейчас &#8594;</a>
                </div>
                <div class="col-2">
                    <img src="images/image1.png">
                </div>
            </div>
        </div>
    </div>

    <!-- категории -->
    <div class="categories">
        <div class="small-container">
            <div class="row">
                <div class="col-3"><img src="images/category-1.jpg"></div>
                <div class="col-3"><img src="images/category-2.jpg"></div>
                <div class="col-3"><img src="images/category-3 (2).jpg"></div>
            </div>
        </div>
    </div>

    <!-- Избранные товары (статика) -->
    <div class="small-container">
        <h2 class="title">Избранные товары</h2>
        <div class="row">
            <div class="col-4">
                <a href="products-details.php"><img src="images/product-11.jpg"></a>
                <a href="products-details.php"><h4>Спортивные кроссовки Downshifter</h4></a>
                <div class="rating">
                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i><i class="fa fa-star-o"></i>
                </div>
                <p>5000₽</p>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="1">
                    <input type="hidden" name="product_name" value="Спортивные кроссовки Downshifter">
                    <input type="hidden" name="product_price" value="5000.00">
                    <input type="hidden" name="product_image" value="product-11.jpg">
                    <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                </form>
            </div>
            <div class="col-4">
                <a href="products-details.php"><img src="images/product-2.jpg"></a>
                <h4>Беговые кроссовки с шнуровкой</h4>
                <div class="rating">
                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-half-o"></i>
                </div>
                <p>4950₽</p>
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="2">
                    <input type="hidden" name="product_name" value="Беговые кроссовки с шнуровкой">
                    <input type="hidden" name="product_price" value="4950.00">
                    <input type="hidden" name="product_image" value="product-2.jpg">
                    <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                </form>
            </div>
        </div>

        <!-- Последние товары из БД -->
        <h2 class="title">Последние товары</h2>
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $el): ?>
                    <div class="col-4">
                        <a href="products-details.php"><img src="images/<?= htmlspecialchars($el['image']) ?>"></a>
                        <h4><?= htmlspecialchars($el['item']) ?></h4>
                        <div class="rating">
                            <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
                        </div>
                        <p>₽<?= number_format((float)$el['price'], 2) ?></p>
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $el['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($el['item']) ?>">
                            <input type="hidden" name="product_price" value="<?= $el['price'] ?>">
                            <input type="hidden" name="product_image" value="<?= htmlspecialchars($el['image']) ?>">
                            <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#999;">Товары временно недоступны.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- предложение -->
    <div class="full-width-offer">
        <div class="row">
            <div class="col-2">
                <img src="images/image1.png" class="offer-img" style="max-width: 100%;">
            </div>
            <div class="col-2">
                <p>Эксклюзивно только в Легком шаге</p>
                <h1>Спортивные кроссовки</h1>
                <small>Купите новейшие коллекции спортивных кроссовок онлайн в Легком шаге по лучшим ценам от таких брендов, как Adidas, Nike, Puma, Asics и Sparx.</small><br>
                <a href="products.php" class="btn">Смотреть товары &#8594;</a>
            </div>
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
            <p class="copyright">© 2026 Легкий Шаг. Все права защищены.</p>
        </div>
    </div>

    <!-- скрипты -->
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

        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        showMessage('Ошибка при добавлении', 'error');
                        updateCartCount();
                    } else {
                        showMessage('Товар добавлен в корзину', 'success');
                    }
                });
            });
        });

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

        function showMessage(text, type) {
            const message = document.createElement('div');
            message.className = 'cart-message';
            message.style.position = 'fixed';
            message.style.top = '20px';
            message.style.right = '20px';
            message.style.padding = '15px 25px';
            message.style.borderRadius = '5px';
            message.style.color = 'white';
            message.style.fontWeight = '600';
            message.style.zIndex = '1000';
            message.style.animation = 'slideIn 0.5s, fadeOut 0.5s 2.5s forwards';
            message.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            message.textContent = text;
            document.body.appendChild(message);
            setTimeout(() => message.remove(), 3000);
        }
    </script>
</body>
</html>
