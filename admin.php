<?php
session_start();

if (!isset($_COOKIE['login'])) {
    header('Location: account.php');
    exit;
}

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$login = $_COOKIE['login'];

$stmt = $pdo->prepare("
    SELECT role
    FROM regisrtation
    WHERE NAME = ?
");

$stmt->execute([$login]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    die('Доступ запрещен');
}

$products = $pdo->query("
    SELECT *
    FROM products
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$orders = $pdo->query("
    SELECT *
    FROM orders
    ORDER BY order_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Админ-панель</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="small-container">

<h1>Админ-панель</h1>

<h2>Добавить товар</h2>

<form action="add_product.php" method="POST">
    <input type="text" name="item" placeholder="Название" required>
    <input type="text" name="price" placeholder="Цена" required>
    <input type="text" name="image" placeholder="image.jpg" required>
    <button type="submit">Добавить</button>
</form>

<hr>

<h2>Товары</h2>

<table border="1" cellpadding="10">
<tr>
<th>ID</th>
<th>Название</th>
<th>Цена</th>
<th>Удалить</th>
</tr>

<?php foreach($products as $product): ?>

<tr>
<td><?= $product['id'] ?></td>
<td><?= htmlspecialchars($product['item']) ?></td>
<td><?= $product['price'] ?> ₽</td>

<td>
<a href="delete_product.php?id=<?= $product['id'] ?>">
Удалить
</a>
</td>

</tr>

<?php endforeach; ?>

</table>

<hr>

<h2>Заказы</h2>

<table border="1" cellpadding="10">

<tr>
<th>Заказ</th>
<th>Пользователь</th>
<th>Сумма</th>
<th>Статус</th>
<th>Изменить</th>
</tr>

<?php foreach($orders as $order): ?>

<tr>
<td><?= $order['order_id'] ?></td>
<td><?= htmlspecialchars($order['user_login']) ?></td>
<td><?= $order['total'] ?> ₽</td>
<td><?= $order['status'] ?></td>

<td>
<form action="update_order_status.php" method="POST">

<input type="hidden"
name="id"
value="<?= $order['id'] ?>">

<select name="status">
<option value="pending">pending</option>
<option value="processing">processing</option>
<option value="completed">completed</option>
<option value="cancelled">cancelled</option>
</select>

<button type="submit">
Сохранить
</button>

</form>
</td>

</tr>

<?php endforeach; ?>

</table>

</div>

</body>
</html>
