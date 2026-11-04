<?php
require '../config/database.php'; require '../config/auth.php'; require_login();
$uid=(int)$_SESSION['user_id']; $method=$_SERVER['REQUEST_METHOD'];
if($method==='GET') {
 $q=$pdo->prepare('SELECT id,title,category,DATE_FORMAT(deadline,"%Y-%m-%d") deadline,completed FROM tasks WHERE user_id=? ORDER BY deadline,id DESC');
 $q->execute([$uid]); json_out(['tasks'=>$q->fetchAll()]);
}
$data=json_decode(file_get_contents('php://input'),true)??[];
if($method==='POST') {
 $title=trim($data['title']??''); $cat=trim($data['category']??''); $deadline=$data['deadline']??'';
 $dt=DateTime::createFromFormat('Y-m-d',$deadline);
 if(!$title||mb_strlen($title)>120||!$cat||mb_strlen($cat)>60||!$dt||$dt->format('Y-m-d')!==$deadline) json_out(['message'=>'Data tugas tidak valid.'],422);
 $q=$pdo->prepare('INSERT INTO tasks(user_id,title,category,deadline) VALUES(?,?,?,?)'); $q->execute([$uid,$title,$cat,$deadline]); json_out(['id'=>(int)$pdo->lastInsertId()],201);
}

if($method==='PUT') {
 $id=(int)($data['id']??0);
 $title=trim($data['title']??''); $cat=trim($data['category']??''); $deadline=$data['deadline']??'';
 $dt=DateTime::createFromFormat('Y-m-d',$deadline);
 if($id<1||!$title||mb_strlen($title)>120||!$cat||mb_strlen($cat)>60||!$dt||$dt->format('Y-m-d')!==$deadline) json_out(['message'=>'Data tugas tidak valid.'],422);
 $q=$pdo->prepare('UPDATE tasks SET title=?,category=?,deadline=? WHERE id=? AND user_id=?');
 $q->execute([$title,$cat,$deadline,$id,$uid]);
 if(!$q->rowCount()) {
   $check=$pdo->prepare('SELECT id FROM tasks WHERE id=? AND user_id=?'); $check->execute([$id,$uid]);
   if(!$check->fetch()) json_out(['message'=>'Tugas tidak ditemukan.'],404);
 }
 json_out(['updated'=>true]);
}

if($method==='PATCH') {
 $q=$pdo->prepare('UPDATE tasks SET completed=? WHERE id=? AND user_id=?'); $q->execute([!empty($data['completed'])?1:0,(int)($data['id']??0),$uid]); json_out(['updated'=>true]);
}
if($method==='DELETE') {
 $q=$pdo->prepare('DELETE FROM tasks WHERE id=? AND user_id=?'); $q->execute([(int)($data['id']??0),$uid]); json_out(['deleted'=>true]);
}
json_out(['message'=>'Method tidak didukung.'],405);
