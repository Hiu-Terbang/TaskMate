<?php
require 'config/database.php'; require 'config/auth.php';
if(!empty($_SESSION['user_id'])) { header('Location:index.php'); exit; }
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 $q=$pdo->prepare('SELECT id,password FROM users WHERE email=?'); $q->execute([strtolower(trim($_POST['email']??''))]); $u=$q->fetch();
 if($u && password_verify($_POST['password']??'',$u['password'])) { session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; header('Location:index.php'); exit; }
 $error='Email atau password salah.';
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login TaskMate</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500&family=Inter:wght@400;500;600&display=swap"><link rel="stylesheet" href="style.css"><script src="ui.js" defer></script></head><body class="auth-page"><form class="auth-card" method="post"><h1>Login TaskMate</h1><?php if($error) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?><label>Email</label><input type="email" name="email" required><label>Password</label><input type="password" name="password" required><button class="primary-btn" type="submit">Login</button><p class="forgot-link"><a href="forgot_password.php">Lupa password?</a></p><p>Belum punya akun? <a href="register.php">Daftar</a></p></form></body></html>
