<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please sign in to continue.');
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $sent = (string)($_POST['csrf_token'] ?? '');
    $saved = (string)($_SESSION['csrf_token'] ?? '');
    if ($sent === '' || $saved === '' || !hash_equals($saved, $sent)) {
        http_response_code(419);
        exit('Your session token expired. Go back, refresh the page, and try again.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function current_user(): array
{
    return [
        'id' => (int)($_SESSION['user_id'] ?? 0),
        'name' => (string)($_SESSION['user_name'] ?? 'Student'),
        'email' => (string)($_SESSION['user_email'] ?? ''),
    ];
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $result = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $result .= strtoupper(substr($part, 0, 1));
    }
    return $result ?: 'S';
}

function valid_date(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function task_priority_label(string $priority): string
{
    return match ($priority) {
        'high' => 'High',
        'low' => 'Low',
        default => 'Medium',
    };
}
