TASKMATE - PHP NATIVE + MYSQL

INSTALASI DI XAMPP
1. Clone repo ini atau download ZIP dari GitHub, lalu beri nama foldernya TaskMate.
2. Salin folder TaskMate ke folder htdocs XAMPP kamu (contoh: C:\\xampp\\htdocs\\).
3. Jalankan XAMPP Control Panel, lalu nyalakan Apache dan MySQL.
4. Pastikan MySQL menggunakan port 3307. Pengaturannya ada di config/database.php.
5. Buka http://localhost/phpmyadmin/ (sesuaikan port phpMyAdmin jika instalasi XAMPP kamu mengaturnya berbeda).
6. Jika database taskmate sudah berisi data, JANGAN import database.sql lagi.
7. Untuk instalasi baru: buat database bernama taskmate di phpMyAdmin, pilih database itu, lalu import database.sql.
8. Buka http://localhost/TaskMate/ lalu daftar atau login.

STRUKTUR FOLDER
- config/database.php : koneksi database (nilai bawaan untuk XAMPP)
- config/config.example.php : contoh pengaturan database untuk hosting (salin jadi config.local.php)
- config/auth.php     : session dan helper login
- api/tasks.php       : API tugas (GET, POST, PUT, PATCH, DELETE)
- api/profile.php     : API simpan semester dan tahun akademik
- *.php di root       : halaman (index, login, register, logout, lupa & reset password)
- script.js, style.css: frontend

NAVIGASI
- Dashboard: ringkasan akademik, statistik tugas, produktivitas, dan tugas terdekat.
- Semua Tugas: daftar tugas, filter, tambah, edit, hapus, dan tandai selesai.
- Deadline: semua tugas diurutkan berdasarkan tanggal deadline, dengan penanda terlambat, hari ini, dan akan datang.

CATATAN DATA
- File database.sql disertakan sebagai referensi struktur awal untuk instalasi baru.
- Jangan mengimpor database.sql di atas database taskmate yang sudah berjalan jika tidak diperlukan.
- File config/database.php tetap menggunakan host 127.0.0.1, database taskmate, user root, password kosong, dan port 3307.
- API tugas tetap memakai tabel tasks yang sama. Fitur edit menggunakan endpoint PUT baru; data lama tidak diubah.
- Fitur login, register, logout, profil akademik, reset password, dan koneksi database tetap disertakan.

Jika tampilan belum berubah setelah mengganti folder, lakukan hard refresh browser dengan Ctrl + F5.

DEPLOY KE HOSTING GRATIS (contoh: InfinityFree)
1. Daftar di infinityfree.com, buat akun hosting baru, pilih subdomain gratis.
2. Di panel hosting, buka "MySQL Databases", buat database baru (misalnya taskmate).
3. Buka phpMyAdmin dari panel, pilih database tadi, lalu import database.sql.
4. Salin config/config.example.php jadi config/config.local.php, isi dengan data "MySQL Details"
   dari panel (hostname, username, password, nama database, port 3306).
5. Upload semua file (termasuk config/config.local.php) ke folder htdocs lewat File Manager atau FTP.
6. Buka alamat subdomain kamu, daftar akun baru, dan TaskMate siap dipakai.

Catatan: hosting gratis biasanya tidak bisa mengirim email, jadi fitur lupa password
belum bisa mengirim tautan reset kecuali SMTP dikonfigurasi.
