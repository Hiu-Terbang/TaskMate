<?php
if(session_status()!==PHP_SESSION_ACTIVE) session_start();
function require_login(): void {
 if(empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
}
function json_out(array $data,int $status=200): never {
 http_response_code($status); header('Content-Type: application/json; charset=utf-8');
 echo json_encode($data,JSON_UNESCAPED_UNICODE); exit;
}
