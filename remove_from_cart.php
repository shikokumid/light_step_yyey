<?php
// remove_from_cart.php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$item_key = $_POST['item_key'] ?? null;

if ($item_key !== null && isset($_SESSION['cart'][$item_key])) {
    unset($_SESSION['cart'][$item_key]);
}

header('Location: cart.php');
exit;   
?>