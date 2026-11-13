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
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,500&family=Inter:wght@400;500;600&display=swap"><link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@2.1.0/dist/iconify-icon.min.js"></script>
<script src="script.js" defer></script><script src="ui.js" defer></script>
</head>
<body data-view="<?=htmlspecialchars($page)?>">
<div class="app">
<aside class="sidebar"><div class="sidebar-inner">
  <a href="index.php?page=dashboard" class="brand" aria-label="TaskMate"><span class="brand-icon">T.</span> TaskMate</a>
  <p class="menu-label">Menu utama</p>
  <a href="index.php?page=dashboard" class="nav-link <?=$page==='dashboard'?'active':''?>"><iconify-icon icon="solar:widget-5-linear" aria-hidden="true"></iconify-icon><span>Dashboard</span></a>
  <a href="index.php?page=tasks" class="nav-link <?=$page==='tasks'?'active':''?>"><iconify-icon icon="solar:checklist-minimalistic-linear" aria-hidden="true"></iconify-icon><span>Semua Tugas</span></a>
  <a href="index.php?page=deadlines" class="nav-link <?=$page==='deadlines'?'active':''?>"><iconify-icon icon="solar:calendar-mark-linear" aria-hidden="true"></iconify-icon><span>Deadline</span></a>
  <div class="sidebar-bottom"><div class="avatar"><?=htmlspecialchars($initial)?></div><div><strong><?=htmlspecialchars($u['name'])?></strong><p>Student Account</p><a class="logout-link" href="logout.php"><iconify-icon icon="solar:logout-2-linear" aria-hidden="true"></iconify-icon>Keluar</a></div></div>
</div></aside>
<nav class="mobile-nav" aria-label="Navigasi">
  <a href="index.php?page=dashboard" class="<?=$page==='dashboard'?'active':''?>"><iconify-icon icon="solar:widget-5-linear" aria-hidden="true"></iconify-icon>Dashboard</a>
  <a href="index.php?page=tasks" class="<?=$page==='tasks'?'active':''?>"><iconify-icon icon="solar:checklist-minimalistic-linear" aria-hidden="true"></iconify-icon>Tugas</a>
  <a href="index.php?page=deadlines" class="<?=$page==='deadlines'?'active':''?>"><iconify-icon icon="solar:calendar-mark-linear" aria-hidden="true"></iconify-icon>Deadline</a>
  <a href="logout.php" class="logout"><iconify-icon icon="solar:logout-2-linear" aria-hidden="true"></iconify-icon>Keluar</a>
</nav>
<main class="main">
<header class="topbar"><div><p class="eyebrow">Student workspace</p><h1 class="split"><?=htmlspecialchars($titles[$page])?></h1><p class="page-description"><?=htmlspecialchars($descriptions[$page])?></p></div><div class="date-chip"><iconify-icon icon="solar:calendar-linear" aria-hidden="true"></iconify-icon><span id="today"></span></div></header>

