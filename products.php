<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
Сортировка
*/

$sort = $_GET['sort'] ?? 'new';

switch ($sort) {

    case 'price_asc':
        $sql = "
            SELECT *
            FROM products
            ORDER BY CAST(REPLACE(price, ',', '') AS DECIMAL(10,2)) ASC
        ";
        break;

    case 'price_desc':
        $sql = "
            SELECT *
            FROM products
            ORDER BY CAST(REPLACE(price, ',', '') AS DECIMAL(10,2)) DESC
        ";
        break;

    case 'rating':
        $sql = "
            SELECT *
            FROM products
            ORDER BY rating DESC
        ";
        break;

    case 'popular':
        $sql = "
            SELECT *
            FROM products
            ORDER BY views DESC
        ";
        break;

    default:
        $sql = "
            SELECT *
            FROM products
            ORDER BY id DESC
        ";
}

$stmt = $pdo->query($sql);

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
// добавляем в вишлист
$wishlist_ids = [];

if (isset($_COOKIE['login'])) {

    $username = $_COOKIE['login'];

    $userStmt = $pdo->prepare("
        SELECT id
        FROM regisrtation
        WHERE NAME = ?
    ");

    $userStmt->execute([$username]);

    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $wishStmt = $pdo->prepare("
            SELECT product_id
            FROM wishlist
            WHERE user_id = ?
        ");

        $wishStmt->execute([$user['id']]);

        $wishlist_ids = $wishStmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары - Легкий шаг</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  
</head>
    <style>
        .col-4 > a:first-child {
    display: block;
    height: 250px;           /* высота, которую вы считаете подходящей */
    overflow: hidden;        /* обрезаем всё, что выходит за границы */
}

.col-4 > a:first-child img {
    width: 100%;
    height: 100%;
    object-fit: cover;     
}
    </style>
<body>
    <div class = "header">
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
                            if(isset($_COOKIE['login']) ) {
                                echo'<li><a href="/user.php"</a>Кабинет пользователя</li>';

                            }
                            else{
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

    <div class="small-container">
        <div class="row row-2">
            <h2>Все товары</h2>
             <select id="sortSelect" onchange="changeSort(this.value)">
             
                <option value="new" <?= $sort == 'new' ? 'selected' : '' ?>>
                Новинки
                </option>
                
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>
                Сначала дешевле
                </option>
                
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>
                Сначала дороже
                </option>
                
                <option value="popular" <?= $sort == 'popular' ? 'selected' : '' ?>>
                По популярности
                </option>
                
                <option value="rating" <?= $sort == 'rating' ? 'selected' : '' ?>>
                По рейтингу
                </option>
             
             </select>
        </div>

        <!-- Динамический вывод товаров -->
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-4">
                    <a href="products-details.php?id=<?= $product['id'] ?>">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>">
                    </a>
                    <a href="products-details.php?id=<?= $product['id'] ?>">
                        <h4><?= htmlspecialchars($product['item']) ?></h4>
            <?php
                        // Убираем всё, кроме цифр и точки, затем преобразуем в число
                        $clean_price = floatval(preg_replace('/[^0-9.]/', '', $product['price']));
                        // Форматируем и выводим с символом рубля
                        echo '<p>' . number_format($clean_price, 2, '.', ' ') . ' ₽</p>';
            ?>
                    </a>
                    <div class="rating">
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                        <i class="fa fa-star"></i>
                    </div>
                                            
                    <div class="product-actions">
                        <!-- Форма добавления в корзину -->
                        <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['item']) ?>">
                            <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                            <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image']) ?>">
                            <button type="submit" class="add-to-cart-btn">Добавить в корзину</button>
                        </form>

                        <!-- Кнопка избранного (вишлист) -->
                        <?php if (isset($_COOKIE['login'])): ?>
                            <button class="wishlist-btn <?= in_array($product['id'], $wishlist_ids) ? 'active' : '' ?>" 
                                    data-product-id="<?= $product['id'] ?>"
                                    data-product-name="<?= htmlspecialchars($product['item']) ?>"
                                    data-product-price="<?= $product['price'] ?>"
                                    data-product-image="<?= htmlspecialchars($product['image']) ?>">
                                <i class="fa <?= in_array($product['id'], $wishlist_ids) ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                            </button>
                        <?php else: ?>
                            <a href="account.php" class="wishlist-btn" title="Войдите, чтобы добавить в избранное">
                                <i class="fa fa-heart-o"></i>
                            </a>
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
            <p class="copyright">© 2026 Легкий Шаг. Все права защищены.</p>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        function menutoggle() {
            var MenuItems = document.getElementById("MenuItems");
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }
        
        // Инициализация меню
        document.addEventListener('DOMContentLoaded', function() {
            var MenuItems = document.getElementById("MenuItems");
            MenuItems.style.maxHeight = "0px";
        });
    </script>
</body>

</html>
<script>
 function changeSort(value) {
     window.location.href = 'products.php?sort=' + value;
 }
</script>
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

        // Обработка добавления в корзину через AJAX (чтобы страница не перезагружалась)
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

        // Обработка клика по кнопке избранного
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

        // Функция обновления счётчика корзины
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
    </script>
</body>
</html>
