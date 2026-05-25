<?php
session_start();

// Функция для создания таблицы addresses, если её нет

// Проверяем авторизацию
if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$username = $_COOKIE['login'];

// Подключение к БД
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Получаем ID пользователя и email
$userStmt = $pdo->prepare("SELECT id, mail FROM regisrtation WHERE name = ?");
$userStmt->execute([$username]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: account.php');
    exit;
}

$user_id = $user['id'];
$user_email = $user['mail'];

// --- Обработка POST-запросов ---

// 1. Обновление email
if (isset($_POST['update_profile'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profile_error = "Некорректный email";
    } else {
        // Проверка, не занят ли email другим пользователем
        $checkEmail = $pdo->prepare("SELECT id FROM regisrtation WHERE mail = ? AND id != ?");
        $checkEmail->execute([$email, $user_id]);
        if ($checkEmail->fetch()) {
            $profile_error = "Этот email уже используется другим аккаунтом";
        } else {
            // Обновляем данные
            $update = $pdo->prepare("UPDATE regisrtation SET mail = ? WHERE id = ?");
            $update->execute([$email, $user_id]);
            $profile_success = "Email успешно обновлён";
            $user_email = $email;
        }
    }
}

// 2. Смена пароля
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $passStmt = $pdo->prepare("SELECT password FROM regisrtation WHERE id = ?");
    $passStmt->execute([$user_id]);
    $hash = $passStmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $password_error = "Неверный текущий пароль";
    } elseif (strlen($new) < 6) {
        $password_error = "Новый пароль должен быть не менее 6 символов";
    } elseif ($new !== $confirm) {
        $password_error = "Пароли не совпадают";
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $updatePass = $pdo->prepare("UPDATE regisrtation SET password = ? WHERE id = ?");
        $updatePass->execute([$newHash, $user_id]);
        $password_success = "Пароль успешно изменён";
    }
}

