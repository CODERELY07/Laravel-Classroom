# ME Classroom

A small, focused classroom app built with Laravel 11. Teachers create classes and share a short code; students join with it. Inside each class you can post materials and questions, collect and grade answers, and talk in a real-time class chat.

> Full technical documentation (architecture, data model, permissions, routes, deployment and the security audit) lives in [`docs/DOCUMENTATION.md`](docs/DOCUMENTATION.md).
> To find the files behind each feature, or to add a new one step by step, see [`docs/FEATURES.md`](docs/FEATURES.md).

## Features

| | Teachers | Students |
|---|---|---|
| Classes | Create, edit, archive, restore, delete | Join with a class code, leave |
| Classwork | Post materials (with attachments) and multi-item questions with due dates and points | Read materials, download attachments, turn in answers |
| People | Add people by email, remove students | See classmates |
| Grades | Grade each answer (auto-saves) | See own grades and missing work |
| Chat | Real-time class chat (Laravel Reverb), with automatic polling fallback | Same |
| Account | Name, password and profile photo | Same |

The interface is responsive from phone to desktop, keyboard-accessible (Bootstrap dialogs and menus, skip link, visible focus) and has no build step for its own CSS/JS.

## Tech stack

- **Backend:** PHP 8.2+, Laravel 11, Eloquent, policies for authorization
- **Realtime:** Laravel Reverb + Laravel Echo (optional; chat polls every 5 s without it)
- **Frontend:** Blade components, Bootstrap 5.3, jQuery 3.7, Font Awesome 6, native ES modules in `public/js`
- **Database:** MySQL in production, SQLite for local development and tests
- **Tests:** Pest 3

## Quick start

Requirements: PHP 8.2+ with `pdo_sqlite` (or MySQL), Composer, Node 18+.

```bash
git clone https://github.com/CODERELY07/Laravel-Classroom.git
cd Laravel-Classroom
composer install
npm install
cp .env.example .env
php artisan key:generate
```

For a zero-config local database, set `DB_CONNECTION=sqlite` in `.env` (and comment out `DB_DATABASE`), then:

```bash
php artisan migrate --seed
```

Run the app (web server, queue listener and Vite together):

```bash
composer run dev
```

Open http://localhost:8000.

### Demo accounts

`php artisan migrate --seed` creates two classes with sample work. Every seeded account's password is `password`.

| Role | Email |
|---|---|
| Teacher | `teacher@example.com` |
| Student | `student@example.com` |

### Real-time chat (optional)

```bash
php artisan reverb:install   # fills the REVERB_* values in .env
php artisan reverb:start
npm run dev                  # or npm run build
```

Set `BROADCAST_CONNECTION=reverb`. Without Reverb the chat still works by polling.

## Tests

```bash
php vendor/bin/pest
```

The suite runs against in-memory SQLite and covers authentication, class permissions, people management, classwork, grading and chat (32 tests).

## Project layout

```
app/
  Http/Controllers/   Thin controllers; JSON helpers in Controller.php
  Http/Middleware/    SecurityHeaders
  Models/             Classess, ClassMember, Question, Material, …
  Policies/           ClassessPolicy (view / teach / manage)
resources/views/
  components/         avatar, field, modal, class-card, feed-item, …
  layouts/            clean (HTML shell), app (signed-in), auth
public/
  css/app.css         Design tokens + components on top of Bootstrap
  js/                 main.js entry and feature modules (forms, ui, chat, …)
resources/js/         Echo bootstrap bundled by Vite
tests/Feature/        Pest feature tests
docs/                 DOCUMENTATION.md (reference), FEATURES.md (feature map + how-to)
```

## License

MIT.
