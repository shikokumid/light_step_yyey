<?php
// cart.php
session_start();

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - RedStore</title>
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
                            echo '<li><a href="/user.php">Кабинет пользователя</a></li>';
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
    
    <div class="cart-container">
        <h1 class="cart-title">Ваша корзина</h1>
        
        <?php if(empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <h2>Ваша корзина пуста</h2>
                <p>Добавьте товары из каталога</p>
                <a href="products.php" class="continue-shopping">Продолжить покупки</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Товар</th>
                        <th>Название</th>
                        <th>Цена</th>
                        <th>Количество</th>
                        <th>Сумма</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach($_SESSION['cart'] as $key => $item):
                        $item_total = $item['price'] * $item['quantity'];
                        $total += $item_total;
                    ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </td>
                        <td class="product-name"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="product-price">₽<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="quantity-control">
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="quantity-btn">-</button>
                                </form>
                                
                                <span class="quantity-input"><?php echo $item['quantity']; ?></span>
                                
                                <form action="update_cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="quantity-btn">+</button>
                                </form>
                            </div>
                        </td>
                        <td class="product-total">₽<?php echo number_format($item_total, 2); ?></td>
                        <td>
                            <form action="remove_from_cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                <button type="submit" class="remove-btn">Удалить</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-summary">
                <div class="cart-total">
                    Итого: <span class="cart-total-amount">₽<?php echo number_format($total, 2); ?></span>
                </div>
                <div style="margin-top: 20px;">
                    <a href="checkout.php" class="checkout-btn">Оформить заказ</a>
                    <a href="products.php" class="cart-continue-btn">Продолжить покупки</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    

    


        
       
        <!----------------------------------footer------------------------------------->
        <div class ="footer">
        <div class="container">
            
            <div class="row">
                <div class="footer-col-1">
                    <h3>Скачайте наше приложение</h3>
                    <p>Скачайте приложение для мобильных телефонов на Android и iOS.</p>
                    <div class="app-logo">
                        <img src="images/play-store.png" alt="">
                        <img src="images/app-store.png" alt="">
                    </div>
                </div>
                <div class="footer-col-2">
                    <img src="images/logoi.png">
                    <p>Наше предназначение — сделать удовольствие и пользу от спорта доступными для большинства.</p>
                </div>
                <div class="footer-col-3">
                    <h3>Полезные ссылки</h3>
                   <ul>
                       <li>Купоны</li>
                       <li>Блог</li>
                       <li>Политика возврата</li>
                       <li>Присоединиться к партнерской программе</li>
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
            
            <hr><!-- горизонтальная линия -->
            <p class="copyright">Авторские права 2021 - Apurba Kr. Pramanik</p>
            
        </div>
    </div>
        
        
        <!-----------------------------------js для переключения меню------------------------------------>
        <script>
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
        // Фун
            var menuItems=document.getElementById("MenuItems");
            
            MenuItems.style.maxHeight="0px";
            function menutoggle(){
                if(MenuItems.style.maxHeight == "0px"){
                    MenuItems.style.maxHeight="200px";
                }
                else{
                    MenuItems.style.maxHeight="0px";
                }
            }
        </script>
     
    </body>
</html>
