<?php

$pdo = new PDO(
'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
'root',
'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$id = $_POST['id'];
$status = $_POST['status'];

$stmt = $pdo->prepare("
UPDATE orders
SET status = ?
WHERE id = ?
");

$stmt->execute([
    $status,
    $id
]);

header('Location: admin.php');
