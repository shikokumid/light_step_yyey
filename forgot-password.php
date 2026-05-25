<?php
// forgot-password.php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля | Легкий Шаг</title>
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
                    <a href="index.php"><img src="images/logot.png" width="125px" alt="Легкий Шаг"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Главная</a></li>
                        <li><a href="products.php">Товары</a></li>
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="contact.php">Контакты</a></li>
                        <?php if(isset($_COOKIE['login'])): ?>
                            <li><a href="user.php">Кабинет пользователя</a></li>
                        <?php else: ?>
                            <li><a href="account.php">Аккаунт</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <a href="cart.php" class="cart-link">
                    <img src="images/cart.png" width="30px" height="30px" alt="Корзина">
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onclick="menutoggle()" alt="Меню">
            </div>
        </div>
    </div>

    <style>
        /* ========== ОБЩИЕ СТИЛИ В СТИЛЕ ПРОЕКТА ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }

        :root {
            --primary: #ff523b;
            --primary-dark: #e8432e;
            --light-accent: #ffd6d6;
            --text-dark: #333;
            --text-medium: #555;
            --white: #fff;
            --bg-light: #f9f9f9;
        }

        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 25px;
        }

        /* ========== ШАПКА (как на других страницах) ========== */
        .header {
            background: radial-gradient(var(--white), var(--light-accent));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 1px;
        }

        .logo a {
            text-decoration: none;
            color: inherit;
        }

        nav a {
            color: var(--text-medium);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: var(--primary);
        }

        /* ========== ОСНОВНОЙ КОНТЕНТ ========== */
        .main-content {
            min-height: calc(100vh - 300px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 0;
        }

        /* Карточка восстановления пароля — в стиле карточек из style.css */
        .password-reset-form {
            background: var(--white);
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .form-title {
            color: var(--primary);
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
        }

        .form-subtitle {
            color: var(--text-medium);
            text-align: center;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 30px;  /* как у кнопок */
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 82, 59, 0.1);
        }

        .submit-btn {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 14px 20px;
            width: 100%;
            border-radius: 30px;  /* закругление как у всех кнопок */
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.02);
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
        }

        .back-to-login a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        /* Сообщения об успехе/ошибке в стиле проекта */
        .message {
            padding: 14px 18px;
            border-radius: 30px;  /* закругление как у кнопок */
            margin-bottom: 25px;
            text-align: center;
            font-weight: 500;
            font-size: 15px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* ========== ПОДВАЛ (как в проекте) ========== */
        .footer {
            background: #000;
            color: #8a8a8a;
            font-size: 14px;
            padding: 50px 0 20px;
            margin-top: auto;
        }

        .footer .row {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .footer-col-1, .footer-col-2, .footer-col-3, .footer-col-4 {
            min-width: 220px;
            margin-bottom: 30px;
        }

        .footer-col-1 { flex-basis: 25%; }
        .footer-col-2 { 
            flex: 1;
            text-align: center;
        }
        .footer-col-3, .footer-col-4 { flex-basis: 15%; }

        .footer h3 {
            color: #fff;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 500;
        }

        .footer p {
            color: #8a8a8a;
            line-height: 1.6;
        }

        .footer ul {
            list-style: none;
        }

        .footer ul li {
            margin-bottom: 10px;
        }

        .footer ul li a {
            color: #8a8a8a;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer ul li a:hover {
            color: var(--primary);
        }

        .footer .app-logo {
            margin-top: 15px;
        }

        .footer .app-logo img {
            width: 130px;
            margin-right: 10px;
        }

        .footer-col-2 img {
            width: 150px;
            margin-bottom: 15px;
        }

        .footer hr {
            border: none;
            background: #b5b5b5;
            height: 1px;
            margin: 30px 0 20px;
        }

        .copyright {
            text-align: center;
            color: #8a8a8a;
        }

        /* Иконки соцсетей */
        .fa {
            margin-right: 8px;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            .password-reset-form {
                padding: 30px 20px;
            }
            .footer .row {
                flex-direction: column;
                text-align: center;
            }
            .footer-col-2, .footer-col-3, .footer-col-4 {
                text-align: center;
            }
        }
    </style>
</head>
    <!-- Основной контент -->
    <main class="main-content">
        <div class="container">
            <div class="password-reset-form">
                <h1 class="form-title">Восстановление пароля</h1>
                <p class="form-subtitle">Введите email, указанный при регистрации, и мы отправим вам ссылку для восстановления пароля</p>
                
                <!-- Сообщение об успехе -->
                <div class="message success-message" id="successMessage" style="display: none;">
                    <i class="fa fa-check-circle"></i> Ссылка для восстановления пароля отправлена на ваш email!
                </div>
                
                <!-- Сообщение об ошибке -->
                <div class="message error-message" id="errorMessage" style="display: none;">
                    <i class="fa fa-exclamation-circle"></i> Пользователь с таким email не найден!
                </div>
                
                <form id="passwordResetForm">
                    <div class="form-group">
                        <label for="email" class="form-label">Email адрес</label>
                        <input type="email" id="email" class="form-input" placeholder="example@mail.com" required>
                    </div>
                    
                    <button type="submit" class="submit-btn">Отправить ссылку для восстановления</button>
                    
                    <div class="back-to-login">
                        <p>Вспомнили пароль? <a href="account.php">Войти в аккаунт</a></p>
                    </div>
                </form>
            </div>
        </div>
    </main>
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

    <script src="js/menu.js"></script>
    <script>
        // Обработка формы (AJAX-заглушка)
        document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');

            // Имитация отправки
            successMsg.style.display = 'block';
            errorMsg.style.display = 'none';

            setTimeout(() => {
                console.log('Запрос на восстановление пароля для:', email);
                document.getElementById('email').value = '';
            }, 1000);

            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 5000);
        });
        
        var MenuItems = document.getElementById("MenuItems");
        MenuItems.style.maxHeight = "0px";

        function menutoggle() {
            if (MenuItems.style.maxHeight === "0px") {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }
    </script>
</body>
</html>