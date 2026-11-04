<?php
require 'config/database.php';
require 'config/auth.php';

// Tabel dibuat otomatis agar database lama tidak perlu di-import ulang.
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

$message = '';
$error = '';
$devLink = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Masukkan alamat email yang valid.';
    } else {
        // Respons tetap sama agar halaman tidak membocorkan apakah email terdaftar.
        $message = 'Jika email tersebut terdaftar, instruksi reset password akan dikirim.';
        $stmt = $pdo->prepare('SELECT id, name, email FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Batasi permintaan berulang untuk akun yang sama.
            $recent = $pdo->prepare('SELECT id FROM password_resets WHERE user_id = ? AND created_at > (NOW() - INTERVAL 60 SECOND) LIMIT 1');
            $recent->execute([$user['id']]);
            if (!$recent->fetch()) {
                $pdo->prepare('DELETE FROM password_resets WHERE expires_at < NOW() OR user_id = ?')->execute([$user['id']]);
                $token = bin2hex(random_bytes(32));
                $hash = hash('sha256', $token);
                $insert = $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))');
                $insert->execute([$user['id'], $hash]);

                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
                $resetUrl = $scheme . '://' . $host . $basePath . '/reset_password.php?token=' . urlencode($token);
                $subject = 'Reset Password TaskMate';
                $body = "Halo {$user['name']},\n\nGunakan tautan berikut untuk membuat password baru (berlaku 15 menit dan hanya bisa digunakan sekali):\n\n{$resetUrl}\n\nJika kamu tidak meminta reset password, abaikan email ini.";
                $headers = "From: TaskMate <no-reply@localhost>\r\nContent-Type: text/plain; charset=UTF-8";

                // Localhost: tampilkan tautan hanya untuk pengujian lokal.
                // Hosting: konfigurasi mail() / SMTP terlebih dahulu agar tautan dikirim melalui email.
                $isLocal = in_array(strtolower($host), ['localhost', '127.0.0.1']);
                if ($isLocal) {
                    $devLink = $resetUrl;
                } else {
                    @mail($user['email'], $subject, $body, $headers);
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Lupa Password - TaskMate</title><link rel="stylesheet" href="style.css"></head>
<body class="auth-page">
<form class="auth-card" method="post" autocomplete="on">
    <h1>Lupa Password</h1>
    <p class="auth-description">Masukkan email akun TaskMate kamu untuk memulai proses reset password.</p>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($message): ?><p class="success-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
    <?php if ($devLink): ?>
        <div class="dev-reset-box"><strong>Mode pengujian localhost</strong><p>Pengiriman email belum dikonfigurasi. Buka tautan reset ini untuk melanjutkan:</p><a class="dev-reset-link" href="<?= htmlspecialchars($devLink, ENT_QUOTES, 'UTF-8') ?>">Reset password TaskMate</a><small>Tautan berlaku 15 menit dan hanya dapat digunakan sekali.</small></div>
    <?php endif; ?>
    <label for="email">Email</label>
    <input id="email" type="email" name="email" required autocomplete="email" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <button class="primary-btn" type="submit">Kirim instruksi reset</button>
    <p><a href="login.php">Kembali ke login</a></p>
</form>
</body></html>
