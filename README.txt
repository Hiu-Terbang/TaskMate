TASKMATE - PHP NATIVE + MYSQL

INSTALASI DI XAMPP
1. Clone repo ini atau download ZIP dari GitHub, lalu beri nama foldernya TaskMate.
2. Salin folder TaskMate ke folder htdocs XAMPP kamu (contoh: C:\\xampp\\htdocs\\).
3. Jalankan XAMPP Control Panel, lalu nyalakan Apache dan MySQL.
4. Pastikan MySQL menggunakan port 3307. Pengaturannya ada di config/database.php.
5. Buka http://localhost/phpmyadmin/ (sesuaikan port phpMyAdmin jika instalasi XAMPP kamu mengaturnya berbeda).
6. Jika database taskmate sudah berisi data, JANGAN import database.sql lagi. Perubahan navigasi ini tidak membutuhkan penghapusan atau pembuatan ulang database.
7. Untuk instalasi baru, import database.sql lewat phpMyAdmin.
8. Buka http://localhost/TaskMate/ lalu daftar atau login.

STRUKTUR FOLDER
- config/database.php : koneksi database (host, nama database, user, password, port)
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
