<?php
// add_to_cart.php
session_start();

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Получаем данные о товаре
$product_id = $_POST['product_id'] ?? null;
$product_name = $_POST['product_name'] ?? '';
$product_price = $_POST['product_price'] ?? 0;
$product_image = $_POST['product_image'] ?? '';

// Проверяем ID товара
if (!$product_id) {
    $_SESSION['cart_error'] = 'Ошибка: не указан ID товара';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

// Инициализируем корзину, если ее нет
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Проверяем, есть ли уже такой товар в корзине
$item_found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product_id) {
        $item['quantity'] += 1;
        $item_found = true;
        break;
    }
}

// Если товара нет в корзине, добавляем его
if (!$item_found) {
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => floatval($product_price),
        'image' => $product_image,
        'quantity' => 1
    ];
}

// Сообщение об успехе
$_SESSION['success'] = 'Товар успешно добавлен в корзину!';

// Перенаправляем обратно на предыдущую страницу
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>
