# TSA4 — Ember Daily — Full CRUD, Auth, and Email

Laravel 12 implementation of IT0049 TSA4, continued from the provided TSA3 project.

## Run locally

1. Double-click **START.cmd** in this folder.
2. Open <http://127.0.0.1:8004>.
3. Keep the launcher window open while using the app; press Ctrl+C to stop it.

No assessment selection is needed. Each assessment has a different port, so all three can run together. Restarting preserves existing data.

## Assignment implementation

The Welcome, Task List, Profile, and About pages are public. Laravel Breeze provides registration, login, logout, password reset, password confirmation, and email verification. Creating, editing, updating status, and archiving tasks require authentication. Deletion is implemented as soft archiving through `is_archived`. A `TaskCompleted` mailable is sent only when a task changes from pending to done.

## Fresh installation

The prepared folder already includes the installed libraries and database. For a fresh source-only copy, install PHP 8.2 or newer and Composer 2, then run the following commands from this directory:

```sh
composer install
```

Copy `.env.example` to `.env`, then run:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate:fresh --seed
php artisan serve --host=127.0.0.1 --port=8004
```

PHP 8.2 or newer is required. No frontend build is needed. The seeded login uses the email from `config/developer.php` and password `password`.

## Mail configuration

The default `MAIL_MAILER=log` safely writes generated messages to `storage/logs/laravel.log`. Set the notification destination in `.env`:

```env
TASK_COMPLETED_RECIPIENT=task-notifications@example.com
```

For a safe SMTP tester such as Mailpit or Mailtrap, replace the `MAIL_*` values in `.env`. A notification is sent on the first pending-to-done transition and is not resent while the task remains done.

## Checks

```sh
php artisan test
php artisan route:list --except-vendor
```

Official Breeze and the locked Symfony console source are retained under `packages/` so installation stays reproducible when Packagist is unavailable.
