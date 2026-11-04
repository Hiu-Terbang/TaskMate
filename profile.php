<?php
require '../config/database.php'; require '../config/auth.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST') json_out(['message'=>'Method tidak didukung.'],405);
$d=json_decode(file_get_contents('php://input'),true)??[];
$s=filter_var($d['semester']??null,FILTER_VALIDATE_INT); $y=trim($d['academic_year']??'');
if(!$s||$s<1||$s>14||!preg_match('/^\d{4}\/\d{4}$/',$y)) json_out(['message'=>'Semester/tahun akademik tidak valid.'],422);
$p=explode('/',$y); if((int)$p[1] !== (int)$p[0]+1) json_out(['message'=>'Contoh format yang benar: 2026/2027.'],422);
$q=$pdo->prepare('UPDATE users SET semester=?,academic_year=? WHERE id=?'); $q->execute([$s,$y,$_SESSION['user_id']]); json_out(['saved'=>true]);
