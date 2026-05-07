# PAAUMENTOR — Setup Guide

Complete installation instructions for running PAAUMENTOR locally with XAMPP.

---

## Prerequisites

Make sure the following are installed before proceeding:

| Tool | Version | Notes |
|---|---|---|
| [XAMPP](https://www.apachefriends.org/) | Any recent | Provides Apache, MySQL, PHP |
| PHP | 8.2 or higher | Bundled with XAMPP |
| [Composer](https://getcomposer.org/) | 2.x | PHP dependency manager |
| [Node.js](https://nodejs.org/) | 18+ | For building frontend assets |
| npm | 9+ | Bundled with Node.js |
| Git | Any | For cloning the repository |

---

## 1. Clone the Repository

Open a terminal and run:

```bash
git clone https://github.com/MosesGoddey/paaumentor.git
cd paaumentor
```

If you are on Windows with XAMPP, clone directly into the `htdocs` folder:

```bash
cd C:\xampp\htdocs
git clone https://github.com/MosesGoddey/paaumentor.git
cd paaumentor
```

---

## 2. Install PHP Dependencies

```bash
composer install
```

This installs Laravel and all backend packages listed in `composer.json`.

---

## 3. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

On Windows (PowerShell):

```powershell
Copy-Item .env.example .env
```

Open `.env` and update the following sections:

### Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paaumentor
DB_USERNAME=root
DB_PASSWORD=
```

> Leave `DB_PASSWORD` empty if your XAMPP MySQL has no root password (default).

### Application URL

```env
APP_URL=http://localhost/paaumentor/public
```

### Gemini AI Key

```env
GEMINI_API_KEY=your_gemini_api_key_here
```

Get a free key at [aistudio.google.com/apikey](https://aistudio.google.com/apikey).  
Create a **new Google Cloud project** for this key to ensure it has its own free quota.

### Mail (Optional)

For email notifications to work, configure a Gmail app password:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_FROM_ADDRESS=your@gmail.com
MAIL_FROM_NAME="PAAUMENTOR"
```

To skip mail during development, leave `MAIL_MAILER=log` (errors go to `storage/logs/` instead of being sent).

---

## 4. Generate Application Key

```bash
php artisan key:generate
```

This fills in `APP_KEY` in your `.env` file.

---

## 5. Create the Database

1. Start XAMPP and ensure **Apache** and **MySQL** are running.
2. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3. Click **New** in the left panel.
4. Create a database named `paaumentor` with collation `utf8mb4_unicode_ci`.

---

## 6. Run Migrations and Seed Demo Data

```bash
php artisan migrate --seed
```

This creates all tables and populates the database with demo users, learning paths, sessions, and certificates.

> If you see errors about missing tables, make sure your `.env` database settings are correct and MySQL is running.

---

## 7. Link Public Storage

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`, required for uploaded files (avatars, submissions, certificates) to be publicly accessible.

---

## 8. Install Frontend Dependencies and Build Assets

```bash
npm install
npm run build
```

This compiles Tailwind CSS and any JavaScript assets via Vite.

> For active development with hot reload, use `npm run dev` instead and keep it running in a separate terminal.

---

## 9. Start the Application

### Option A — Laravel Dev Server (Recommended for development)

```bash
php artisan serve
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

### Option B — XAMPP Apache

If you cloned into `C:\xampp\htdocs\paaumentor`, open:

```
http://localhost/paaumentor/public
```

For a cleaner URL with XAMPP, create a virtual host or configure `.htaccess` to point to `public/`.

---

## 10. Log In

Use these demo credentials to explore the platform:

| Role | Login | Password |
|---|---|---|
| Admin | admin@paau.edu.ng | password |
| Mentor | amaka@paau.edu.ng | password |
| Mentee | 23CS1004 *(student ID)* | password |
| Verifier | verifier@paau.edu.ng | password |

---

## Troubleshooting

### 500 Server Error on first load

Run:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Make sure `storage/` and `bootstrap/cache/` are writable:
```bash
chmod -R 775 storage bootstrap/cache   # Linux/macOS
```
On Windows, right-click the folders → Properties → Security → give full control to your user.

### `php artisan migrate` fails with "Access denied"

Your MySQL credentials in `.env` are wrong, or MySQL is not running. Check XAMPP control panel.

### Gemini API returns 429 or "quota exceeded"

Your API key's project free tier is exhausted. Create a new Google Cloud project at [console.cloud.google.com](https://console.cloud.google.com) and generate a fresh key from [aistudio.google.com/apikey](https://aistudio.google.com/apikey).

### Uploaded files not showing / storage 404

Run `php artisan storage:link` if you haven't already. If the symlink already exists, delete `public/storage` and re-run the command.

### Assets look unstyled

Run `npm run build` to compile CSS/JS. If using `php artisan serve`, make sure Vite assets are built, not in dev-server mode.

---

## Updating After a `git pull`

```bash
composer install
php artisan migrate
npm install && npm run build
php artisan config:clear
```

---

## Directory Overview

```
app/
  Http/Controllers/   — Route controllers for all features
  Models/             — Eloquent models
  Services/
    AiService.php     — Gemini: mentor matching, learning path generation, study buddy
    GeminiService.php — Gemini: assessment question generation
resources/views/      — Blade templates
public/
  css/style.css       — Main stylesheet
  js/app.js           — Charts, theme toggle, shared utilities
database/
  migrations/         — All table schemas
  seeders/            — Demo data
storage/
  app/public/         — User-uploaded files (submissions, avatars, certificates)
```

---

## License

MIT — see [LICENSE](LICENSE) for details.