<?php if($page==='dashboard'): ?>
<section class="welcome reveal"><div><p class="welcome-label">Your personal task manager</p><h2 class="split">Halo, <em><?=htmlspecialchars($u['name'])?></em></h2><p>Atur tugasmu, selesaikan satu per satu, dan capai targetmu.</p></div>
<div class="welcome-art parallax" aria-hidden="true"><div style="position:relative">
<svg viewBox="0 0 760 380" fill="none"><path d="M380 190 L110 80 M380 190 L640 90 M380 190 L90 260 M380 190 L660 270 M380 190 L250 330 M380 190 L510 40" stroke="#8E9C75" stroke-opacity=".35"/><circle cx="380" cy="190" r="120" stroke="#F2EEE5" stroke-opacity=".08"/><circle cx="380" cy="190" r="185" stroke="#F2EEE5" stroke-opacity=".06"/></svg>
<div class="orbit-center">T.</div>
<span class="orbit-dot" style="left:14.5%;top:21%;animation-delay:0s"><iconify-icon icon="solar:book-2-linear"></iconify-icon></span>
<span class="orbit-dot" style="left:84%;top:23.7%;animation-delay:.6s"><iconify-icon icon="solar:calendar-mark-linear"></iconify-icon></span>
<span class="orbit-dot" style="left:11.8%;top:68.4%;animation-delay:1.2s"><iconify-icon icon="solar:pen-new-square-linear"></iconify-icon></span>
<span class="orbit-dot" style="left:86.8%;top:71%;animation-delay:1.8s"><iconify-icon icon="solar:clock-circle-linear"></iconify-icon></span>
<span class="orbit-dot" style="left:67%;top:10.5%;animation-delay:2.4s"><iconify-icon icon="solar:star-linear"></iconify-icon></span>
</div></div></section>
<section class="semester-panel grad-border reveal"><div><p class="welcome-label">Informasi akademik</p><h2 id="semester-display">Semester <?= (int)$u['semester'] ?></h2><p id="year-display">Tahun Akademik <?=htmlspecialchars($u['academic_year'])?></p></div>
<form id="academic-form" class="academic-form"><label for="semester">Semester</label><select id="semester"><?php for($i=1;$i<=14;$i++):?><option value="<?=$i?>" <?=$i==(int)$u['semester']?'selected':''?>>Semester <?=$i?></option><?php endfor;?></select><label for="academic-year">Tahun akademik</label><input id="academic-year" value="<?=htmlspecialchars($u['academic_year'])?>" placeholder="2026/2027" required><button class="secondary-btn">Simpan</button><span id="profile-message"></span></form></section>
<section class="stats">
<article class="stat-card grad-border reveal"><div class="stat-top"><span>Total Tugas</span><span class="stat-icon purple"><iconify-icon icon="solar:documents-linear"></iconify-icon></span></div><strong id="total-count">0</strong><p>Semua tugasmu</p></article>
<article class="stat-card grad-border reveal"><div class="stat-top"><span>Belum Selesai</span><span class="stat-icon orange"><iconify-icon icon="solar:hourglass-linear"></iconify-icon></span></div><strong id="pending-count">0</strong><p>Masih perlu dikerjakan</p></article>
<article class="stat-card grad-border reveal"><div class="stat-top"><span>Selesai</span><span class="stat-icon green"><iconify-icon icon="solar:check-circle-linear"></iconify-icon></span></div><strong id="completed-count">0</strong><p>Tugas yang sudah beres</p></article>
</section>
<section class="dashboard-grid">
<div class="task-panel grad-border reveal"><div class="section-heading"><div><h2 class="split">Tugas Terdekat</h2><p>Prioritas tugas yang perlu segera diselesaikan.</p></div><a class="text-link" href="index.php?page=deadlines">Lihat deadline <iconify-icon icon="solar:arrow-right-linear" aria-hidden="true"></iconify-icon></a></div><div id="dashboard-upcoming" class="task-items"></div><p id="dashboard-empty" class="empty-state hidden">Belum ada tugas yang perlu dikerjakan.</p></div>
<aside class="progress-panel grad-border reveal"><h2>Produktivitas</h2><p>Persentase tugas yang sudah selesai</p><div class="progress-ring" id="progress-ring"><div class="progress-inner"><strong id="progress-number">0%</strong><span>Selesai</span></div></div><p id="progress-caption" class="progress-caption">Yuk, mulai kerjakan tugas pertamamu!</p><div class="tip"><span><iconify-icon icon="solar:lightbulb-minimalistic-linear"></iconify-icon></span><div><strong>Tips hari ini</strong><p>Kerjakan tugas berdasarkan deadline terdekat agar tidak menumpuk.</p></div></div></aside>
</section>

<?php elseif($page==='tasks'): ?>
<section class="task-panel page-panel grad-border reveal">
<div class="section-heading"><div><h2 class="split">Daftar Semua Tugas</h2><p>Tambah, ubah, dan pantau status tugasmu.</p></div><button id="open-form" class="primary-btn"><iconify-icon icon="solar:add-circle-linear" aria-hidden="true"></iconify-icon>Tambah Tugas</button></div>
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
<section class="task-panel page-panel grad-border reveal">
<div class="section-heading"><div><h2 class="split">Urutan Deadline</h2><p>Tugas diurutkan dari tanggal paling dekat.</p></div><a class="secondary-btn link-button" href="index.php?page=tasks">Kelola tugas</a></div>
<div id="deadline-items" class="task-items"></div><p id="deadline-empty" class="empty-state hidden">Belum ada tugas yang menunggu deadline.</p>
</section>
<?php endif; ?>
<footer>© <?=date('Y')?> TaskMate · Made for students.</footer>
</main>
</div>
</body></html>
