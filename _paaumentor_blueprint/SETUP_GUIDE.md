# PAAUMENTOR — Laravel Backend Integration Guide

> **Project:** PAAUMENTOR Peer Mentorship Platform  
> **Student:** Moses Goddey Joseph ·
> **Department of Computer Science, PAAU Anyigba**

---

## 📁 WHERE EACH FILE GOES

```
Your Laravel project: C:\xampp\htdocs\paaumentor\
│
├── database/
│   ├── migrations/                ← paste each migration from ALL_MIGRATIONS.php
│   └── seeders/DatabaseSeeder.php ← replace with seeders/DatabaseSeeder.php
│
├── app/
│   ├── Models/
│   │   ├── User.php               ← from models/ALL_MODELS.php
│   │   ├── Skill.php
│   │   ├── Mentorship.php
│   │   ├── MentorSession.php
│   │   ├── LearningPath.php
│   │   ├── LearningModule.php
│   │   ├── LearningTask.php
│   │   ├── TaskSubmission.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── Resource.php
│   │   ├── Rating.php
│   │   ├── Certificate.php
│   │   └── Notification.php
│   │
│   └── Http/Controllers/
│       ├── AuthController.php     ← from controllers/ALL_CONTROLLERS.php
│       ├── DashboardController.php
│       ├── MentorController.php
│       ├── LearningPathController.php
│       ├── ChatController.php
│       ├── AdminController.php
│       └── ProfileController.php
│
├── routes/web.php                 ← replace with routes/web.php
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php          ← from views/ALL_VIEWS.blade.php
│   │   └── sidebar.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php
│   ├── mentors/index.blade.php
│   ├── learning/index.blade.php
│   ├── learning/show.blade.php
│   └── admin/dashboard.blade.php
│
└── public/
    ├── css/style.css              ← copy your style.css here
    └── js/app.js                  ← copy your app.js here
```

---

## 🚀 STEP-BY-STEP SETUP

### Step 1 — Configure your .env database

Open `C:\xampp\htdocs\paaumentor\.env` and make sure these lines are set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paaumentor
DB_USERNAME=root
DB_PASSWORD=
```

Make sure the `paaumentor` database exists in phpMyAdmin (http://localhost/phpmyadmin).

---

### Step 2 — Copy migrations

Each migration in `ALL_MIGRATIONS.php` is a separate `return new class` block.  
Copy each one into its own file in `database/migrations/`. Name them like:

```
2024_01_01_000001_create_users_table.php
2024_01_01_000002_create_skills_table.php
2024_01_01_000003_create_mentorships_table.php
2024_01_01_000004_create_sessions_table.php
2024_01_01_000005_create_learning_paths_table.php
2024_01_01_000006_create_messages_table.php
2024_01_01_000007_create_resources_table.php
2024_01_01_000008_create_ratings_table.php
2024_01_01_000009_create_certificates_table.php
2024_01_01_000010_create_notifications_table.php
```

Each file must start with `<?php` and contain `use Illuminate\...` imports.

---

### Step 3 — Copy models

Copy each class from `ALL_MODELS.php` into its own file in `app/Models/`.  
Remove the duplicate `namespace` lines — only the first file needs them.

**Important:** Laravel already has a `User.php` model. **Replace** it entirely with the  
`User` class from `ALL_MODELS.php`.

---

### Step 4 — Copy controllers

Same pattern — copy each class from `ALL_CONTROLLERS.php` into its own file  
in `app/Http/Controllers/`. Each file needs:

```php
<?php
namespace App\Http\Controllers;

use App\Models\{User, Mentorship, ...};
use Illuminate\Http\Request;
// etc.
```

---

### Step 5 — Copy routes

Replace the entire contents of `routes/web.php` with the file from `routes/web.php`.

---

### Step 6 — Copy Blade views

Split `ALL_VIEWS.blade.php` at each `{{-- SAVE AS: ... --}}` comment into separate files.

---

### Step 7 — Copy CSS and JS to public

```bash
# In your project root:
cp path/to/style.css  public/css/style.css
cp path/to/app.js     public/js/app.js
```

Or in Windows Explorer: copy `style.css` → `public\css\style.css`

---

### Step 8 — Run migrations and seeder

Open PowerShell or Command Prompt in `C:\xampp\htdocs\paaumentor\` and run:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Expected output from seeder:
```
✅ Seeding complete!
   Admin:  admin@paau.edu.ng / password
   Mentee: 23CS1004 / password
   Mentors: amaka@paau.edu.ng / password (and others)
```

---

### Step 9 — Start the server

```bash
php artisan serve
```

Then open: **http://127.0.0.1:8000**

---

## 🔑 DEMO LOGIN CREDENTIALS

| Role   | Login            | Password   |
|--------|------------------|------------|
| Admin  | admin@paau.edu.ng | password  |
| Mentee | 23CS1004          | password  |
| Mentor | amaka@paau.edu.ng | password  |

---

## 🗃️ DATABASE SCHEMA OVERVIEW

```
users               — All users (mentors, mentees, alumni, admins)
skills              — Skill catalog
skill_user          — Pivot: which user has/wants which skills
mentorships         — Mentorship relationships + status
sessions            — Scheduled/completed sessions per mentorship
conversations       — One per mentorship (chat thread)
messages            — Individual chat messages
learning_paths      — Structured learning plan per mentorship
learning_modules    — Sections within a path
learning_tasks      — Individual tasks/assignments
task_submissions    — Mentee submissions per task
resources           — Uploaded files/links
ratings             — Session ratings + reviews
certificates        — Auto-issued completion certificates
notifications       — In-app notifications per user
```

---

## 📌 IMPORTANT NOTES

1. **Breeze vs AuthController** — If you installed Laravel Breeze, you can either:
   - Keep Breeze's auth routes and only add the new routes for mentors/dashboard/etc.
   - Or remove Breeze's auth routes and use `AuthController` directly (simpler).
   The provided `AuthController` is self-contained — it handles both email AND student ID login.

2. **Policies** — The controllers reference `$this->authorize(...)` calls.  
   You'll need to create Policies (MentorshipPolicy, ConversationPolicy, LearningPathPolicy).  
   Quick shortcut: run `php artisan make:policy MentorshipPolicy --model=Mentorship`

3. **Storage** — Always run `php artisan storage:link` to link the `storage/app/public`  
   folder to `public/storage` so uploaded files (avatars, PDFs) are publicly accessible.

4. **Naming collision** — PHP has a built-in `Session` class. The model is named  
   `MentorSession` with `protected $table = 'sessions'` to avoid the conflict.

5. **Match Score** — The `matchScore()` method on `User` is a weighted algorithm:
   - Skill overlap: 50 pts
   - Same department: 20 pts
   - Ideal level gap (1-2 years above): 20 pts
   - Has availability set: 10 pts

---

## 🔜 NEXT STEPS (Phase 3)

- [ ] Real-time chat with Laravel Echo + Pusher (or Soketi for free)
- [ ] WebRTC video call integration
- [ ] PDF certificate generation with `barryvdh/laravel-dompdf`
- [ ] QR code generation with `simplesoftwareio/simple-qrcode`
- [ ] Email notifications with Laravel Mail + Mailgun/SMTP
- [ ] API endpoints for mobile app (Laravel Sanctum)
