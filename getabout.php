<?
$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS));
if(strlen($email) < 2 && !str_contains($email,'@')){
    echo'error';
    exit;
}
$pdo = new PDO('mysql:host=localhost;dbname=diplom;port=3306', 'root', '');

$sql = 'INSERT INTO justemail(EMAIL) VALUES(?)';
$query = $pdo->prepare($sql);
$query->execute([$email]);

// Проверяем, найден ли пользователь

    
header('Location: /about.php');


?>