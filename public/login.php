<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) redirect('dashboard.php');
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter your email address and password.';
    } else {
        $stmt = db()->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            flash('success', 'Welcome back. Let’s make today count.');
            redirect('dashboard.php');
        }
        $error = 'The email or password is incorrect.';
    }
}
?>
<!doctype html><html lang="en" data-theme="light"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="color-scheme" content="light dark"><title>Sign in · StudyFlow</title><script>try{document.documentElement.dataset.theme=localStorage.getItem('studyflow-theme')==='dark'?'dark':'light'}catch(e){}</script><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/app.css"><script src="assets/js/app.js" defer></script></head><body>
<div class="auth-page"><aside class="auth-aside"><a class="auth-brand" href="index.php"><span class="brand-mark">✓</span> StudyFlow</a><div class="auth-aside-content"><div class="eyebrow">A LITTLE PROGRESS, EVERY DAY</div><h2>Your goals deserve a plan.</h2><p>Bring your subjects, study tasks, and deadlines into one thoughtful space.</p><div class="auth-benefit"><span>✓</span> Know what to focus on next</div><div class="auth-benefit"><span>✓</span> Make deadlines feel manageable</div><div class="auth-benefit"><span>✓</span> Watch your progress grow</div></div><footer>Welcome back to your learning space.</footer></aside><main class="auth-main"><div class="auth-card"><div class="auth-top-tools"><button class="theme-toggle" type="button" id="themeToggle"><span id="themeIcon">☼</span><span id="themeLabel">Light Mode</span></button></div><div class="auth-heading"><div class="eyebrow">WELCOME BACK</div><h1>Good to see you.</h1><p>Sign in to pick up where you left off.</p></div>
<?php if ($error): ?><div class="toast toast-error" role="alert"><span>!</span><?= e($error) ?></div><?php endif; ?>
<?php foreach (consume_flashes() as $notice): ?><div class="toast toast-<?= e($notice['type']) ?>" role="status"><span>✓</span><?= e($notice['message']) ?></div><?php endforeach; ?>
<form method="post" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" placeholder="you@example.com" value="<?= e($email) ?>" required></div><div class="form-field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Your password" required></div><button class="btn btn-primary" type="submit">Sign in to StudyFlow <span>→</span></button></form><div class="auth-switch">New to StudyFlow? <a href="register.php">Create an account</a></div></div></main></div></body></html>
