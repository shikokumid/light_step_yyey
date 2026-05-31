<?php

$pdo = new PDO(
'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
'root',
'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$item = $_POST['item'];
$price = $_POST['price'];
$image = $_POST['image'];

$stmt = $pdo->prepare("
INSERT INTO products(item, price, image)
VALUES(?,?,?)
");

$stmt->execute([
    $item,
    $price,
    $image
]);

header('Location: admin.php');
