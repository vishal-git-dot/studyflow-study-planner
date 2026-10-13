<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) redirect('dashboard.php');

$errors = [];
$name = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) $errors[] = 'Name must be between 2 and 80 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $stmt = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)db()->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            flash('success', 'Welcome to StudyFlow! Your workspace is ready.');
            redirect('dashboard.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') $errors[] = 'An account with that email already exists.';
            else $errors[] = 'We could not create your account. Check your database configuration.';
        }
    }
}
$pageTitle = 'Create account';
$authMode = true;
?>
<!doctype html><html lang="en" data-theme="light"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="color-scheme" content="light dark">
<title>Create account · StudyFlow</title><script>try{document.documentElement.dataset.theme=localStorage.getItem('studyflow-theme')==='dark'?'dark':'light'}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/app.css"><script src="assets/js/app.js" defer></script></head><body>
<div class="auth-page"><aside class="auth-aside"><a class="auth-brand" href="index.php"><span class="brand-mark">✓</span> StudyFlow</a><div class="auth-aside-content"><div class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</div><h2>Make room for your best work.</h2><p>Turn your study intentions into a clear plan. One task, one session, one win at a time.</p><div class="auth-benefit"><span>✓</span> Keep every subject organized</div><div class="auth-benefit"><span>✓</span> See deadlines before they sneak up</div><div class="auth-benefit"><span>✓</span> Celebrate progress that adds up</div></div><footer>Built for curious minds and consistent progress.</footer></aside>
<main class="auth-main"><div class="auth-card"><div class="auth-top-tools"><button class="theme-toggle" type="button" id="themeToggle"><span id="themeIcon">☼</span><span id="themeLabel">Light Mode</span></button></div><div class="auth-heading"><div class="eyebrow">GET STARTED FOR FREE</div><h1>Create your account</h1><p>A calmer, clearer way to plan your learning.</p></div>
<?php foreach ($errors as $error): ?><div class="toast toast-error" role="alert"><span>!</span><?= e($error) ?></div><?php endforeach; ?>
<form method="post" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-field"><label for="name">Your name</label><input id="name" name="name" autocomplete="name" maxlength="80" placeholder="e.g. Alex Morgan" value="<?= e($name) ?>" required></div>
<div class="form-field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="email" maxlength="190" placeholder="you@example.com" value="<?= e($email) ?>" required></div>
<div class="form-field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" placeholder="At least 8 characters" required><span class="form-help">Use at least 8 characters.</span></div>
<div class="form-field"><label for="confirm_password">Confirm password</label><input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" minlength="8" placeholder="Enter your password again" required></div>
<button class="btn btn-primary" type="submit">Create my workspace <span>→</span></button></form><div class="auth-switch">Already have an account? <a href="login.php">Sign in</a></div></div></main></div></body></html>
