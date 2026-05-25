<?php
// account.php - страница входа и регистрации
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход и регистрация | Легкий Шаг</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .form-btn {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
        }
        .form-btn span {
                    cursor: pointer;
                    padding: 10px 30px;
                    font-size: 18px;
                    font-weight: 500;
                    color: #777;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                }
        .form-btn span.active {
                    color: #ff523b;
                }
        #indicator {
                height: 3px;
                background: #ff523b;
                border: none;
                border-radius: 3px;
                width: 50%;
                position: absolute;
                bottom: -5px;
                left: 0;
                transition: left 0.3s ease;
            }
        .form {
                    display: none;
                    animation: fadeIn 0.5s ease;
                }
        .form.active {
                    display: block;
                }
        @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }



    </style>
    </head>
<body>
    <!-- Шапка -->
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
                <a href="cart.php"><img src="images/cart.png" width="30px" height="30px" alt="Корзина"></a>
                <img src="images/menu.png" class="menu-icon" onclick="menutoggle()" alt="Меню">
            </div>
        </div>
    </div>

    <!-- Страница аккаунта с вкладками Вход / Регистрация -->
    <div class="account-page">
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <img src="images/image1.png" width="100%" alt="Спортивная обувь">
                </div>
                <div class="col-2">
                    <div class="form-container">
                        <!-- Переключатель вкладок -->
                        <div class="form-tabs">
                            <div class="form-btn">
                                <span id="loginTab" class="active">Войти</span>
                                <span id="registerTab">Регистрация</span>
                                <hr id="indicator">
                            </div>
                        </div>

                        <!-- Форма входа -->
                        <form id="loginForm" class="form active" method="post" action="/lib/auth.php">
                            <div class="form-group">
                                <input type="text" name="login" placeholder="Имя пользователя" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" placeholder="Пароль" required>
                            </div>
                            <button type="submit" class="btn">Войти</button>
                            <a href="/forgot-password.php" class="forgot-link">Забыли пароль?</a>
                        </form>

                        <!-- Форма регистрации -->
                        <form id="registerForm" class="form" method="post" action="/lib/reg.php">
                            <div class="form-group">
                                <input type="text" name="login" placeholder="Логин" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" id="regPassword" placeholder="Пароль" required>
                            </div>
                            <button type="submit" class="btn">Зарегистрироваться</button>
                        </form>
                    </div>
                </div>
            </div>
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
                        <a href="https://play.google.com/store/games?hl=ru"><img src="images/play-store.png" alt="Google Play"></a>
                        <a href="https://apps.apple.com/ru/developer/apple/id284417353?mt=12"><img src="images/app-store.png" alt="App Store"></a>
                    </div>
                </div>
                <div class="footer-col-2">
                    <a href="#"><img src="images/logoi.png" alt="Логотип"></a>
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
                        <li>Facebook</li>
                        <li>Twitter</li>
                        <li>Instagram</li>
                        <li>Youtube</li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">Авторские права 2021 - Apurba Kr. Pramanik</p>
        </div>
    </div>

    <!-- JavaScript для переключения вкладок и валидации -->
    <script>
        // Функция показа формы входа
        function showLogin() {
            document.querySelectorAll('.form').forEach(f => f.classList.remove('active'));
            document.getElementById('loginForm').classList.add('active');
            document.getElementById('loginTab').classList.add('active');
            document.getElementById('registerTab').classList.remove('active');
            document.getElementById('indicator').style.left = '0';
            document.getElementById('indicator').style.width = '50%';
        }

        // Функция показа формы регистрации
        function showRegister() {
            document.querySelectorAll('.form').forEach(f => f.classList.remove('active'));
            document.getElementById('registerForm').classList.add('active');
            document.getElementById('registerTab').classList.add('active');
            document.getElementById('loginTab').classList.remove('active');
            document.getElementById('indicator').style.left = '50%';
            document.getElementById('indicator').style.width = '50%';
        }

        // Валидация формы входа
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const login = this.querySelector('input[name="login"]').value.trim();
            const password = this.querySelector('input[name="password"]').value.trim();

            if (!login || !password) {
                e.preventDefault();
                alert('Пожалуйста, заполните все поля');
                return false;
            }
        });

        // Валидация формы регистрации
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const login = this.querySelector('input[name="login"]').value.trim();
            const email = this.querySelector('input[name="email"]').value.trim();
            const password = this.querySelector('input[name="password"]').value;
           

            if (!login || !email || !password || !confirm) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Пароль должен содержать минимум 6 символов');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Введите корректный email адрес');
                return false;
            }
        });

        // Инициализация при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            // По умолчанию показываем форму входа
            showLogin();

            // Обработчики для вкладок
            document.getElementById('loginTab').addEventListener('click', showLogin);
            document.getElementById('registerTab').addEventListener('click', showRegister);

            // Мобильное меню
            var menuItems = document.getElementById("MenuItems");
            menuItems.style.maxHeight = "0px";
            window.menutoggle = function() {
                if (menuItems.style.maxHeight === "0px") {
                    menuItems.style.maxHeight = "200px";
                } else {
                    menuItems.style.maxHeight = "0px";
                }
            };
        });
    </script>
</body>
</html>