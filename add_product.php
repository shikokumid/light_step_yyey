<?php
session_start();

$pdo = new PDO(
    'mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306',
    'root',
    'zUuofgBLCodqyylBPVacalWLUzyDmyhs'
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$item = trim($_POST['item']);
$price = trim($_POST['price']);

if (!$item || !$price) {
    die('Заполните все поля');
}



if (!isset($_FILES['image'])) {
    die('Фото не выбрано');
}

$image = $_FILES['image'];

if ($image['error'] !== 0) {
    die('Ошибка загрузки изображения');
}

$allowed = ['jpg', 'jpeg', 'png', 'webp'];

$ext = strtolower(
    pathinfo($image['name'], PATHINFO_EXTENSION)
);

if (!in_array($ext, $allowed)) {
    die('Допустимы только JPG PNG WEBP');
}

/*
|--------------------------------------------------------------------------
| Уникальное имя
|--------------------------------------------------------------------------
*/

$fileName =
    uniqid() .
    '.' .
    $ext;

/*
|--------------------------------------------------------------------------
| Папка images
|--------------------------------------------------------------------------
*/

$uploadPath =
    __DIR__ .
    '/images/' .
    $fileName;

move_uploaded_file(
    $image['tmp_name'],
    $uploadPath
);

$stmt = $pdo->prepare("
    INSERT INTO products (
        item,
        price,
        image
    )
    VALUES (?, ?, ?)
");

$stmt->execute([
    $item,
    $price,
    $fileName
]);

header('Location: admin.php');
exit;
?>
