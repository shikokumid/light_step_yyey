<?php
// order_success.php
session_start();

// Получаем номер заказа из GET-параметра
$order_id = $_GET['order_id'] ?? '';

// Если заказа нет, перенаправляем на главную
if (empty($order_id)) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ подтвержден - Легкий шаг</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        
        <h1 class="success-title">Заказ успешно оформлен!</h1>
        
        <div class="order-id">Номер вашего заказа: <?php echo htmlspecialchars($order_id); ?></div>
        
        <p class="success-message">
            Спасибо за покупку в Легком шаге!<br>
            Ваш заказ принят в обработку. Мы отправим вам email с подтверждением заказа<br>
            и информацией о доставке в ближайшее время.
        </p>
        
        <div class="action-buttons">
            <a href="index.php" class="btn-primary">
                <i class="fa fa-home"></i> На главную
            </a>
            <a href="products.php" class="btn-secondary">
                <i class="fa fa-shopping-bag"></i> Продолжить покупки
            </a>
            <?php if(isset($_COOKIE['login'])): ?>
                <a href="user_orders.php" class="btn-secondary">
                    <i class="fa fa-shopping-bag"></i> Мои заказы
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>