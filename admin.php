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
    <style>
.user-container{
    max-width:1200px;
    margin:auto;
    padding:40px 20px;
}

.user-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.user-menu{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:30px;
}

.user-menu-item{
    padding:12px 18px;
    background:#f5f5f5;
    border-radius:10px;
    text-decoration:none;
    color:#333;
}

.user-menu-item.active{
    background:#ff523b;
    color:#fff;
}

.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:25px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
}

.card h3{
    margin-bottom:12px;
}

.admin-block{
    margin-top:40px;
    background:#111;
    color:white;
    border-radius:18px;
    padding:30px;
}

.admin-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-top:20px;
}

.admin-stat{
    background:#1d1d1d;
    padding:25px;
    border-radius:12px;
    text-align:center;
}

.logout-btn{
    background:#ff523b;
    color:#fff;
    padding:12px 20px;
    border-radius:8px;
    text-decoration:none;
}
</style>
<body>

<div class="small-container">

<h1>Админ-панель</h1>

<h2>Добавить товар</h2>

<h2>Добавить товар</h2>

<form action="add_product.php" method="POST" enctype="multipart/form-data">

    <input
        type="text"
        name="item"
        placeholder="Название товара"
        required
    >

    <input
        type="number"
        name="price"
        placeholder="Цена"
        required
    >

    <input
        type="file"
        name="image"
        accept="image/*"
        required
    >

    <button type="submit">
        Добавить товар
    </button>

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
