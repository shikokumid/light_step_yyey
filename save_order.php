<?php
// save_order.php
session_start();

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'invalid_request';
    exit;
}

// Получаем данные из формы
$order_id = $_POST['order_id'] ?? '';
$total = $_POST['total'] ?? 0;
$payment_method = $_POST['payment_method'] ?? '';

// Проверяем данные
if (empty($order_id) || empty($payment_method)) {
    echo 'missing_data';
    exit;
}

// Получаем логин пользователя (если авторизован)
$user_login = isset($_COOKIE['login']) ? $_COOKIE['login'] : 'guest';

// Подключение к базе данных
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



// Сохраняем заказ в базу данных
try {
    $sql = "INSERT INTO orders (order_id, user_login, total, payment_method, status) 
            VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id, $user_login, $total, $payment_method]);
    
    // Очищаем корзину
    unset($_SESSION['cart']);
    
    echo 'success';
    
} catch (PDOException $e) {
    // Если ошибка дублирования заказа (уже существует)
    if ($e->getCode() == 23000) {
        echo 'order_exists';
    } else {
        echo 'error: ' . $e->getMessage();
    }
}
?>
