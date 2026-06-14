<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_POST['product_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

$product_name = $_POST['product_name'] ?? '';
$product_price = $_POST['product_price'] ?? 0;
$product_image = $_POST['product_image'] ?? '';

// минимальное количество = 1
if ($quantity < 1) {
    $quantity = 1;
}

if (!$product_id) {
    $_SESSION['cart_error'] = 'Ошибка: не указан ID товара';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$item_found = false;

// ищем товар в корзине
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product_id) {
        $item['quantity'] += $quantity; // 👈 ВАЖНО: добавляем введённое количество
        $item_found = true;
        break;
    }
}

unset($item); // защита от побочных эффектов ссылки

// если товара нет — добавляем с нужным количеством
if (!$item_found) {
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => floatval($product_price),
        'image' => $product_image,
        'quantity' => $quantity // 👈 ВАЖНО
    ];
}

$_SESSION['success'] = 'Товар успешно добавлен в корзину!';

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>
