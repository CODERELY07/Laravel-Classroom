# ME Classroom — Technical Documentation

This document covers how ME Classroom works and how to run, extend and deploy it. For a short overview, see the [README](../README.md).

## Contents

1. [Overview](#1-overview)
2. [Architecture](#2-architecture)
3. [Directory structure](#3-directory-structure)
4. [Data model](#4-data-model)
5. [Roles and permissions](#5-roles-and-permissions)
6. [Routes](#6-routes)
7. [Request and response conventions](#7-request-and-response-conventions)
8. [Front end](#8-front-end)
9. [Real-time chat](#9-real-time-chat)
10. [File uploads](#10-file-uploads)
11. [Security](#11-security)
12. [Installation and configuration](#12-installation-and-configuration)
13. [Deployment](#13-deployment)
14. [Testing](#14-testing)
15. [Extending the app](#15-extending-the-app)
16. [Troubleshooting](#16-troubleshooting)
17. [Refactor audit (October 2026)](#17-refactor-audit-october-2026)

---

## 1. Overview

ME Classroom lets a teacher run a class online:

- **Classes.** A teacher creates a class (name, section, subject). The app generates a 7-character class code. Students join with that code, or a teacher adds them by email.
- **Stream.** Everything posted in a class, newest first.
- **Classwork.** Two kinds of posts:
  - **Materials:** a title, a description and an optional attachment (PDF, Office document, image or text).
  - **Questions:** a title, instructions, one or more prompts, a due date and a point value. Students turn in one written answer per question.
- **People.** Teachers and students in the class. Teachers can add and remove students.
- **Grades.** Teachers grade each answer out of the question's points. Students see their own grades and any missing work.
- **Chat.** A message thread per class. It updates live through Laravel Reverb, or every few seconds when Reverb isn't running.
- **Archive.** The owner can archive a class to hide it from home pages, restore it later or delete it permanently.

Accounts have one of two roles, chosen at sign-up: **teacher** or **student**.

## 2. Architecture

```
Browser ──HTTP──▶ Laravel routes (routes/web.php)
   │                 │  auth / guest / throttle middleware
   │                 ▼
   │             Controllers ──Gate::authorize──▶ ClassessPolicy
   │                 │
   │                 ▼
   │             Eloquent models ──▶ MySQL / SQLite
   │                 │
   │                 └─ broadcast(MessageSent) ──▶ Reverb ──WebSocket──┐
   │                                                                   │
   └── public/js (ES modules + jQuery + Bootstrap) ◀── Echo (Vite) ◀──┘
```

- **Server-rendered pages** use Blade layouts and components. Pages work on first load without extra AJAX calls.
- **Mutations** (create, edit, join, grade, …) are AJAX `POST` requests that return a small JSON object (see [§7](#7-request-and-response-conventions)). The one exception is turning in an answer, which is a normal form post with a redirect and flash message.
- **Authorization** lives in one policy, `App\Policies\ClassessPolicy`. Every controller action that touches a class calls `Gate::authorize(...)`.
- **No build step for the app's own assets.** `public/css/app.css` and `public/js/*.js` are served as-is, and cache-busted with the file's modification time. Vite is only used to bundle Laravel Echo for the chat.

## 3. Directory structure

```
app/
├── Events/MessageSent.php            Chat message broadcast (private channel, immediate)
├── Http/
│   ├── Controllers/
│   │   ├── Controller.php            success()/error() JSON helpers
│   │   ├── AuthController.php        Sign in (rate limited), sign up, sign out
│   │   ├── ForgotPasswordController  Password broker: reset link + reset
│   │   ├── UserController.php        Home, archive, settings, avatar
│   │   ├── ClassController.php       Create/join/edit/archive/restore/delete/leave
│   │   ├── StreamController.php      Class page, people search/add/remove
│   │   ├── MaterialController.php    Post, view, download attachments
│   │   ├── QuestionController.php    Post and view questions
│   │   ├── ClassMemberQuestionAnswersController.php   Turn in answers
│   │   ├── GradeController.php       Grade page and grade updates
│   │   └── ChatAppController.php     Send and list chat messages
│   └── Middleware/SecurityHeaders.php
├── Models/                           See §4
├── Policies/ClassessPolicy.php       view / teach / manage / create
├── Providers/
│   ├── AppServiceProvider.php        Numeric route parameter patterns
│   └── ViewServiceProvider.php       Shares $authUser with every view
└── View/Components/EnrolledClassess.php   Sidebar class list

bootstrap/app.php                     Routing, middleware, JSON error rendering
config/reverb.php                     REVERB_ALLOWED_ORIGINS support
database/
├── migrations/                       Schema (see §4)
└── seeders/DatabaseSeeder.php        Demo teacher, student, classes and work

public/
├── css/app.css                       Design tokens and components (see §8)
└── js/
    ├── main.js                       Entry point; calls each init*()
    ├── core.js                       AJAX setup, toasts, safe DOM builder, avatar()
    ├── forms.js                      data-ajax-form handling and error display
    ├── ui.js                         Sidebar, tabs, copy, confirm dialog, avatar picker
    ├── classes.js                    Class grid refresh, edit dialog
    ├── classwork.js                  Question builder, people picker, unenroll, grading
    └── chat.js                       Chat client with Echo + polling fallback

resources/
├── js/                               app.js → bootstrap.js → echo.js (Vite)
└── views/
    ├── layouts/
    │   ├── clean.blade.php           <html> shell, assets, toasts
    │   ├── app.blade.php             Signed-in shell: top bar + sidebar
    │   ├── auth.blade.php            Centered card for auth pages
    │   └── partials/                 header, sidebar, class grid, modals
    ├── components/                   avatar, field, password-field, modal,
    │                                 empty-state, feed-item, class-card,
    │                                 enrolled-classess
    ├── home/index.blade.php          Landing page
    ├── auth/                         login, signup, forgot/reset password, terms
    └── user/                         index, archiveclass, stream, settings,
                                      class/{question,material,grade}

routes/
├── web.php                           All HTTP routes
└── channels.php                      Broadcast channel authorization

tests/Feature/ClassroomTest.php       Feature tests (Pest)
```

## 4. Data model

```
users ─┬─< class_members >─┬─ classess
       │        │          │      │
       │        └─< class_member_question_answers >── questions >── classess
       │
       ├─< materials >── classess
       ├─< questions
       └─< classroom_chats >── classess
```

| Table | Model | Purpose | Key columns |
|---|---|---|---|
| `users` | `User` | Accounts | `name`, `email` (unique), `password` (hashed), `role` (`teacher`/`student`), `profile` (avatar filename) |
| `classess` | `Classess` | Classes | `classname`, `section`, `subject`, `class_code` (unique), `status` (`ok` = active, `archive`), `teacher_id` (owner) |
| `class_members` | `ClassMember` | Membership | `class_id`, `user_id`, `role` (`Teacher`/`Student`), `joined_at` |
| `materials` | `Material` | Shared resources | `class_id`, `user_id`, `title`, `description` (text), `file` (nullable path) |
| `questions` | `Question` | Assignments | `class_id`, `user_id`, `title`, `description` (text), `question` (JSON array of prompts), `due_date`, `points` |
| `class_member_question_answers` | `ClassMemberQuestionAnswers` | Submissions | `class_member_id`, `question_id`, `answer` (text), `grade` (nullable int) |
| `classroom_chats` | `ClassroomChat` | Chat | `class_id`, `user_id`, `message` (text) |
| `password_reset_tokens` | — | Hashed reset tokens | `email`, `token`, `created_at` |

Notes:

- Table and model names (`classess`) are kept as they were to avoid a breaking migration on existing databases.
- The two role columns use different casing for historical reasons: `users.role` is lowercase, `class_members.role` is capitalised. Use the constants `User::ROLE_TEACHER` / `User::ROLE_STUDENT` and `ClassMember::ROLE_TEACHER` / `ClassMember::ROLE_STUDENT`, and `ClassMember::roleFor($user)` to convert.
- All foreign keys cascade on delete. Deleting a class removes its members, posts, answers and chat. `ClassController::destroy` also deletes uploaded attachment files.
- Migration `2026_10_26_000000_fix_content_column_types` makes `materials.file` nullable, turns description and message columns into `TEXT`, and adds indexes for chat history and membership lookups.

Useful model helpers:

| Helper | Description |
|---|---|
| `Classess::forMember($user)` | Query scope: classes the user belongs to |
| `Classess::generateCode()` | Unique 7-character lowercase code |
| `$class->membershipFor($user)` | The user's `ClassMember` row, or `null` |
| `$class->themeIndex()` | 0–5, picks the banner colour |
| `$user->isTeacher()`, `avatar_url`, `initial` | Role check, profile photo URL, first letter |
| `$question->items()` | The prompts as a clean array |
| `$question->isPastDue()` | Past the end of the due date |
| `$material->fileDisk()`, `isImage()`, `deleteFile()` | Attachment helpers (see [§10](#10-file-uploads)) |
| `$chat->toChatPayload()` | The JSON shape sent to the browser |

## 5. Roles and permissions

Authorization goes through `ClassessPolicy`:

| Ability | Who | Used for |
|---|---|---|
| `create` | Any user with role `teacher` | Creating a class |
| `view` | Any member of the class | Class page, materials, questions, attachments, chat (HTTP and WebSocket) |
| `teach` | The owner, or a member with role `Teacher` | Posting materials/questions, grading, people search, adding/removing students |
| `manage` | The owner only (`teacher_id`) | Editing, archiving, restoring and deleting the class |

Additional rules in controllers:

- A student can only submit answers as **themselves**. The membership is found from the session, never from the request.
- Answers can be edited until they are graded.
- Grades must be whole numbers between 0 and the question's points.
- The owner can't "leave" their own class; they archive or delete it instead.
- Archived classes can't be joined by code.

If a non-member opens a class page, they are sent back to their home page. JSON requests get `403 {"error": "..."}`.

## 6. Routes

All routes are in `routes/web.php`. Route parameters `class`, `material`, `question` and `student` only match digits.

### Public / guest

| Method | URI | Name | Notes |
|---|---|---|---|
| GET | `/` | `home` | Landing page; signed-in users are redirected to `/user` |
| GET | `/login` | `login` | |
| POST | `/login` | `login.post` | 5 attempts per email+IP, then locked out temporarily |
| GET | `/signup` | `signup` | |
| POST | `/signup` | `signup.post` | Throttled 10/min; signs the user in |
| GET | `/forget-password` | `forget.password.get` | |
| POST | `/forget-password` | `forget.password.post` | Throttled 6/min; same reply whether or not the email exists |
| GET | `/reset-password/{token}` | `password.reset` | Link from the reset email |
| POST | `/reset-password` | `reset.password.post` | Throttled 6/min |

### Signed in

| Method | URI | Name | Ability |
|---|---|---|---|
| POST | `/logout` | `logout` | — |
| GET | `/user` | `user.index` | — |
| GET | `/user/archive` | `user.archive` | — |
| GET | `/user/setting` | `user.setting` | — |
| POST | `/user/setting/name` | `user.changeNamePost` | self |
| POST | `/user/setting/password` | `user.changePassword` | self, needs current password |
| POST | `/user/profile` | `user.changeProfile` | self |
| GET | `/classess` | `classess.show` | JSON: active class grid HTML |
| GET | `/archive` | `archive.show` | JSON: archived class grid HTML |
| POST | `/createClass` | `createClass.post` | `create` |
| POST | `/joinClass` | `join.post` | — |
| POST | `/updateClass/{class}` | `updateClass.post` | `manage` |
| POST | `/archiveClass/{class}` | `archiveClass.post` | `manage` |
| POST | `/unarchiveClass/{class}` | `unarchiveClass.post` | `manage` |
| POST | `/destroyClass/{class}` | `destroyClass.post` | `manage` |
| POST | `/unenrollClass/{class}` | `unenrollClass.post` | `view` (not owner) |
| GET | `/stream/{class}` | `stream` | `view` |
| POST | `/stream/{class}/unenroll` | `unenroll` | `teach` |
| GET | `/stream/{class}/search?data=` | `stream.search` | `teach` |
| POST | `/stream/{class}/members` | `stream.store` | `teach` |
| POST | `/material/store` | `material.store` | `teach` |
| GET | `/material/{material}` | `material.show` | `view` |
| GET | `/material/{material}/file` | `material.file` | `view`; `?download=1` forces download |
| POST | `/question` | `question.store` | `teach` |
| GET | `/question/{question}` | `question.show` | `view` |
| POST | `/question/turnin` | `turnIn` | student member |
| GET | `/grade/{class}/user/{student}` | `grade.show` | `teach` |
| POST | `/grade/update` | `grade.update` | `teach` |
| POST | `/fire/message` | `send.message` | `view`; throttled 30/min |
| GET | `/chat/{class}` | `chats` | `view`; latest 100 messages |
| POST | `/broadcasting/auth` | — | Registered by Laravel for private channels |
| GET | `/up` | — | Health check |

## 7. Request and response conventions

AJAX requests send `X-CSRF-TOKEN` (from `<meta name="csrf-token">`) and `Accept: application/json`. `public/js/core.js` sets this up once.

| Outcome | Status | Body |
|---|---|---|
| Success | 200 | `{ "success": "Message", "redirectUrl": "optional" }` |
| Business error | 4xx | `{ "error": "Message" }` |
| Validation error | 422 | `{ "message": "...", "errors": { "field": ["Message"] } }` |
| Forbidden | 403 | `{ "error": "..." }` |
| Not found | 404 | `{ "error": "That item no longer exists." }` |
| Session expired | 419 | Shown as "Your session expired. Reload the page…" |
| Rate limited | 429 | `{ "error": ... }` or a validation error on `email` |

Controllers build success and error replies with `$this->success($message, redirectUrl: ...)` and `$this->error($message, $status)` from the base `Controller`.

## 8. Front end

### Design system

`public/css/app.css` sits on top of Bootstrap 5.3 and is split into sections: tokens, base, Bootstrap overrides, app shell, components, pages, utilities and responsive rules.

- **Palette.** A warm paper background (`--paper`), white surfaces, ink-coloured text, one deep green accent (`--accent`) and a clay secondary (`--clay`). Six muted banner colours (`--theme-0` … `--theme-5`) all reach at least 4.5:1 contrast with white text. A class always gets the same colour (`id % 6`).
- **Type.** Fraunces (serif) for page and class titles, Inter for everything else.
- **Shell.** A sticky top bar and a sidebar. At 992 px and above the sidebar is always visible; below that it becomes a slide-in drawer with a backdrop. Escape closes it.
- **Motion.** Short transitions only, all turned off when `prefers-reduced-motion` is set.

### Blade components

| Component | Purpose |
|---|---|
| `<x-avatar :user size="xs\|sm\|lg\|xl">` | Profile photo, or the first letter of the name |
| `<x-field name label type …>` | Label, input or textarea, hint, and an error slot that `forms.js` fills |
| `<x-password-field name label>` | Password input with a show/hide toggle |
| `<x-modal id title size>` | Bootstrap dialog shell; put `.modal-body` / `.modal-footer` in the slot |
| `<x-empty-state icon title>` | Friendly empty placeholder |
| `<x-feed-item :type :item>` | Row for a question or material |
| `<x-class-card :class :archived>` | Card on the home and archive pages, with its own options menu |
| `<x-enrolled-classess>` | Sidebar list of active classes |

### JavaScript modules

`public/js/main.js` imports each module and calls its `init*()` function. Each one does nothing when its markup isn't on the page. Handlers are delegated from `document`, so re-rendered HTML keeps working without re-binding.

| Module | What it does |
|---|---|
| `core.js` | AJAX defaults (CSRF, Accept, `X-Socket-ID`), `toast()`, `errorMessage()`, `el()` (safe element builder), `avatar()`, `debounce()`, flash messages |
| `forms.js` | Submits every `form[data-ajax-form]`; shows field errors and form-level alerts; handles redirects |
| `ui.js` | Sidebar drawer, tab ↔ URL hash sync, copy buttons, password toggles, modal autofocus, shared confirm dialog, avatar preview |
| `classes.js` | Reloads the class grid on the `classes-changed` event; fills the edit dialog |
| `classwork.js` | Question prompt builder, add-people search, remove students, auto-saving grades |
| `chat.js` | Chat history, sending, live updates and polling fallback |

### Declarative hooks

| Attribute | Effect |
|---|---|
| `data-ajax-form` | Submit by AJAX (see `forms.js`) |
| `data-on-success="event-name"` | After success, close the dialog, reset the form and fire the event instead of redirecting |
| `data-reset-on-success` | Clear the form after success |
| `data-error-for="field"` | Where the field's validation message is written |
| `data-confirm-url` (+ `-title`, `-body`, `-label`, `data-confirm-danger`) | Opens the shared confirm dialog and POSTs to the URL on accept |
| `data-copy="text"` | Copies text to the clipboard |
| `data-toggle-password="#id"` | Shows or hides a password field |
| `data-tab="name"` | Class page tab; the active tab is kept in the URL hash (`/stream/1#chats`) |

### Accessibility

- Skip link to the main content.
- Real `<button>` and `<a>` elements everywhere; Bootstrap dropdowns and dialogs provide keyboard support and focus handling.
- Tabs use `role="tablist"` / `role="tab"` / `role="tabpanel"`.
- Inputs have associated labels; errors appear next to the field and the first invalid field gets focus.
- Toasts are in an `aria-live="polite"` region; the chat log is `role="log"`.
- Decorative icons use `aria-hidden="true"`.

## 9. Real-time chat

1. The browser sends `POST /fire/message` with `class_id` and `message`. The sender comes from the session.
2. The server saves the message and broadcasts `App\Events\MessageSent` on the private channel `classroom.{classId}`, skipping the sender's own socket (`toOthers()`).
3. `routes/channels.php` only lets members of the class subscribe to that channel.
4. `public/js/chat.js` listens with Echo and appends messages as text nodes, so message content is never parsed as HTML.

The event uses `ShouldBroadcastNow`, so no queue worker is needed. If the broadcast fails (for example, Reverb is down), the error is logged and the message is still saved and returned to the sender.

**Fallback.** If Echo isn't available (no Vite build, no Reverb keys, or the socket drops), the chat polls `GET /chat/{class}` every 5 seconds while the tab is visible. The status pill in the chat header shows "Live" or "Refreshing every few seconds". Messages are de-duplicated by id, so switching between modes doesn't repeat anything.

Configuration:

```dotenv
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
REVERB_ALLOWED_ORIGINS=classroom.example.com   # optional; defaults to *
```

The `VITE_REVERB_*` variables are read when Vite builds, so rebuild (`npm run build`) after changing them.

## 10. File uploads

### Material attachments

- Allowed types: `jpg, jpeg, png, gif, webp, pdf, doc, docx, ppt, pptx, xls, xlsx, txt`, up to 10 MB.
- New files are stored on the **private** `local` disk (`storage/app/private/materials`). They are served only through `GET /material/{id}/file`, which checks class membership.
- Files uploaded before this refactor may still be on the `public` disk. `Material::fileDisk()` checks both disks, so old attachments keep working.
- Images and PDFs open in the browser; other types download. Responses include `X-Content-Type-Options: nosniff`.
- Deleting a class deletes its attachment files.

### Profile photos

- JPG, PNG, GIF or WebP up to 2 MB. **SVG is rejected** because it can contain scripts.
- Stored in `public/images/` with a random UUID filename. The extension comes from the detected MIME type, not the uploaded filename.
- The previous photo is deleted when a new one is saved.

## 11. Security

| Area | Measure |
|---|---|
| Authorization | One policy (`ClassessPolicy`) checked in every class-scoped action and on the chat channel |
| Account changes | Name and password changes apply to the signed-in user only; a password change needs the current password |
| Passwords | Hashed (bcrypt via the `hashed` cast); at least 8 characters |
| Brute force | Login: 5 attempts per email+IP; sign-up, reset and chat are rate limited |
| Session | Regenerated on sign-in; invalidated and CSRF token rotated on sign-out; sign-out is `POST` only |
| Password reset | Laravel password broker: hashed tokens, 60-minute expiry, request throttling, no account enumeration |
| CSRF | Laravel middleware; AJAX sends `X-CSRF-TOKEN` |
| XSS | Blade escapes output; JavaScript builds DOM with text nodes (`el()`); no user content goes through `innerHTML` |
| Data exposure | API responses return only the fields they need; no `users.*` selects; `password`, `remember_token` and `email_verified_at` are hidden on the model |
| SQL | Eloquent/bindings only; `LIKE` wildcards in search are escaped |
| Uploads | Type and size limits, random names, private storage for attachments, no SVG avatars |
| Headers | `X-Content-Type-Options`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, `Permissions-Policy` (`SecurityHeaders` middleware) |
| Errors | JSON errors return a readable message without stack traces (keep `APP_DEBUG=false` in production) |

## 12. Installation and configuration

### Requirements

- PHP 8.2+ with `mbstring`, `openssl`, `fileinfo`, and `pdo_mysql` and/or `pdo_sqlite`
- Composer 2
- Node.js 18+ and npm (only for the Echo bundle)
- MySQL 8 / MariaDB 10.6+, or SQLite 3

### Steps

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed      # --seed is optional demo data
npm run build                   # optional; enables live chat
php artisan serve
```

### Environment variables

| Variable | Purpose |
|---|---|
| `APP_NAME` | Shown in page titles and emails (default "ME Classroom") |
| `APP_URL` | Used to build links in reset emails |
| `APP_DEBUG` | `false` in production |
| `DB_*` | Database connection |
| `MAIL_*` | Mailer for password reset links (`log` writes them to `storage/logs/laravel.log`) |
| `BROADCAST_CONNECTION` | `reverb` for live chat; `log` or `null` to rely on polling |
| `REVERB_*`, `VITE_REVERB_*` | WebSocket server settings ([§9](#9-real-time-chat)) |
| `SESSION_DRIVER` | `database` by default (needs the `sessions` table from the migrations) |

## 13. Deployment

1. `composer install --no-dev --optimize-autoloader`
2. Set production `.env` values (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, database, mail, Reverb).
3. `php artisan migrate --force`
4. `npm ci && npm run build` if you use live chat.
5. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
6. Make `storage/`, `bootstrap/cache/` and `public/images/` writable by the web server.
7. Point the web root at `public/`.
8. For live chat, keep `php artisan reverb:start` running under a process manager (Supervisor, systemd) and proxy WebSocket traffic to it.

No queue worker is required. Chat broadcasts are sent immediately, and reset emails go out during the request.

## 14. Testing

```bash
php vendor/bin/pest
```

`phpunit.xml` uses in-memory SQLite, the array mailer and the `null` broadcaster, so the tests need no external services. `tests/Feature/ClassroomTest.php` covers:

- every public and signed-in page renders for teachers and students
- sign-up, login throttling, POST-only logout, non-enumerating password reset
- password change needs the current password
- teacher-only class creation; owner-only edit/archive/restore/delete
- joining by code (no duplicates); class list JSON leaks no user data
- non-members can't see a class, its chat or the people search
- only teachers remove students; people search hides members and passwords; adding people skips existing members
- only teachers post work; materials can be posted without a file
- answers are recorded for the signed-in student even if the request is tampered with
- grading is limited to class teachers and to the question's points
- a student sees their own grades, not a classmate's
- only members can post chat messages

The `UserFactory` has a `teacher()` state; the default role is `student`.

## 15. Extending the app

**Add a class-scoped feature**

1. Add a route inside the `auth` group using a `{class}` parameter (it's bound to `Classess` and limited to digits).
2. In the controller, call `Gate::authorize('view' | 'teach' | 'manage', $class)` first.
3. Return `$this->success(...)` / `$this->error(...)` for AJAX actions.
4. In Blade, build the form with `<x-field>` and mark it `data-ajax-form`. It will get validation display, a busy state and toasts with no extra JavaScript.
5. Add a feature test next to the existing ones.

**Add a new tab to the class page**

Add an entry to the `$tabs` array at the top of `resources/views/user/stream.blade.php` and a matching `<section id="..." class="tab-pane fade" role="tabpanel">`. URL hash sync works automatically.

**Change the look**

Edit the tokens at the top of `public/css/app.css`. Component styles use those variables, so a palette or radius change applies everywhere.

## 16. Troubleshooting

| Symptom | Likely cause / fix |
|---|---|
| Chat says "Refreshing every few seconds" | Echo isn't loaded. Run `npm run build` (or `npm run dev`), set `BROADCAST_CONNECTION=reverb` and the `REVERB_*` keys, and start `php artisan reverb:start`. |
| `Vite manifest not found` | No longer fatal: the class page only loads the Vite bundle when `public/build/manifest.json` or `public/hot` exists. |
| Reset emails never arrive | `MAIL_MAILER=log` writes them to `storage/logs/laravel.log`. Configure SMTP for real delivery. |
| "Your session expired" | The CSRF token expired after a long idle period; reload the page. |
| Old attachment returns 404 | The file isn't on the `local` or `public` disk any more. Re-upload it. |
| Profile photo upload fails | Make sure `public/images/` is writable. |
| `SQLSTATE … no such table: sessions` | Run `php artisan migrate`. |

## 17. Refactor audit (October 2026)

What the review found and how each item was resolved.

### Security

| # | Issue | Fix |
|---|---|---|
| 1 | **Any user could change any user's name or password** (`/user/setting/*/{id}` trusted the id in the URL) | Routes act on the signed-in user only; password change needs the current password |
| 2 | **Class list JSON returned `users.*`**, exposing the teacher's password hash and remember token to every student | JSON returns only rendered HTML and a count |
| 3 | **People search returned all user columns**, password hashes included, to any signed-in user, for any class | `teach` permission required; returns id, name, email, role and avatar only; `LIKE` input escaped |
| 4 | Anyone could add or remove students in any class | `teach` permission; only `Student` memberships can be removed |
| 5 | Anyone could grade any answer; grades weren't range-checked | `teach` permission; 0 ≤ grade ≤ points |
| 6 | Grade pages and class pages didn't check membership; questions and materials were readable by anyone signed in | `view` permission everywhere |
| 7 | Students could submit answers as another student (forged `class_member_id`) | Membership comes from the session |
| 8 | **Stored XSS in chat** (messages inserted with template strings) and on the stream (`echo` of raw titles and descriptions) | DOM built with text nodes; Blade escaping |
| 9 | Chat broadcast on one **public** channel: every class saw every other class's messages | Private `classroom.{id}` channel with membership check |
| 10 | Chat sender name and user id came from the request (impersonation) | Taken from the session |
| 11 | Reset tokens were stored in plain text, never expired, and the form revealed whether an email existed | Laravel password broker (hashed, 60-minute expiry, throttled), uniform reply |
| 12 | No login throttling; session not regenerated on login; logout via GET (CSRF logout) | Rate limiter, session regeneration, POST logout with invalidation |
| 13 | SVG profile pictures allowed (script injection); `unlink` on missing files crashed | SVG rejected; safe delete; random names |
| 14 | Material files were publicly reachable under `/storage` and through an unauthenticated route | Private disk + authorized download route |
| 15 | No security headers | `SecurityHeaders` middleware |

### Bugs

| # | Issue | Fix |
|---|---|---|
| 1 | Students saw the **last student's grades** (`$student` leaked from a previous loop) and the page crashed when a class had no students | Grades loaded for the signed-in member |
| 2 | Grade lookups used the student's **first membership in any class**, so grades could come from the wrong class | Membership looked up within the class |
| 3 | Materials without a file failed (`file` was `NOT NULL`) and the form wrongly required a file | Column nullable; field optional |
| 4 | `Route::resource('question')` registered index/edit/update/destroy routes with no methods (500 errors) | `only(['store', 'show'])` |
| 5 | `/class/{className}` and `/account` routes pointed at missing or stub views | Removed |
| 6 | Descriptions and chat messages longer than 255 characters failed | Columns changed to `TEXT` |
| 7 | `@vite` was output **before** `<!DOCTYPE html>` on the class page, which put the browser in quirks mode | Moved into `<head>`; skipped when no build exists |
| 8 | Bootstrap JS was loaded twice (plain and bundle) | Bundle only |
| 9 | Class list JS re-bound click handlers on every refresh, so actions fired several times | Delegated handlers bound once |
| 10 | The sidebar showed the **first class's initial** for every class | Per-class initial and colour |
| 11 | AJAX error handler crashed on non-validation errors (`responseJSON.errors` undefined) | Unified `errorMessage()` |
| 12 | Question page crashed for non-members or a missing question | Model binding plus `view` check |
| 13 | Question page showed a hard-coded date ("Dec 12, 2025") | Real posted date |
| 14 | Joining the same class twice was only partly prevented; adding people inserted duplicates | Checked on join and add |
| 15 | Owners could "unenroll" from their own class, leaving it without them | Blocked with a clear message |
| 16 | Full names had to be unique, so two students with the same name couldn't both sign up | Uniqueness removed (identity is the email) |
| 17 | `ClassMember` declared its namespace twice and expected timestamp columns that don't exist | Cleaned up; `$timestamps = false` |
| 18 | Teacher check compared `'teacher'` against `'Teacher'` in some places | Role constants and `isTeacher()` |
| 19 | Active tab was stored globally in `localStorage`, so opening another class landed on a random tab | Tab kept in the URL hash |

### Code quality (DRY)

- One policy replaces about a dozen copy-pasted "does this teacher own this class" queries.
- One `classList()` / `grid()` path replaces four near-identical class queries.
- `<x-avatar>` replaces eight copies of the photo-or-initial markup; `<x-field>`, `<x-modal>`, `<x-empty-state>` and `<x-feed-item>` replace repeated form, dialog and list markup.
- One confirm dialog driven by data attributes replaces four separate confirm modals.
- One AJAX form handler replaces per-form handlers and inline scripts in Blade files.
- Removed unused components (`ClassWork`, `Materials`, `questions`, `ShowGrade`, `SearchForm`, `ProfileInitial`), dead views, about 500 lines of commented-out CSS and the unused Tailwind pipeline.

### Performance

- Class pages eager-load questions, materials, teachers and students in one pass; the old version ran duplicate queries and re-queried in nested components.
- Chat history is limited to the latest 100 messages and indexed on `(class_id, id)`; memberships are indexed on `(user_id, class_id)`.
- Profile initials no longer query the database on every render.
- Minified jQuery; a single Bootstrap bundle; fonts loaded with `preconnect` and `display=swap`; lazy-loaded avatars; cache-busted static assets.
- Chat polling pauses while the browser tab is hidden.
