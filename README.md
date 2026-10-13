# StudyFlow — Student Study Planner

StudyFlow is a plain PHP, MySQL, HTML, CSS, and JavaScript starter app for organizing subjects, study tasks, priorities, deadlines, and completion progress.

## Features

- Account registration, login, logout, and password hashing
- User-scoped subjects and tasks (users only see their own records)
- Create/edit/delete subjects and tasks
- Deadline, priority, pending/completed status, and overdue indicators
- Dashboard metrics and progress ring
- Task search and status filtering
- Monthly calendar with task deadlines
- Light/dark theme switch persisted in `localStorage`
- Responsive desktop, tablet, and mobile layout
- CSRF tokens, prepared statements, HTML escaping, and session ID regeneration
- Reduced-motion support, visible focus states, toast feedback, and confirmation prompts

## Requirements

- PHP 8.1+ (8.2+ recommended)
- MySQL 8.0+ or MariaDB equivalent
- PHP extensions: `pdo_mysql`, `mbstring`
- Apache (XAMPP/WAMP) or PHP's built-in server for local testing

## Install on Windows with XAMPP

1. Extract the `StudyFlow` folder into `C:\xampp\htdocs\StudyFlow`.
2. Start Apache in XAMPP.
3. Ensure one MySQL service is configured for the database. Your existing `MySQL80` service may already use port `3306`. Do not stop it blindly. Either:
   - use that service and keep `DB_PORT` as `3306`, or
   - configure XAMPP's MySQL to use `3307` and set `DB_PORT` to `3307`.
   Do not run two MySQL servers on the same port.
4. Open phpMyAdmin for the MySQL server you intend to use, or run the MySQL client.
5. Import `database/schema.sql`. It creates the `studyflow` database and its tables.
6. Open `config/config.php` and set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASS` for your local MySQL account. XAMPP defaults are often `root` with an empty password; do not use that setup for a public deployment.
7. Visit `http://localhost/StudyFlow/public/`.
8. Register a new account, create subjects, and add study tasks.

### Database import via command line

```bash
mysql -u root -p < database/schema.sql
```

If the MySQL root account has no password, omit `-p`. If using a non-default port, add `-P 3307` (uppercase P).

## Optional: PHP built-in server

From the extracted project folder:

```bash
php -S 127.0.0.1:8000 -t public
```

Then open `http://127.0.0.1:8000/`. Ensure the database settings are correct first.

## Project structure

```text
StudyFlow/
├── config/
│   ├── config.php
│   └── database.php
├── database/
│   └── schema.sql
├── includes/
│   ├── auth.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── public/
│   ├── assets/
│   │   ├── css/app.css
│   │   └── js/app.js
│   ├── index.php
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── subjects.php
│   ├── subject_form.php
│   ├── subject_action.php
│   ├── tasks.php
│   ├── add_task.php
│   ├── task_action.php
│   └── calendar.php
└── README.md
```

## Security and production notes

This is a local-development starter, not a fully audited production deployment. Before public hosting:

- Create a dedicated database user with only the privileges needed by this app.
- Set a strong database password and configure HTTPS.
- Set secure cookie options (`Secure`, `HttpOnly`, and `SameSite`) based on deployment.
- Add login rate limiting, password reset and email verification if needed.
- Configure web server document root to `StudyFlow/public` so `config/`, `includes/`, and `database/` are not web-accessible.
- Add automated tests, database backups, and operational error logging.
- Review CSP and other HTTP security headers for your hosting setup.
- Do not display raw database exceptions to visitors.

## Notes

- Subject deletion unassigns related tasks rather than deleting the tasks.
- Deleting a task is permanent.
- A missing due date is allowed.
- The app uses Asia/Kolkata as its default PHP timezone; change `APP_TIMEZONE` if needed.
- Google Fonts are optional external fonts; the UI falls back to system sans-serif fonts if unavailable.
