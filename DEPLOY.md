# Deploy TaskMate ke hosting gratis (InfinityFree)

Panduan ini buat upload TaskMate ke [InfinityFree](https://www.infinityfree.com), hosting PHP + MySQL yang gratis. Cara menjalankan di komputer sendiri ada di [README.md](README.md).

## 1. Buat akun hosting

1. Daftar di infinityfree.com, lalu buat akun hosting baru.
2. Pilih subdomain gratis (misalnya `namakamu.infinityfree.io`).

## 2. Siapkan database

1. Di panel hosting, buka **MySQL Databases** dan buat database baru (misalnya `taskmate`).
2. Buka **phpMyAdmin** dari panel, pilih database tadi, lalu import `database.sql`.

## 3. Isi pengaturan database

1. Salin `config/config.example.php` jadi `config/config.local.php`.
2. Isi dengan data dari menu **MySQL Details** di panel:

   ```php
   return [
       'host' => 'sqlXXX.infinityfree.com', // MySQL Hostname
       'port' => 3306,                      // MySQL Port
       'name' => 'if0_XXXXXXXX_taskmate',   // MySQL Database Name
       'user' => 'if0_XXXXXXXX',            // MySQL Username
       'pass' => 'password-akun-hosting',   // MySQL Password
   ];
   ```

`config.local.php` sudah masuk `.gitignore`, jadi password database tidak ikut ter-upload ke GitHub.

## 4. Upload file

1. Buka **File Manager** di panel (atau pakai FTP), masuk ke folder `htdocs`.
2. Hapus file bawaan hosting seperti `index2.html`.
3. Upload semua file TaskMate, termasuk `config/config.local.php`. File harus langsung ada di dalam `htdocs` (jadi `htdocs/index.php`), bukan di dalam folder lain.

## 5. Coba buka

Buka alamat subdomain kamu, daftar akun baru, dan TaskMate siap dipakai.

## Kalau ada masalah

| Masalah | Penyebab yang sering terjadi |
| --- | --- |
| Muncul "Database belum terhubung" | Isi `config.local.php` salah, atau nama filenya jadi `config.local.php.php`. Pastikan ekstensi file kelihatan saat rename. |
| Yang muncul halaman bawaan hosting | `index2.html` masih ada, atau file TaskMate ter-upload di dalam subfolder. |
| Email lupa password tidak masuk | Hosting gratis biasanya tidak bisa mengirim email sendiri. Fitur ini butuh pengaturan SMTP. |
