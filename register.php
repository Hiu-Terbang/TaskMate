<?php
require 'config/database.php'; require 'config/auth.php';
if(!empty($_SESSION['user_id'])) { header('Location:index.php'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??'')); $pw=$_POST['password']??'';
 if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($pw)<8) $error='Isi data dengan benar. Password minimal 8 karakter.';
 else try {
  $q=$pdo->prepare('INSERT INTO users(name,email,password) VALUES(?,?,?)');
  $q->execute([$name,$email,password_hash($pw,PASSWORD_DEFAULT)]);
  session_regenerate_id(true); $_SESSION['user_id']=(int)$pdo->lastInsertId(); header('Location:index.php'); exit;
 } catch(PDOException $e) { $error='Email sudah terdaftar atau registrasi gagal.'; }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Daftar TaskMate</title><link rel="stylesheet" href="style.css"></head><body class="auth-page"><form class="auth-card" method="post"><h1>Daftar TaskMate</h1><?php if($error) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?><label>Nama</label><input name="name" required maxlength="100"><label>Email</label><input type="email" name="email" required><label>Password (min. 8 karakter)</label><input type="password" name="password" minlength="8" required><button class="primary-btn">Buat akun</button><p>Sudah punya akun? <a href="login.php">Login</a></p></form></body></html>
