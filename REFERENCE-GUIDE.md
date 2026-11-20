# Ember Daily

**Tasks for Today Management System in Laravel**  
IT0049 · Technical Summative Assessment 3

Developed for **Maverick John Klyde L. Carrillo**. Ember Daily applies the warm cream, espresso, and burnt orange theme of the Ember Café project to a personal café checklist. It includes the four assessment pages, a seeded database, and task creation, editing, completion, reopening, and removal.

## Open the prepared local application

The project folder already contains the installed Laravel dependencies and a seeded SQLite database.

1. Double-click **Start Ember Daily.cmd**.
2. Open **<http://127.0.0.1:8001>**.
3. Keep the launcher window open. Press Ctrl+C to stop it.

If the local preview is already running, open the URL directly. Stop an existing Ember Daily server before starting another on port 8001. The original Ember Café POS uses port 8000; both apps have separate databases and session cookies.

The launcher detects XAMPP PHP at `C:\xampp\php\php.exe`, or uses PHP from PATH. It runs migrations, seeds a new empty database on first use, and preserves existing tasks on later launches. Restarting does not reset task dates, completion states, or deleted tasks.

## Install from the source ZIP or a fresh clone

Requirements: PHP 8.2 or newer, Composer 2, and the PHP extensions PDO SQLite, mbstring, OpenSSL, DOM, XML, and fileinfo. Laravel 12 is used to work with the installed XAMPP PHP 8.2. The assignment specifies Laravel without requiring a particular major version.

From inside the extracted `ember-daily` directory:

```sh
composer install
```

Copy `.env.example` to `.env`, then run:

```sh
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8001
```

On this Windows computer, use `C:\xampp\php\php.exe` in place of `php` if it is not in PATH. The source ZIP excludes installed dependencies and local settings; run `composer install` before using a fresh extraction.

The frontend uses local CSS, SVG, and JavaScript. It needs no Node installation, Vite build, external fonts, or remote images.

## Assessment objectives and implementation

| Requirement | Where to find it |
| --- | --- |
| `tasks` migration with title, status, task_date, timestamps | `database/migrations/2026_11_20_000001_create_tasks_table.php` |
| Status defaults to `pending` | Database default in the tasks migration |
| `users` migration with unique username, full_name, email | `database/migrations/0001_01_01_000000_create_users_table.php` |
| At least 8 tasks across at least 3 dates, including today | `TaskSeeder`: 10 tasks; 3 yesterday, 5 today, 2 tomorrow at initial seeding |
| Exactly one user record | `UserSeeder`: record ID 1, username Klyde |
| Eloquent `Task` and `User` models | `app/Models/Task.php`, `app/Models/User.php` |
| Welcome page `/` only shows today's tasks | `TaskController::today()`, `Task::forToday()`, `resources/views/today.blade.php` |
| Full list `/tasks` displays every task ordered by date | `TaskController::index()`, `Task::chronological()`, `resources/views/tasks/index.blade.php` |
| Profile `/profile` displays the single user record | `ProfileController::show()`, `resources/views/profile.blade.php` |
| Static About `/about` identifies the developer | `PageController::about()`, `resources/views/about.blade.php`, `config/developer.php` |
| Multiple Blade views share an Eloquent data layer | Task model scopes; distinct Welcome, Task List, Profile, and About views |
| Clear MVC structure | Routes select controllers; controllers use models; Blade views display the data |
| Navigation across all required pages | `resources/views/layouts/app.blade.php` |
| Reproducible database and setup documentation | Migrations, seeders, `composer.lock`, `.env.example`, this README |
| GitHub and hosted links | Kept local at the owner's request; publishing and deployment are deferred |

## Date filtering and ordering

The application timezone is **Asia/Manila**, configured through `APP_TIMEZONE` in `.env` and `config/app.php`. Today's date is calculated at request time rather than hardcoded to the date the project was built.

`Task::forToday()` applies an Eloquent `whereDate('task_date', today()->toDateString())` condition. Both pending and completed tasks scheduled for today appear on the Welcome page. Other dates are excluded.

`Task::chronological()` orders by `task_date` ascending, then by ID for stable ordering within a date. The full Task List applies this scope without a date or status restriction, then the Blade view groups the results by date. Its default view includes every record. Optional search filters the displayed rows until the search is cleared.

