# ADS_PROJECT (home)

Minimal notes for local development and structure.

Structure (important folders):
- `admin/` — admin pages (dashboard, students, teachers, settings, sections, enrollment)
- `auth/` — authentication pages (`login.php`, `register.php`, `logout.php`)
- `assets/` — static assets (`css/`, `js/`, `images/`)
- `includes/` — shared includes (database connector `db.php`)
- `database/` — SQL exports and setup (`database_setup.sql`)

Local run (Laragon):
- Place project under `c:\laragon\www\ADS_PROJECT` (already set).
- Start Laragon (Apache + MySQL).
- Open browser to `http://localhost/ADS_PROJECT/home/auth/login.php` to access the app.

Notes:
- Database connection is in `includes/db.php`. Ensure MySQL credentials match your Laragon MySQL user.
- Settings are stored in the `system_settings` table (see `database/database_setup.sql`).
- If you see `.php` files offered for download, ensure Laragon's Apache+PHP is running and you're requesting via `http://`.

If you want, I can: run one more repository sweep, remove unused duplicate files from the project root, or draft a full README with setup examples and sample SQL import commands.
