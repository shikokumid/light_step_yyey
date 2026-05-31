<?php
session_start();
$page_title = "О Нас | Легкий Шаг - Спортивный магазин";
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
   
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
                        <li><a href="products.php">Продукты</a></li>
                        <li><a href="about.php" class="active">О нас</a></li>
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
                    <img src="images/cart.png" width="30px" height="30px" alt="Корзина">
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count">
                            <?= array_sum(array_column($_SESSION['cart'], 'quantity')); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()" alt="Меню">
            </div>
        </div>
    </div>

    <!-- Герой-секция "О нас" -->
    <div class="about-hero">
        <div class="container">
            <h1>Легкий Шаг - твой путь к спортивным достижениям</h1>
            <p>Более 10 лет мы помогаем спортсменам и любителям активного образа жизни делать каждый шаг уверенным, комфортным и эффективным.</p>
            <a href="products.php" class="btn">К покупкам →</a>
        </div>
    </div>

    <!-- Наша история -->
    <div class="container about-content">
        <div class="about-story">
            <h2>Наша история</h2>
            <div class="story-box">
                <p>В 2013 году группа энтузиастов-бегунов столкнулась с проблемой: качественная спортивная обувь была либо слишком дорогой, либо недоступной в их городе. Именно тогда родилась идея создать магазин, который бы объединил лучшие бренды спортивной обуви по доступным ценам.</p>
                <p>Сначала это был небольшой интернет-магазин, работающий из гаража. Первые заказы мы собирали и отправляли сами, лично общаясь с каждым клиентом. Наш девиз был прост: "Каждый шаг должен быть легким и уверенным".</p>
                <p>Сегодня "Легкий Шаг" — это сеть магазинов в 5 городах России и один из лидеров онлайн-продаж спортивной обуви. Но мы сохранили наш подход: индивидуальное отношение к каждому клиенту, тщательный отбор продукции и искреннюю любовь к спорту.</p>
            </div>
            
            <!-- Таймлайн -->
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2013</div>
                        <h3>Начало пути</h3>
                        <p>Открытие первого онлайн-магазина в гараже. Первые 100 клиентов за первый год работы.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2015</div>
                        <h3>Первый физический магазин</h3>
                        <p>Открытие первой розничной точки в Москве. Расширение ассортимента до 50 брендов.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2018</div>
                        <h3>Экспансия и развитие</h3>
                        <p>Запуск собственной линейки аксессуаров. Открытие магазинов в Санкт-Петербурге и Казани.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-content">
                        <div class="timeline-year">2023</div>
                        <h3>Новые горизонты</h3>
                        <p>Более 50,000 довольных клиентов. Партнерство с ведущими спортивными брендами мира.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Наши ценности -->
    <div class="values-section">
        <div class="container">
            <h2 class="title">Наши ценности</h2>
            <div class="row">
                <div class="col-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fa fa-heart"></i>
                        </div>
                        <h3>Качество</h3>
                        <p>Мы тщательно отбираем каждый товар. Только проверенные бренды и материалы, которые прослужат долго.</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <h3>Экспертиза</h3>
                        <p>Наши консультанты - действующие спортсмены. Они помогут подобрать обувь именно для ваших целей.</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fa fa-refresh"></i>
                        </div>
                        <h3>Доступность</h3>
                        <p>Мы делаем спорт доступным для всех. Гибкая система скидок, рассрочка и акции для постоянных клиентов.</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class="fa fa-leaf"></i>
                        </div>
                        <h3>Экологичность</h3>
                        <p>Продвигаем экологичные бренды. Используем перерабатываемую упаковку и поддерживаем эко-инициативы.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика -->
    <div class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <div class="stat-item">
                        <div class="stat-number">10+</div>
                        <div class="stat-text">Лет на рынке</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-item">
                        <div class="stat-number">50,000+</div>
                        <div class="stat-text">Довольных клиентов</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-text">Брендов в ассортименте</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-text">Городов присутствия</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Миссия -->
    <div class="mission-section">
        <div class="container">
            <div class="mission-content">
                <h2 class="title">Наша миссия</h2>
                <p class="mission-quote">"Мы верим, что каждый человек заслуживает качественную спортивную экипировку, которая вдохновляет на достижения. Наша цель — сделать спорт доступным и комфортным для всех, от начинающих любителей до профессиональных атлетов."</p>
                <p class="quote-author">— Основатель "Легкий Шаг", главный разработчик, Алфимов Дмитрий</p>
            </div>
        </div>
    </div>

    <!-- Команда -->
    <div class="team-section">
        <div class="container">
            <h2 class="title">Наша команда</h2>
            <div class="row">
                <div class="col-4">
                    <div class="team-member">
                        <div class="team-img">
                            <img src="images/team-0.jpg" alt="Алексей Иванов">
                        </div>
                        <h4>Алфимов Дмитрий</h4>
                        <p>Основатель, главный разработчик</p>
                        <p>Лучший в мире</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="team-member">
                        <div class="team-img">
                            <img src="images/team-05.jpg" alt="Мария Петрова">
                        </div>
                        <h4>Мария Петрова</h4>
                        <p>Главный покупатель</p>
                        <p>Эксперт по спортивной обуви</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="team-member">
                        <div class="team-img">
                            <img src="images/team-03.jpg" alt="Дмитрий Смирнов">
                        </div>
                        <h4>Дмитрий Смирнов</h4>
                        <p>Технический директор</p>
                        <p>Бывший профессиональный трейлраннер</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="team-member">
                        <div class="team-img">
                            <img src="images/team-01.jpg" alt="Ольга Козлова">
                        </div>
                        <h4>Ольга Козлова</h4>
                        <p>Менеджер по клиентскому сервису</p>
                        <p>Спортивный нутрициолог</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Партнеры -->
    <div class="small-container">
        <h2 class="title">Наши партнеры</h2>
        <div class="row">
            <div class="col-5">
                <img src="images/brand-1.png" alt="Demix">
            </div>
            <div class="col-5">
                <img src="images/brand-2.png" alt="Ralf Ringer">
            </div>
            <div class="col-5">
                <img src="images/brand-3.png" alt="Outventure">
            </div>
            <div class="col-5">
                <img src="images/brand-4.png" alt="Asics">
            </div>
            <div class="col-5">
                <img src="images/brand-5.png" alt="Reebok">
            </div>
        </div>
    </div>

    <!-- Призыв к действию -->
    <div class="offer" style="margin-top: 50px;">
        <div class="small-container">
            <div class="row">
                <div class="col-2">
                    <img src="images/logot.png" class="offer-img">
                </div>
                <div class="col-2">
                    <p>Присоединяйтесь к нашему сообществу</p>
                    <h1>Станьте частью "Легкого Шага"</h1>
                    <small>Подпишитесь на нашу рассылку и получите скидку 10% на первую покупку. Узнавайте первыми о новинках, акциях и спортивных событиях.</small><br>
                    <div class="newsletter-form" style="margin-top: 20px;">
                        <input type="email" name="email" placeholder="Ваш email" style="padding: 10px; width: 60%; margin-right: 10px;">
                        <button class="btn" style="padding: 10px 20px;">Подписаться</button>
                    </div>
                </div>
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

    <script src="script.js"></script>
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
        // Функция для переключения мобильного меню (должна быть в script.js, но добавим на всякий случай)
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