Seeded dates are relative to the date the seeder first runs. Existing task dates remain fixed afterward: tasks do not silently shift to the next day. If a future day has no tasks, Today shows an empty state with an Add task link.

## Profile and developer details

- Full name: Maverick John Klyde L. Carrillo
- Username: Klyde
- Email: klyde_carrillo@yahoo.com

The profile is retrieved from the `users` table. The static About page takes its developer credit from `config/developer.php`. That file is also used by `UserSeeder` so the initial profile matches the developer details.

The single user is a profile record, not a login account. The assignment does not require authentication or password management. The app is configured as a local single-user classroom project.

## Additional management features

- Create a task with a title, date, and status.
- Edit or reschedule it.
- Mark a pending task completed, or reopen a completed task.
- See progress counts and a completion ring for today's tasks.
- Filter today's rows by All, Pending, or Completed.
- Search the full list without changing the stored data.
- Remove a task from its edit page after confirming in a dialog.
- Use the pages at desktop and phone widths.

All writes use Laravel validation and CSRF-protected forms. Titles are trimmed, required, limited to 255 characters, and escaped by Blade. Dates must be valid `Y-m-d` dates. Status is restricted to `pending` or `completed`. Route model binding returns 404 for missing task records. The core create, edit, and completion forms work without JavaScript; JavaScript adds filtering, duplicate-submit protection, and the removal confirmation.

## Demonstration walkthrough

1. Open `/`: confirm that only today's five initial tasks appear. The initial progress is two completed and three pending.
2. Open `/tasks`: confirm all ten tasks appear across three dates, earliest first.
3. Open `/profile`: verify the single profile's name, username, and email.
4. Open `/about`: verify the developer credit and project description.
5. Add a task scheduled for today and watch it appear on Today.
6. Complete it, then reopen it. The progress summary changes each time.
7. Edit its date to tomorrow. It disappears from Today and appears under tomorrow in the full list.
8. Search for the task, clear the search, and try a search with no matches.

## Checks

Run these commands **from the project directory**:

```sh
php artisan test
php artisan route:list --except-vendor
php artisan migrate:status
php artisan view:cache
```

The feature suite contains **14 passing tests and 75 assertions**, covering the required pages and seed counts, database schema defaults and username uniqueness, today's date filter, the Manila/UTC date boundary, chronological retrieval, profile and developer details, persistence of task changes, validation, deletion, HTML escaping, and empty states. Tests use an isolated in-memory database and do not reset the local working data.

Browser checks also verified adding a task, completing and reopening it, rescheduling it to tomorrow, searching the full list, confirming the profile, and checking desktop and mobile layouts.

The task seeder populates an empty table; it leaves an existing task list alone. The profile seeder updates its single ID without adding duplicate profiles. To seed a fresh installation manually, run `php artisan db:seed` after migrations.

## Project map

```text
app/Http/Controllers/    Page, Profile, and Task controllers
app/Models/             Task and User Eloquent models
config/developer.php    Developer credit and initial profile details
database/migrations/    Task and user schemas
database/seeders/       Ten sample tasks and one profile
public/                 Local styles, JavaScript, and favicon
resources/views/        Required pages, shared layout, task form and partials
routes/web.php          Page and task action routes
tests/Feature/          TaskManagementTest.php
```

## If publishing later

The app has not been uploaded or hosted, following the local-only preference. A future GitHub submission should include raw source, `composer.lock`, migrations, seeders, and the README. Exclude `.env`, `vendor/`, local SQLite files, logs, and generated views; the supplied ignore files cover these.

For PHP hosting, use the `public/` directory as the web root, install Composer dependencies, create the host's environment settings and application key, configure a persistent database, set `APP_ENV=production` and `APP_DEBUG=false`, retain `APP_TIMEZONE=Asia/Manila`, and run `php artisan migrate --seed --force`. Any public deployment should be reviewed for access control and profile visibility before putting personal information online.

## References

- Laravel query scopes: <https://laravel.com/docs/12.x/eloquent#query-scopes>
- Date comparisons: <https://laravel.com/docs/12.x/queries#additional-where-clauses>
- Migrations: <https://laravel.com/docs/12.x/migrations>
- Seeding: <https://laravel.com/docs/12.x/seeding>
- Blade templates: <https://laravel.com/docs/12.x/blade>
