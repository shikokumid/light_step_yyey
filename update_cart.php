<?php
// update_cart.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$item_key = $_POST['item_key'] ?? null;
$action = $_POST['action'] ?? '';

if ($item_key !== null && isset($_SESSION['cart'][$item_key])) {
    if ($action === 'increase') {
        $_SESSION['cart'][$item_key]['quantity'] += 1;
    } elseif ($action === 'decrease') {
        $_SESSION['cart'][$item_key]['quantity'] -= 1;
        if ($_SESSION['cart'][$item_key]['quantity'] <= 0) {
            unset($_SESSION['cart'][$item_key]);
        }
    }
}

header('Location: cart.php');
exit;
?>