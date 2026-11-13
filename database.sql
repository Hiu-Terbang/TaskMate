-- Struktur database TaskMate.
-- Lokal (XAMPP): buat dulu database bernama "taskmate" di phpMyAdmin, pilih database itu, lalu import file ini.
-- Hosting: pilih database yang dibuat dari panel hosting, lalu import file ini.
CREATE TABLE IF NOT EXISTS users (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
 semester TINYINT UNSIGNED NOT NULL DEFAULT 1,
 academic_year VARCHAR(9) NOT NULL DEFAULT '2026/2027',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS tasks (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL,
 title VARCHAR(120) NOT NULL, category VARCHAR(60) NOT NULL,
 deadline DATE NOT NULL, completed TINYINT(1) NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX (user_id, deadline)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS password_resets (
 id INT AUTO_INCREMENT PRIMARY KEY,
 user_id INT NOT NULL,
 token_hash CHAR(64) NOT NULL,
 expires_at DATETIME NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 INDEX (user_id),
 INDEX (token_hash),
 CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
