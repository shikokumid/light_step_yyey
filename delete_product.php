<?php

$pdo = new PDO(
'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
'root',
'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$id = $_GET['id'];

$stmt = $pdo->prepare("
DELETE FROM products
WHERE id = ?
");

$stmt->execute([$id]);

header('Location: admin.php');
