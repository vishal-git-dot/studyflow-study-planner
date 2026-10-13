<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();
$pdo = db();

$stmt = $pdo->prepare("SELECT COUNT(*) total, SUM(status='completed') completed, SUM(status='pending') pending, SUM(status='pending' AND due_date < CURDATE()) overdue, SUM(status='pending' AND due_date = CURDATE()) due_today FROM study_tasks WHERE user_id=?");
$stmt->execute([$user['id']]);
$stats = $stmt->fetch() ?: [];
$total = (int)($stats['total'] ?? 0);
$completed = (int)($stats['completed'] ?? 0);
$pending = (int)($stats['pending'] ?? 0);
$overdue = (int)($stats['overdue'] ?? 0);
$dueToday = (int)($stats['due_today'] ?? 0);
$progress = $total ? (int)round($completed / $total * 100) : 0;

$stmt = $pdo->prepare("SELECT t.*, s.name subject_name, s.color subject_color FROM study_tasks t LEFT JOIN subjects s ON s.id=t.subject_id AND s.user_id=t.user_id WHERE t.user_id=? AND t.status='pending' ORDER BY (t.due_date IS NULL), t.due_date ASC, FIELD(t.priority,'high','medium','low') LIMIT 6");
$stmt->execute([$user['id']]); $tasks = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT t.*, s.name subject_name FROM study_tasks t LEFT JOIN subjects s ON s.id=t.subject_id AND s.user_id=t.user_id WHERE t.user_id=? AND t.status='pending' AND t.due_date >= CURDATE() ORDER BY t.due_date ASC LIMIT 5");
$stmt->execute([$user['id']]); $deadlines = $stmt->fetchAll();

$pageTitle = 'Dashboard'; $activePage = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>
<section class="page-heading reveal"><div><div class="eyebrow">YOUR LEARNING SPACE <span>✦</span></div><h1>Hey, <?= e(explode(' ', trim($user['name']))[0]) ?>. Ready to make progress?</h1><p>Every focused session brings you one step closer to your goals.</p></div><a class="btn btn-primary" href="add_task.php"><span>＋</span> Add a study task</a></section>
<section class="stats-grid">
  <article class="stat-card reveal"><div class="stat-top">All study tasks <span class="stat-icon">☷</span></div><div class="stat-value"><?= $total ?></div><div class="stat-note">Everything on your plate</div></article>
  <article class="stat-card reveal"><div class="stat-top">Completed <span class="stat-icon">✓</span></div><div class="stat-value"><?= $completed ?></div><div class="stat-note positive"><?= $progress ?>% overall completion</div></article>
  <article class="stat-card reveal"><div class="stat-top">Due today <span class="stat-icon">◷</span></div><div class="stat-value"><?= $dueToday ?></div><div class="stat-note">Pending tasks for today</div></article>
  <article class="stat-card reveal"><div class="stat-top">Overdue <span class="stat-icon">!</span></div><div class="stat-value"><?= $overdue ?></div><div class="stat-note"><?= $overdue ? 'A good place to restart' : 'You are all caught up' ?></div></article>
</section>
<div class="dashboard-grid">
  <div>
    <section class="panel reveal"><div class="panel-header"><div><h2 class="panel-title">Your progress</h2><p class="panel-subtitle">Every completed task counts.</p></div><a class="text-link" href="tasks.php">View all tasks ↗</a></div>
      <div class="progress-layout"><svg class="progress-ring" viewBox="0 0 100 100" role="img" aria-label="<?= $progress ?> percent complete"><circle class="track" cx="50" cy="50" r="40"/><circle class="fill" cx="50" cy="50" r="40" stroke-dasharray="251.2" stroke-dashoffset="<?= 251.2 - (251.2 * $progress / 100) ?>"/><text x="50" y="51"><?= $progress ?>%</text></svg><div class="progress-copy"><strong><?= $total === 0 ? 'Your journey starts here' : ($progress === 100 ? 'Look at you go!' : 'You’re building momentum') ?></strong><p><?= $total === 0 ? 'Add your first task and start turning plans into progress.' : "$completed of $total tasks completed. Keep your next step small and achievable." ?></p><div class="mini-legend"><span><i class="legend-dot"></i><?= $completed ?> completed</span><span><i class="legend-dot pending"></i><?= $pending ?> pending</span></div></div></div>
    </section>
    <section class="panel reveal"><div class="panel-header"><div><h2 class="panel-title">Next up</h2><p class="panel-subtitle">A short list to help you focus.</p></div><a class="text-link" href="tasks.php">Manage tasks ↗</a></div>
      <?php if (!$tasks): ?><div class="empty-state"><div class="empty-illustration">✦</div><h3>Clear desk, clear mind.</h3><p>You have no pending tasks. Add a study task to plan your next win.</p><a class="btn btn-primary" href="add_task.php">＋ Add your first task</a></div>
      <?php else: ?><div class="task-list"><?php foreach ($tasks as $task): ?><div class="task-item"><form class="inline-form" method="post" action="task_action.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="task_id" value="<?= (int)$task['id'] ?>"><input type="hidden" name="action" value="toggle"><input class="task-check" type="checkbox" aria-label="Mark <?= e($task['title']) ?> completed" onchange="this.form.submit()"></form><div class="task-main"><span class="task-name"><?= e($task['title']) ?></span><div class="task-meta"><span><?= e($task['subject_name'] ?: 'Unassigned') ?></span><span>·</span><span class="pill pill-<?= e($task['priority']) ?>"><?= e(task_priority_label($task['priority'])) ?> priority</span></div></div><span class="task-date"><?= $task['due_date'] ? e(date('M j', strtotime($task['due_date']))) : 'No deadline' ?></span></div><?php endforeach; ?></div><?php endif; ?>
    </section>
  </div>
  <div>
    <section class="panel reveal"><div class="panel-header"><div><h2 class="panel-title">Upcoming deadlines</h2><p class="panel-subtitle">A little planning goes a long way.</p></div><a class="text-link" href="calendar.php">Calendar ↗</a></div>
    <?php if (!$deadlines): ?><div class="empty-state"><div class="empty-illustration">▦</div><h3>Nothing looming.</h3><p>No upcoming deadlines. Enjoy the breathing room or plan ahead.</p></div>
    <?php else: foreach ($deadlines as $task): $d = new DateTime($task['due_date']); $today = new DateTime('today'); $diff = (int)$today->diff($d)->format('%r%a'); ?><div class="deadline-row"><div class="date-block"><strong><?= $d->format('d') ?></strong><small><?= $d->format('M') ?></small></div><div class="deadline-copy"><strong><?= e($task['title']) ?></strong><small><?= e($task['subject_name'] ?: 'No subject') ?></small></div><div class="deadline-status"><?= $diff === 0 ? 'Today' : ($diff === 1 ? 'Tomorrow' : "In $diff days") ?></div></div><?php endforeach; endif; ?>
    </section>
    <section class="panel reveal"><div class="panel-header"><div><h2 class="panel-title">A gentle reminder</h2><p class="panel-subtitle">Progress over perfection.</p></div><span class="stat-icon">✧</span></div><p style="color:var(--muted);font-size:12px;margin:0">You do not need a perfect study day. Choose one meaningful task, give it your attention, and let consistency do the rest.</p><a class="btn btn-secondary" style="margin-top:16px" href="subjects.php">Organize subjects →</a></section>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
