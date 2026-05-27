<?
$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS));
if(strlen($email) < 2 && !str_contains($email,'@')){
    echo'error';
    exit;
}
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 
$sql = 'INSERT INTO justemail(EMAIL) VALUES(?)';
$query = $pdo->prepare($sql);
$query->execute([$email]);

// Проверяем, найден ли пользователь

    
header('Location: /about.php');


?>