// 3. Добавление нового адреса
if (isset($_POST['add_address'])) {
    $address_line1 = trim($_POST['address_line1']);
    $address_line2 = trim($_POST['address_line2']);
    $city = trim($_POST['city']);
    $postal_code = trim($_POST['postal_code']);
    $country = trim($_POST['country']);

    if (empty($address_line1) || empty($city)) {
        $address_error = "Заполните обязательные поля (улица, дом и город)";
    } else {
        $checkFirst = $pdo->prepare("SELECT COUNT(*) FROM addresses WHERE user_id = ?");
        $checkFirst->execute([$user_id]);
        $is_first = ($checkFirst->fetchColumn() == 0);

        $insert = $pdo->prepare("INSERT INTO addresses (user_id, address_line1, address_line2, city, postal_code, country, is_default) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$user_id, $address_line1, $address_line2, $city, $postal_code, $country, $is_first ? 1 : 0]);
        
        header('Location: user_settings.php?tab=addresses&added=1');
        exit;
    }
}

// 4. Удаление адреса
if (isset($_GET['delete_address'])) {
    $address_id = (int)$_GET['delete_address'];
    $delStmt = $pdo->prepare("DELETE FROM addresses WHERE id = ? AND user_id = ?");
    $delStmt->execute([$address_id, $user_id]);
    header('Location: user_settings.php?tab=addresses&deleted=1');
    exit;
}

// 5. Установка адреса по умолчанию
if (isset($_GET['default_address'])) {
    $address_id = (int)$_GET['default_address'];
    $reset = $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?");
    $reset->execute([$user_id]);
    $set = $pdo->prepare("UPDATE addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
    $set->execute([$address_id, $user_id]);
    header('Location: user_settings.php?tab=addresses&defaulted=1');
    exit;
}

// Получаем список адресов пользователя
$addrStmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");
$addrStmt->execute([$user_id]);
$addresses = $addrStmt->fetchAll(PDO::FETCH_ASSOC);

// Определяем активную вкладку
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки пользователя - Redstore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
   
</head>
<body>
    <!-- Шапка -->
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
                        <span class="cart-count"><?= array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                    <?php endif; ?>
                </a>
                <img src="images/menu.png" class="menu-icon" onClick="menutoggle()">
            </div>
        </div>
    </div>

    <div class="settings-container">
        <div class="settings-header">
            <h1>Настройки профиля</h1>
            <a href="user.php" class="back-link"><i class="fa fa-arrow-left"></i> Назад</a>
        </div>

        <!-- Табы -->
        <div class="tabs">
            <a href="?tab=profile" class="tab-link <?= $active_tab == 'profile' ? 'active' : '' ?>">Аккаунт</a>
            <a href="?tab=security" class="tab-link <?= $active_tab == 'security' ? 'active' : '' ?>">Безопасность</a>
            <a href="?tab=addresses" class="tab-link <?= $active_tab == 'addresses' ? 'active' : '' ?>">Адреса</a>
        </div>

        <div class="tab-content">
            <!-- Вкладка Аккаунт -->
            <div class="tab-pane <?= $active_tab == 'profile' ? 'active' : '' ?>" id="profile">
                <h2>Личные данные</h2>
                
                <?php if (isset($profile_success)): ?>
                    <div class="message success"><?= $profile_success ?></div>
                <?php endif; ?>
                <?php if (isset($profile_error)): ?>
                    <div class="message error"><?= $profile_error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Имя пользователя (логин)</label>
                        <input type="text" value="<?= htmlspecialchars($username) ?>" disabled>
                        <small>Логин нельзя изменить</small>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn">Сохранить изменения</button>
                </form>
            </div>

            <!-- Вкладка Безопасность -->
            <div class="tab-pane <?= $active_tab == 'security' ? 'active' : '' ?>" id="security">
                <h2>Смена пароля</h2>
                
                <?php if (isset($password_success)): ?>
                    <div class="message success"><?= $password_success ?></div>
                <?php endif; ?>
                <?php if (isset($password_error)): ?>
                    <div class="message error"><?= $password_error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Текущий пароль</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль (минимум 6 символов)</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Подтверждение нового пароля</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn">Изменить пароль</button>
                </form>
            </div>

            <!-- Вкладка Адреса -->
            <div class="tab-pane <?= $active_tab == 'addresses' ? 'active' : '' ?>" id="addresses">
                <h2>Мои адреса доставки</h2>

                <?php if (isset($_GET['added'])): ?>
                    <div class="message success">Адрес успешно добавлен</div>
                <?php endif; ?>
                <?php if (isset($_GET['deleted'])): ?>
                    <div class="message success">Адрес удалён</div>
                <?php endif; ?>
                <?php if (isset($_GET['defaulted'])): ?>
                    <div class="message success">Основной адрес изменён</div>
                <?php endif; ?>
                <?php if (isset($address_error)): ?>
                    <div class="message error"><?= $address_error ?></div>
                <?php endif; ?>

                <div class="address-list">
                    <?php if (count($addresses) > 0): ?>
                        <?php foreach ($addresses as $addr): ?>
                            <div class="address-card <?= $addr['is_default'] ? 'default' : '' ?>">
                                <?php if ($addr['is_default']): ?>
                                    <span class="address-badge"><i class="fa fa-check"></i> Основной</span>
                                <?php endif; ?>
                                <p><strong><?= htmlspecialchars($addr['address_line1']) ?></strong></p>
                                <?php if (!empty($addr['address_line2'])): ?>
                                    <p><?= htmlspecialchars($addr['address_line2']) ?></p>
                                <?php endif; ?>
                                <p><?= htmlspecialchars($addr['city']) ?>, <?= htmlspecialchars($addr['postal_code']) ?></p>
                                <p><?= htmlspecialchars($addr['country']) ?></p>
                                <div class="address-actions">
                                    <?php if (!$addr['is_default']): ?>
                                        <a href="?tab=addresses&default_address=<?= $addr['id'] ?>" class="btn-small" onclick="return confirm('Сделать этот адрес основным?')">Сделать основным</a>
                                    <?php endif; ?>
                                    <a href="edit_address.php?id=<?= $addr['id'] ?>" class="btn-small">Редактировать</a>
                                    <a href="?tab=addresses&delete_address=<?= $addr['id'] ?>" class="btn-small delete" onclick="return confirm('Удалить этот адрес?')">Удалить</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>У вас пока нет сохранённых адресов.</p>
                    <?php endif; ?>
                </div>

                <div class="add-address-form">
                    <h3>Добавить новый адрес</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Адрес (улица, дом, квартира) *</label>
                            <input type="text" name="address_line1" required>
                        </div>
                        <div class="form-group">
                            <label>Дополнительная информация</label>
                            <input type="text" name="address_line2">
                        </div>
                        <div class="row2">
                            <div class="form-group">
                                <label>Город *</label>
                                <input type="text" name="city" required>
                            </div>
                            <div class="form-group">
                                <label>Почтовый индекс</label>
                                <input type="text" name="postal_code">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Страна</label>
                            <input type="text" name="country" value="Россия">
                        </div>
                        <button type="submit" name="add_address" class="btn">Добавить адрес</button>
                    </form>
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
            <p class="copyright">© 2023 Легкий Шаг. Все права защищены.</p>
        </div>
    </div>

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