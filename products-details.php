<?php
session_start();

// Подключение к БД
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Получаем ID товара из URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    header('Location: products.php');
    exit;
}

// Запрашиваем товар из БД
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header('Location: products.php');
    exit;
}

// Для связанных товаров (например, другие товары, кроме текущего)
$relatedStmt = $pdo->prepare("SELECT * FROM products WHERE id != ? ORDER BY id DESC LIMIT 4");
$relatedStmt->execute([$product_id]);
$relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

// Если пользователь авторизован, получим список его избранных товаров
$wishlist_ids = [];
if (isset($_COOKIE['login'])) {
    $username = $_COOKIE['login'];
    $userStmt = $pdo->prepare("SELECT id FROM regisrtation WHERE NAME = ?");
    $userStmt->execute([$username]);
    $user = $userStmt->fetch();
    if ($user) {
        $user_id = $user['id'];
        $wishStmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
        $wishStmt->execute([$user_id]);
        $wishlist_ids = $wishStmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

// Очищаем цену от символов (если хранится как "$48.00")
$clean_price = floatval(preg_replace('/[^0-9.]/', '', $product['price']));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['item']) ?> - Легкий шаг</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">я
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
                        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
        </div>
    </div>

    <!-- Детальная информация о товаре -->
    <div class="small-container single-product">
        <div class="row">
            <div class="col-2">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" width="100%" id="productImg">
                <!-- Галерея миниатюр (можно расширить, если есть несколько изображений) -->
                <div class="small-img-row">
                    <div class="small-img-col">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" width="100%" class="small-img">
                    </div>
                    <!-- Дополнительные миниатюры можно добавить, если в БД есть дополнительные поля -->
                    <div class="small-img-col">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" width="100%" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" width="100%" class="small-img">
                    </div>
                    <div class="small-img-col">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" width="100%" class="small-img">
                    </div>
                </div>
            </div>
            <div class="col-2">
                <p>Главная / Обувь</p>
                <h1><?= htmlspecialchars($product['item']) ?></h1>
                <h4>₽<?= number_format($clean_price, 2) ?></h4>

                <!-- Таблица выбора размера -->
                <table class="size-table">
                    <tr>
                        <td>Select Size</td>
                        <td>30</td>
                        <td>33</td>
                        <td>35</td>
                        <td>37</td>
                        <td>39</td>
                    </tr>
                </table>

                <!-- Форма добавления в корзину с выбором количества -->
                <form action="add_to_cart.php" method="POST" class="add-to-cart-form" id="detailAddToCartForm">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['item']) ?>">
                    <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image']) ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit" class="btn">Добавить в корзину</button>
                </form>

                <!-- Кнопка избранного -->
                <?php if (isset($_COOKIE['login'])): ?>
                    <button class="wishlist-btn <?= in_array($product['id'], $wishlist_ids) ? 'active' : '' ?>" 
                            data-product-id="<?= $product['id'] ?>"
                            style="margin-top: 10px;">
                        <i class="fa <?= in_array($product['id'], $wishlist_ids) ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                    </button>
                <?php else: ?>
                    <a href="account.php" class="wishlist-btn" title="Войдите, чтобы добавить в избранное" style="display: inline-block; margin-top: 10px;">
                        <i class="fa fa-heart-o"></i>
                    </a>
                <?php endif; ?>

                <h3>Детали товара <i class="fa fa-indent"></i></h3>
                <br>
                <p><?= htmlspecialchars($product['item']) ?> — отличный выбор для активного отдыха и тренировок. Высокое качество материалов, удобная посадка и современный дизайн делают эту модель популярной среди спортсменов.</p>
            </div>
        </div>
    </div>

    <!-- Связанные товары -->
    <div class="small-container">
        <div class="row row-2">
            <h2>Похожие товары</h2>
            <a href="products.php"><p>Смотреть все</p></a>
        </div>
        <div class="row">
            <?php foreach ($relatedProducts as $rel): ?>
                <?php
                $rel_price = floatval(preg_replace('/[^0-9.]/', '', $rel['price']));
                $in_wishlist = in_array($rel['id'], $wishlist_ids);
                ?>
                <div class="col-4">
                    <a href="products-details.php?id=<?= $rel['id'] ?>">
                        <img src="images/<?= htmlspecialchars($rel['image']) ?>">
                    </a>
                    <a href="products-details.php?id=<?= $rel['id'] ?>">
                        <h4><?= htmlspecialchars($rel['item']) ?></h4>
                    </a>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star-half-o"></i>
                        <i class="fa fa-star-o"></i>
                    </div>
                    <p>₽<?= number_format($rel_price, 2) ?></p>
                    <div class="product-actions">
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $rel['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($rel['item']) ?>">
                            <input type="hidden" name="product_price" value="<?= $rel['price'] ?>">
                            <input type="hidden" name="product_image" value="<?= htmlspecialchars($rel['image']) ?>">
                            <button type="submit" class="add-to-cart-btn">В корзину</button>
                        </form>
                        <?php if (isset($_COOKIE['login'])): ?>
                            <button class="wishlist-btn <?= $in_wishlist ? 'active' : '' ?>" data-product-id="<?= $rel['id'] ?>">
                                <i class="fa <?= $in_wishlist ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                            </button>
                        <?php else: ?>
                            <a href="account.php" class="wishlist-btn"><i class="fa fa-heart-o"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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

        // Обработка добавления в корзину (AJAX)
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
                        showMessage('Успешно добавлен', 'error');
                    }
                })
                .catch(() => showMessage('Ошибка соединения', 'error'));
            });
        });

        // Обработка кнопок избранного
        document.querySelectorAll('.wishlist-btn[data-product-id]').forEach(btn => {
            btn.addEventListener('click', function() {
                const productId = this.dataset.productId;
                const icon = this.querySelector('i');
                const isActive = this.classList.contains('active');

                fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'added') {
                        this.classList.add('active');
                        icon.classList.remove('fa-heart-o');
                        icon.classList.add('fa-heart');
                        showMessage('Добавлено в избранное', 'success');
                    } else if (data === 'removed') {
                        this.classList.remove('active');
                        icon.classList.remove('fa-heart');
                        icon.classList.add('fa-heart-o');
                        showMessage('Удалено из избранного', 'success');
                    } else if (data === 'not_logged_in') {
                        window.location.href = 'account.php';
                    } else {
                        showMessage('Ошибка', 'error');
                    }
                });
            });
        });

        // Обновление счётчика корзины
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

        // Всплывающее сообщение
        function showMessage(text, type) {
            const msg = document.createElement('div');
            msg.className = 'cart-message';
            msg.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
            msg.textContent = text;
            document.body.appendChild(msg);
            setTimeout(() => msg.remove(), 3000);
        }

        // Галерея: переключение главного изображения по миниатюре
        var productImg = document.getElementById("productImg");
        var smallImgs = document.getElementsByClassName("small-img");
        if (smallImgs.length > 0) {
            for (let i = 0; i < smallImgs.length; i++) {
                smallImgs[i].onclick = function() {
                    productImg.src = smallImgs[i].src;
                };
            }
        }
    </script>
</body>
</html>
