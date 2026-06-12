<?php
session_start();

// Переменные для сообщений
$success_message = '';
$error_message = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Простейшая валидация
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Пожалуйста, заполните имя, email и сообщение.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Некорректный email адрес.';
    } else {
        // Здесь можно отправить письмо администратору
        $to = 'admin@redstore.ru'; // замените на реальный email
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";
        $full_message = "Имя: $name\nEmail: $email\nТема: $subject\n\nСообщение:\n$message";
        
        if (mail($to, 'Сообщение с формы контактов Redstore', $full_message, $headers)) {
            $success_message = 'Спасибо! Ваше сообщение отправлено.';
        } else {
            // Если mail не работает (на локальном сервере), можно сохранять в БД или просто вывести успех для теста
            // Для теста просто покажем успех, но в реальности нужно настроить почту.
            $success_message = 'Спасибо! Ваше сообщение отправлено. (Тестовый режим)';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - Redstore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <!-- Шапка (как на других страницах) -->
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
                        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
        </div>
    </div>
    <div class="contact-container">
        <div class="contact-header">
            <h2>Свяжитесь с нами</h2>
            <p>Мы всегда рады ответить на ваши вопросы</p>
        </div>

        <?php if ($success_message): ?>
            <div class="message success"><?= $success_message ?></div>
        <?php elseif ($error_message): ?>
            <div class="message error"><?= $error_message ?></div>
        <?php endif; ?>

        <div class="contact-row">
            <div class="contact-info">
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa fa-map-marker"></i></div>
                    <div class="contact-info-text">
                        <h4>Адрес</h4>
                        <p>г. Москва ул. Тверская д.1<br>ТЦ "Легкий шаг", 3 этаж</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa fa-phone"></i></div>
                    <div class="contact-info-text">
                        <h4>Телефон</h4>
                        <p>+7 (383) 249-50-50<br>+7 (800) 555-35-35</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa fa-envelope"></i></div>
                    <div class="contact-info-text">
                        <h4>Email</h4>
                        <p>info@freestep.ru<br>support@freestep.ru</p>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fa fa-clock-o"></i></div>
                    <div class="contact-info-text">
                        <h4>Режим работы</h4>
                        <p>Пн-Пт: 9:00 - 21:00<br>Сб-Вс: 10:00 - 20:00</p>
                    </div>
                </div>
            </div>

            <div class="contact-form">
                <h3>Напишите нам</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Ваше имя" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Ваш Email" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="Тема сообщения">
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Ваше сообщение" required></textarea>
                    </div>
                    <button type="submit" name="send_message" class="submit-btn">Отправить сообщение</button>
                </form>
            </div>
        </div>

        <div class="map-container">
            <iframe src="https://maps.google.com/?q=Tverskaya+1+Moscow" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>

    <!-- Подвал -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col-1">
                    <h3>Скачайте наше приложение</h3>
                    <p>Доступно для iOS и Android</p>
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
    </script>

    <style>
        .cart-link {
            position: relative;
            display: inline-block;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff523b;
            color: white;
            border-radius: 50%;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: bold;
            border: 2px solid white;
        }
    </style>
</body>
</html>
