<?php
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? 'Workspace';
$activePage = $activePage ?? '';
$user = current_user();
$flashes = consume_flashes();
?>
<!doctype html>
<html lang="en" data-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark">
  <title><?= e($pageTitle) ?> · StudyFlow</title>
  <script>
    (function () {
      try {
        const saved = localStorage.getItem('studyflow-theme');
        document.documentElement.dataset.theme = saved === 'dark' ? 'dark' : 'light';
      } catch (_) {}
    })();
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/app.css">
  <script src="assets/js/app.js" defer></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="dashboard.php" aria-label="StudyFlow dashboard">
      <span class="brand-mark"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M6 8.5 16 4l10 4.5v14L16 27 6 22.5z" fill="none" stroke="currentColor" stroke-width="2"/><path d="m11 16 3.2 3.2L21.5 12" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
      <span>Study<span class="brand-light">Flow</span><small>YOUR LEARNING SPACE</small></span>
    </a>
    <div class="sidebar-label">WORKSPACE</div>
    <nav class="nav-list" aria-label="Main navigation">
      <a class="nav-link <?= $activePage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php"><span class="nav-icon">⌂</span> Dashboard</a>
      <a class="nav-link <?= $activePage === 'tasks' ? 'active' : '' ?>" href="tasks.php"><span class="nav-icon">☷</span> My tasks</a>
      <a class="nav-link <?= $activePage === 'subjects' ? 'active' : '' ?>" href="subjects.php"><span class="nav-icon">▤</span> Subjects</a>
      <a class="nav-link <?= $activePage === 'calendar' ? 'active' : '' ?>" href="calendar.php"><span class="nav-icon">▦</span> Calendar</a>
    </nav>
    <div class="sidebar-bottom">
      <div class="focus-card">
        <span class="focus-spark">✦</span>
        <strong>Small steps add up.</strong>
        <p>A little progress each day leads to big results.</p>
      </div>
      <div class="user-card">
        <span class="avatar"><?= e(initials($user['name'])) ?></span>
        <span class="user-meta"><strong><?= e($user['name']) ?></strong><small><?= e($user['email']) ?></small></span>
        <a class="logout-link" href="logout.php" title="Sign out" aria-label="Sign out">↗</a>
      </div>
    </div>
  </aside>
  <main class="main-area">
    <header class="topbar">
      <button class="icon-button mobile-menu" id="menuToggle" type="button" aria-label="Open navigation">☰</button>
      <div class="breadcrumb"><span>Workspace</span><span class="crumb-slash">/</span><strong><?= e($pageTitle) ?></strong></div>
      <div class="topbar-actions">
        <button class="theme-toggle" type="button" id="themeToggle" aria-label="Switch color theme"><span id="themeIcon">☼</span><span id="themeLabel">Light Mode</span></button>
        <span class="topbar-avatar"><?= e(initials($user['name'])) ?></span>
      </div>
    </header>
    <?php foreach ($flashes as $notice): ?>
      <div class="toast toast-<?= e($notice['type']) ?>" role="status"><span><?= $notice['type'] === 'success' ? '✓' : '!' ?></span><?= e($notice['message']) ?><button type="button" class="toast-close" aria-label="Dismiss notification">×</button></div>
    <?php endforeach; ?>
    <div class="page-content">
