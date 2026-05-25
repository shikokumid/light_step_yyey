<?php
// checkout.php
session_start();

// Проверяем, есть ли товары в корзине
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Вычисляем общую сумму
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Генерируем уникальный ID заказа
$order_id = 'RS' . date('Ymd') . rand(1000, 9999);

// Если пользователь авторизован, получаем его логин
$user_login = isset($_COOKIE['login']) ? $_COOKIE['login'] : 'guest';

// Статус и дата заказа
$status = 'pending'; // ожидание оплаты
$order_date = date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - Легкий шаг</title>
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
                        <li><a href="">Контакты</a></li>
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
    
    <div class="checkout-container">
        <h1 class="checkout-title">Оформление заказа</h1>
        
        <div class="order-summary">
            <h2>Ваш заказ</h2>
            <div class="order-id">Номер заказа: <?php echo $order_id; ?></div>
            
            <div class="order-items">
                <?php foreach($_SESSION['cart'] as $item): ?>
                    <div class="order-item">
                        <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                        <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                        <span class="item-price">₽<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-total">
                <span>Итого:</span>
                <span>₽<?php echo number_format($total, 2); ?></span>
            </div>
            
            <div class="payment-info">
                <h4>Информация о доставке:</h4>
                <ul>
                    <li>Доставка осуществляется в течение 2-3 рабочих дней</li>
                    <li>Бесплатная доставка при заказе от ₽3500</li>
                    <li>Стоимость доставки: ₽150</li>
                    <li>Оплата при получении наличными или картой</li>
                </ul>
            </div>
        </div>
     
        <div class="payment-section">
            <h2>Оплата заказа</h2>
            <p>Для оплаты заказа отсканируйте QR-код или выберите другой способ оплаты:</p>
            
            <div class="qr-code-container">
                <div class="order-id">Сумма к оплате: ₽<?php echo number_format($total, 2); ?></div>
                <!-- QR код для оплаты -->
                <a href="http://qrcoder.ru" target="_blank">
                    <img src="http://qrcoder.ru/code/?RedStore%20Order%20%23<?php echo $order_id; ?>%0ASum%3A%20%24<?php echo number_format($total, 2); ?>%0ADate%3A%20<?php echo date('Y-m-d'); ?>&4&0" 
                         width="200" height="200" 
                         class="qr-code"
                         alt="QR Code for payment"
                         title="QR код для оплаты заказа">
                </a>
                <p>Отсканируйте QR-код для оплаты</p>
            </div>
            
            <div class="payment-methods">
                <h3>Выберите способ оплаты:</h3>
                
                <div class="payment-method">
                    <input type="radio" id="card" name="payment" value="card" checked>
                    <label for="card">
                        <i class="fa fa-credit-card"></i>
                        Банковская карта
                    </label>
                </div>
                
                <div class="payment-method">
                    <input type="radio" id="paypal" name="payment" value="paypal">
                    <label for="paypal">
                        <i class="fa fa-paypal"></i>
                        PayPal
                    </label>
                </div>
                
                <div class="payment-method">
                    <input type="radio" id="cash" name="payment" value="cash">
                    <label for="cash">
                        <i class="fa fa-money"></i>
                        Наличные при получении
                    </label>
                </div>
            </div>
            
            <button class="pay-now-btn" onclick="processPayment()">
                <i class="fa fa-lock"></i> Подтвердить оплату
            </button>
            
            <a href="cart.php" class="back-to-cart">
                <i class="fa fa-arrow-left"></i> Вернуться в корзину
            </a>
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
                        <a href="https://play.google.com/store/games?hl=ru"><img src="images/play-store.png" alt=""></a>
                        <a href="https://apps.apple.com/ru/developer/apple/id284417353?mt=12"><img src="images/app-store.png" alt=""></a>
                    </div>
                </div>
                <div class="footer-col-2">
                    <a href="#"><img src="images/logoi.png"></a>
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
            <p class="copyright"></p>
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
        
        // Обработка платежа
        function processPayment() {
            const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
            const orderId = "<?php echo $order_id; ?>";
            const totalAmount = "<?php echo number_format($total, 2); ?>";
            
            // Отправляем данные на сервер для сохранения заказа
            fetch('save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&total=${totalAmount}&payment_method=${paymentMethod}`
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    alert(`Спасибо за заказ!\n\nНомер заказа: ${orderId}\nСумма: ₽${totalAmount}\nСпособ оплаты: ${getPaymentMethodName(paymentMethod)}\n\nВаш заказ принят в обработку!`);
                    // Перенаправляем на страницу успеха
                    window.location.href = 'order_success.php?order_id=' + orderId;
                } else {
                    alert('Ошибка при сохранении заказа. Попробуйте еще раз.');
                }
            });
        }
        
        function getPaymentMethodName(method) {
            switch(method) {
                case 'card': return 'Банковская карта';
                case 'paypal': return 'PayPal';
                case 'cash': return 'Наличные при получении';
                default: return 'Неизвестный метод';
            }
        }
    </script>
</body>
</html>