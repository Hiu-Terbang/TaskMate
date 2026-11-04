<?php
require 'config/database.php';
require 'config/auth.php';
require_login();

$q=$pdo->prepare('SELECT name,semester,academic_year FROM users WHERE id=?');
$q->execute([$_SESSION['user_id']]);
$u=$q->fetch();
if(!$u) { session_destroy(); header('Location:login.php'); exit; }

$allowed=['dashboard','tasks','deadlines'];
$page=$_GET['page']??'dashboard';
if(!in_array($page,$allowed,true)) $page='dashboard';
$initial=mb_strtoupper(mb_substr($u['name'],0,1));
$titles=['dashboard'=>'Dashboard','tasks'=>'Semua Tugas','deadlines'=>'Deadline'];
$descriptions=['dashboard'=>'Ringkasan aktivitas akademik dan produktivitasmu.','tasks'=>'Kelola seluruh tugas kuliahmu di satu tempat.','deadlines'=>'Pantau tenggat tugas dari yang paling dekat.'];
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars($titles[$page])?> - TaskMate</title>
<link rel="stylesheet" href="style.css"><script src="script.js" defer></script>
</head>
<body data-view="<?=htmlspecialchars($page)?>">
<aside class="sidebar">
  <a href="index.php?page=dashboard" class="brand"><span class="brand-icon">✓</span> TaskMate</a>
  <p class="menu-label">MENU UTAMA</p>
  <a href="index.php?page=dashboard" class="nav-link <?=$page==='dashboard'?'active':''?>">▦ <span>Dashboard</span></a>
  <a href="index.php?page=tasks" class="nav-link <?=$page==='tasks'?'active':''?>">☷ <span>Semua Tugas</span></a>
  <a href="index.php?page=deadlines" class="nav-link <?=$page==='deadlines'?'active':''?>">◷ <span>Deadline</span></a>
  <div class="sidebar-bottom"><div class="avatar"><?=htmlspecialchars($initial)?></div><div><strong><?=htmlspecialchars($u['name'])?></strong><p>Student Account</p><a href="logout.php">Keluar</a></div></div>
</aside>
<main class="main">
<header class="topbar"><div><p class="eyebrow">STUDENT WORKSPACE</p><h1><?=htmlspecialchars($titles[$page])?></h1><p class="page-description"><?=htmlspecialchars($descriptions[$page])?></p></div><div class="date-chip" id="today"></div></header>

<?php if($page==='dashboard'): ?>
<section class="welcome"><div><p class="welcome-label">YOUR PERSONAL TASK MANAGER</p><h2>Halo, <?=htmlspecialchars($u['name'])?>! 👋</h2><p>Atur tugasmu, selesaikan satu per satu, dan capai targetmu.</p></div><div class="welcome-art">✦</div></section>
<section class="semester-panel"><div><p class="welcome-label">INFORMASI AKADEMIK</p><h2 id="semester-display">Semester <?= (int)$u['semester'] ?></h2><p id="year-display">Tahun Akademik <?=htmlspecialchars($u['academic_year'])?></p></div>
<form id="academic-form" class="academic-form"><label for="semester">Semester</label><select id="semester"><?php for($i=1;$i<=14;$i++):?><option value="<?=$i?>" <?=$i==(int)$u['semester']?'selected':''?>>Semester <?=$i?></option><?php endfor;?></select><label for="academic-year">Tahun akademik</label><input id="academic-year" value="<?=htmlspecialchars($u['academic_year'])?>" placeholder="2026/2027" required><button class="secondary-btn">Simpan</button><span id="profile-message"></span></form></section>
<section class="stats">
<article class="stat-card"><div class="stat-top"><span>Total Tugas</span><span class="stat-icon purple">▤</span></div><strong id="total-count">0</strong><p>Semua tugasmu</p></article>
<article class="stat-card"><div class="stat-top"><span>Belum Selesai</span><span class="stat-icon orange">◷</span></div><strong id="pending-count">0</strong><p>Masih perlu dikerjakan</p></article>
<article class="stat-card"><div class="stat-top"><span>Selesai</span><span class="stat-icon green">✓</span></div><strong id="completed-count">0</strong><p>Tugas yang sudah beres</p></article>
</section>
<section class="dashboard-grid">
<div class="task-panel"><div class="section-heading"><div><h2>Tugas Terdekat</h2><p>Prioritas tugas yang perlu segera diselesaikan.</p></div><a class="text-link" href="index.php?page=deadlines">Lihat deadline →</a></div><div id="dashboard-upcoming" class="task-items"></div><p id="dashboard-empty" class="empty-state hidden">Belum ada tugas yang perlu dikerjakan.</p></div>
<aside class="progress-panel"><h2>Produktivitas</h2><p>Persentase tugas yang sudah selesai</p><div class="progress-ring" id="progress-ring"><div class="progress-inner"><strong id="progress-number">0%</strong><span>Selesai</span></div></div><p id="progress-caption" class="progress-caption">Yuk, mulai kerjakan tugas pertamamu!</p><div class="tip"><span>💡</span><div><strong>Tips hari ini</strong><p>Kerjakan tugas berdasarkan deadline terdekat agar tidak menumpuk.</p></div></div></aside>
</section>

<?php elseif($page==='tasks'): ?>
<section class="task-panel page-panel">
<div class="section-heading"><div><h2>Daftar Semua Tugas</h2><p>Tambah, ubah, dan pantau status tugasmu.</p></div><button id="open-form" class="primary-btn">+ Tambah Tugas</button></div>
<form id="task-form" class="task-form hidden">
<label for="task-title">Nama tugas</label><input id="task-title" required maxlength="120" placeholder="Contoh: Makalah basis data">
<label for="task-category">Mata kuliah / kategori</label><input id="task-category" required maxlength="60" placeholder="Contoh: Sistem Basis Data">
<label for="task-deadline">Deadline</label><input id="task-deadline" type="date" required>
<div class="form-actions"><button type="button" id="cancel-form" class="secondary-btn">Batal</button><button id="save-task" class="primary-btn">Simpan Tugas</button></div>
</form>
<div class="filters"><button class="filter-btn active" data-filter="all">Semua</button><button class="filter-btn" data-filter="pending">Belum selesai</button><button class="filter-btn" data-filter="completed">Selesai</button></div>
<div id="task-items" class="task-items"></div><p id="empty-state" class="empty-state">Belum ada tugas. Klik “Tambah Tugas” untuk mulai!</p>
</section>

<?php else: ?>
<section class="task-panel page-panel">
<div class="section-heading"><div><h2>Urutan Deadline</h2><p>Tugas diurutkan dari tanggal paling dekat.</p></div><a class="secondary-btn link-button" href="index.php?page=tasks">Kelola tugas</a></div>
<div id="deadline-items" class="task-items"></div><p id="deadline-empty" class="empty-state hidden">Belum ada tugas yang menunggu deadline.</p>
</section>
<?php endif; ?>
<footer>© <?=date('Y')?> TaskMate · Made for students.</footer>
</main>
</body></html>
