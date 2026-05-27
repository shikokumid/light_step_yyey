<?php 

$login = trim(filter_var($_POST['login'], FILTER_SANITIZE_SPECIAL_CHARS));
$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_SPECIAL_CHARS));
$password = trim(filter_var($_POST['password'], FILTER_SANITIZE_SPECIAL_CHARS));
if(strlen($login) < 2){
    echo'error';
    exit;
}
if(strlen($email) < 2 && !str_contains($email,'@')){
    echo'error';
    exit;
}
// hash password
$hash_password = "lasdl;askd;ak213";
$password = md5($hash_password.$password);
// date base
$pdo = new PDO('mysql:host=mysql-so2r.railway.internal;dbname=railway;port=3306', 'root', 'zUuofgBLCodqyylBPVacalWLUzyDmyhs');
 // insert
$sql = 'INSERT INTO regisrtation(NAME, MAIL, PASSWORD) VALUES(?,?,?)';
$query = $pdo->prepare($sql); 
$query ->execute([$login, $email, $password]);
header ("Location: /account.php");
?>
