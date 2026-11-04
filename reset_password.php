<?php
require 'config/database.php';
require 'config/auth.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token_hash CHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id),
    INDEX (token_hash),
    CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$token = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['token'] ?? '') : ($_GET['token'] ?? '');
$validToken = is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token);
$reset = null;
if ($validToken) {
    $stmt = $pdo->prepare('SELECT id, user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW() LIMIT 1');
    $stmt->execute([hash('sha256', $token)]);
    $reset = $stmt->fetch();
}
$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$reset) {
        $error = 'Tautan reset tidak valid atau sudah kedaluwarsa. Minta tautan baru.';
    } elseif (strlen($password) < 8) {
        $error = 'Password baru minimal 8 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak sama.';
    } else {
        $pdo->beginTransaction();
        try {
            $update = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $update->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);
            // Hapus semua token milik akun ini agar tautan lama tidak dapat digunakan lagi.
            $delete = $pdo->prepare('DELETE FROM password_resets WHERE user_id = ?');
            $delete->execute([$reset['user_id']]);
            $pdo->commit();
            $success = true;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $error = 'Password belum berhasil diubah. Silakan coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Buat Password Baru - TaskMate</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page">
<form class="auth-card" method="post" autocomplete="off">
    <h1>Buat Password Baru</h1>
    <?php if ($success): ?>
        <p class="success-message">Password berhasil diubah. Sekarang kamu bisa login dengan password baru.</p>
        <a class="primary-btn auth-button-link" href="login.php">Ke halaman login</a>
    <?php elseif (!$reset): ?>
        <p class="error">Tautan reset tidak valid atau sudah kedaluwarsa. Silakan minta tautan baru.</p>
        <p><a href="forgot_password.php">Minta tautan reset baru</a></p>
    <?php else: ?>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <p class="auth-description">Buat password baru minimal 8 karakter.</p>
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">Password baru</label>
        <input id="password" type="password" name="password" minlength="8" required autocomplete="new-password">
        <label for="confirm_password">Ulangi password baru</label>
        <input id="confirm_password" type="password" name="confirm_password" minlength="8" required autocomplete="new-password">
        <button class="primary-btn" type="submit">Simpan password baru</button>
    <?php endif; ?>
</form>
</body></html>
