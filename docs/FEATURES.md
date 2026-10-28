# Feature Guide

A map of every feature and the files behind it, followed by step-by-step recipes for adding your own.

- Part 1, [Feature map](#part-1--feature-map), answers "where is the code for X?"
- Part 2, [Adding a feature](#part-2--adding-a-feature), answers "how do I build something new?" with a complete worked example.

For architecture, security and deployment, see [DOCUMENTATION.md](DOCUMENTATION.md).

---

## How a feature is wired

Almost every feature follows the same path through the same kinds of files:

```
routes/web.php                     URL → controller method
app/Http/Controllers/…Controller   validate input, check permission, save, reply
app/Policies/ClassessPolicy.php    who is allowed (view / teach / manage)
app/Models/…                       database table + relationships
database/migrations/…              table structure
resources/views/…                  the page or dialog (Blade)
public/js/…                        browser behaviour (only if needed)
public/css/app.css                 styling (only if needed)
tests/Feature/ClassroomTest.php    proof that it works
```

Folder cheat sheet:

| Folder | Holds |
|---|---|
| `app/Http/Controllers/` | One controller per area (classes, stream, materials, …) |
| `app/Models/` | One model per table |
| `app/Policies/` | Permission rules |
| `database/migrations/` | Table definitions, run with `php artisan migrate` |
| `resources/views/layouts/` | Page shells (`app`, `auth`, `clean`) and shared partials |
| `resources/views/components/` | Reusable pieces: `<x-field>`, `<x-modal>`, `<x-avatar>`, … |
| `resources/views/user/` | Signed-in pages |
| `resources/views/auth/` | Sign-in, sign-up and password pages |
| `public/js/` | Browser modules, loaded by `main.js` |
| `public/css/app.css` | All styles (tokens at the top) |
| `tests/Feature/` | Feature tests |

---

## Part 1 — Feature map

Each feature lists its routes, then every file it touches.

### 1. Landing page

The public home page with the sign-up and sign-in buttons.

| Layer | File |
|---|---|
| Route | `routes/web.php` → `GET /` (`home`); signed-in users are redirected to `/user` |
| View | `resources/views/home/index.blade.php` |
| Layout | `resources/views/layouts/clean.blade.php` |
| Styles | `public/css/app.css` → section "Landing" |

### 2. Sign up, sign in, sign out

| Layer | File |
|---|---|
| Routes | `GET/POST /signup`, `GET/POST /login`, `POST /logout` |
| Controller | `app/Http/Controllers/AuthController.php` (`signup`, `signupPost`, `login`, `loginPost`, `logout`) |
| Model | `app/Models/User.php` (`role`, `isTeacher()`) |
| Migration | `database/migrations/0001_01_01_000000_create_users_table.php` |
| Views | `resources/views/auth/login.blade.php`, `auth/signup.blade.php`, `auth/partials/terms.blade.php` |
| Layout | `resources/views/layouts/auth.blade.php` |
| Components | `<x-field>`, `<x-password-field>` |
| JS | `public/js/forms.js` (form submit), `public/js/ui.js` (show/hide password) |
| Styles | `app.css` → "Auth" (`.auth-card`, `.role-picker`) |
| Tests | "signs a new user up and in", "throttles repeated failed logins", "only logs out through POST" |

Rules: login allows 5 tries per email and IP before locking out briefly; the session is regenerated on login; sign-out is POST only.

### 3. Forgot / reset password

| Layer | File |
|---|---|
| Routes | `GET/POST /forget-password`, `GET /reset-password/{token}` (`password.reset`), `POST /reset-password` |
| Controller | `app/Http/Controllers/ForgotPasswordController.php` |
| Config | `config/auth.php` → `passwords.users` (60-minute expiry) |
| Table | `password_reset_tokens` (in the users migration) |
| Views | `resources/views/auth/forgetPassword.blade.php`, `auth/forgetPasswordLink.blade.php` |
| Email | Laravel's built-in reset notification (set `MAIL_*` in `.env`) |
| Tests | "does not reveal whether an email is registered" |

### 4. Home page (class list) and archive

| Layer | File |
|---|---|
| Routes | `GET /user` (`user.index`), `GET /user/archive` (`user.archive`); JSON refresh: `GET /classess`, `GET /archive` |
| Controllers | `UserController@index`, `UserController@archive`; `ClassController@show`, `@showArchiveClass` |
| Model | `Classess` → `forMember()` scope, `owner()` |
| Views | `resources/views/user/index.blade.php`, `user/archiveclass.blade.php` |
| Partials | `layouts/partials/classess.blade.php` (the grid), `layouts/partials/homeModal.blade.php` (create/join/edit dialogs) |
| Components | `<x-class-card>`, `<x-empty-state>` |
| JS | `public/js/classes.js` (grid refresh on `classes-changed`, fills the edit dialog) |
| Styles | `app.css` → "Class card grid" |

### 5. Create, edit, archive, restore, delete a class

| Layer | File |
|---|---|
| Routes | `POST /createClass`, `/updateClass/{class}`, `/archiveClass/{class}`, `/unarchiveClass/{class}`, `/destroyClass/{class}` |
| Controller | `app/Http/Controllers/ClassController.php` |
| Permission | `ClassessPolicy@create` (teachers), `ClassessPolicy@manage` (owner only) |
| Model | `Classess` (`generateCode()`, `STATUS_ACTIVE`, `STATUS_ARCHIVED`) |
| Migrations | `2025_01_23_065540_class_table.php`, `2025_01_24_122053_class_members_table.php` |
| Views | Create/edit dialogs in `homeModal.blade.php`; menu items in `components/class-card.blade.php` |
| Confirm dialog | `layouts/partials/confirmModal.blade.php` + `ui.js` → `initConfirm()` |
| Tests | "lets teachers create classes and blocks students", "stops other teachers from managing a class they do not own" |

Deleting a class also deletes its attachment files (`Material::deleteFile()`).

### 6. Join or leave a class

| Layer | File |
|---|---|
| Routes | `POST /joinClass` (throttled), `POST /unenrollClass/{class}`, `POST /resetClassCode/{class}` (owner) |
| Controller | `ClassController@join`, `@unenrollClass`, `@resetCode` |
| Model | `ClassMember` (`roleFor()`) |
| Views | Join dialog in `homeModal.blade.php`; "Leave class" in `class-card.blade.php` |
| Tests | "joins a class by code once", "never grants teacher rights through a class code", "lets the owner reset a leaked class code" |

Rules: a class code always makes you a **student**, and codes are case-insensitive.

### 7. Class page (stream and tabs)

The page at `/stream/{id}` with the Stream, Classwork, People, Grades and Chat tabs.

| Layer | File |
|---|---|
| Route | `GET /stream/{class}` (`stream`) |
| Controller | `StreamController@show` (builds `$feed`, `$canTeach`, the student's `$grades`) |
| Permission | `view` to open; `teach` decides which buttons appear |
| View | `resources/views/user/stream.blade.php` (the `$tabs` array at the top defines the tabs) |
| Teacher dialogs | `resources/views/user/partials/classworkModals.blade.php` |
| Components | `<x-feed-item>`, `<x-empty-state>`, `<x-avatar>` |
| JS | `ui.js` → `initTabs()` (keeps the tab in the URL, e.g. `#people`) |
| Styles | `app.css` → "Class header banner", "Tabs", "Feed / list rows" |
| Tests | "keeps non-members out of a class" |

### 8. Materials

A post with a description and an optional file.

| Layer | File |
|---|---|
| Routes | `POST /material/store`, `GET /material/{material}`, `GET /material/{material}/file`, `POST /material/{material}/delete` |
| Controller | `app/Http/Controllers/MaterialController.php` (`ALLOWED_TYPES` lists accepted files) |
| Permission | `teach` to post, `view` to read and download |
| Model | `app/Models/Material.php` (`fileDisk()`, `isImage()`, `deleteFile()`) |
| Migrations | `2025_01_30_062903_create_materials_table.php`, `2026_10_26_000000_fix_content_column_types.php` |
| Storage | New files: `storage/app/private/materials/`; older files may be in `storage/app/public/materials/` |
| Views | Form: "New material" in `classworkModals.blade.php`; page: `resources/views/user/class/material.blade.php` |
| Tests | "lets only class teachers post questions and materials" |

### 9. Questions and answers

| Layer | File |
|---|---|
| Routes | `POST /question` (`question.store`), `GET /question/{question}` (`question.show`), `POST /question/{question}/delete` (`question.destroy`), `POST /question/turnin` (`turnIn`) |
| Controllers | `QuestionController.php` (post, view), `ClassMemberQuestionAnswersController.php` (turn in) |
| Models | `Question.php` (`items()`, `isPastDue()`), `ClassMemberQuestionAnswers.php` (`isGraded()`) |
| Migrations | `2025_01_30_062939_create_questions_table.php`, `2025_01_30_065128_create_class_member_question_answers_table.php` |
| Views | Form: "New question" in `classworkModals.blade.php`; page: `resources/views/user/class/question.blade.php` |
| JS | `classwork.js` → `initQuestionBuilder()` (the "Items" counter that adds prompt inputs) |
| Tests | "records answers for the signed-in student only" |

Rules:

- Students answer as themselves only.
- They can edit an answer until it is graded **or** the due date passes.
- A first submission after the due date is accepted and marked **Late**.
- The status badge comes from `<x-work-status>`, which is also used on the Grades tab and the grade page.

### 10. Grades

| Layer | File |
|---|---|
| Routes | `GET /grade/{class}/user/{student}` (`grade.show`), `POST /grade/update` (`grade.update`) |
| Controller | `app/Http/Controllers/GradeController.php` |
| Permission | `teach` |
| Views | Teacher: `resources/views/user/class/grade.blade.php`; student table: Grades tab in `stream.blade.php` |
| JS | `classwork.js` → `initGrading()` (saves 0.5 s after typing stops) |
| Tests | "grades only within the class and within the points limit", "shows each student their own grades" |

### 11. People (add and remove)

| Layer | File |
|---|---|
| Routes | `GET /stream/{class}/search`, `POST /stream/{class}/members`, `POST /stream/{class}/unenroll`, `POST /stream/{class}/teachers/{teacher}/remove` |
| Controller | `StreamController@search`, `@store`, `@unenrollStudents`, `@removeTeacher` |
| Permission | `teach`; `manage` (owner) to add or remove co-teachers |
| Views | People tab in `stream.blade.php`; "Add people" dialog in `classworkModals.blade.php` |
| JS | `classwork.js` → `initPeoplePicker()`, `initUnenroll()` |
| Tests | "lets only teachers remove students", "searches people by email…", "adds people without creating duplicate memberships", "lets only the owner add co-teachers and remove them", "only finds people by their exact email" |

Rules: search matches the **exact** email only. Only the owner can make someone a co-teacher.

### 12. Class chat

| Layer | File |
|---|---|
| Routes | `POST /fire/message` (`send.message`), `GET /chat/{class}` (`chats`) |
| Controller | `app/Http/Controllers/ChatAppController.php` |
| Event | `app/Events/MessageSent.php` (private channel `classroom.{id}`) |
| Channel rule | `routes/channels.php` (members only) |
| Model | `app/Models/ClassroomChat.php` (`toChatPayload()`) |
| Migration | `2025_02_11_084303_create_classroom_chats_table.php` |
| View | Chat tab in `stream.blade.php` (`#chat` element with `data-*` URLs) |
| JS | `public/js/chat.js`; live updates use `resources/js/echo.js` (built by Vite) |
| Config | `.env` → `BROADCAST_CONNECTION`, `REVERB_*`, `VITE_REVERB_*`; `config/reverb.php` |
| Tests | "accepts chat messages from class members only" |

Without Reverb, the chat checks for new messages every 5 seconds.

### Archived classes (applies to features 7–12)

Every action that changes a class calls `$this->ensureActive($class)` (defined in `app/Http/Controllers/Controller.php`). On an archived class it returns 403 "This class is archived…". The class page uses `$canEdit` (from `StreamController@show`) to hide editing buttons. Test: "makes archived classes read-only".

### 13. Settings and profile photo

| Layer | File |
|---|---|
| Routes | `GET /user/setting`, `POST /user/setting/name`, `POST /user/setting/password`, `POST /user/profile` |
| Controller | `UserController@setting`, `@changeNamePost`, `@changePassword`, `@changeProfile` |
| Model | `User` (`avatar_url`, `initial`) |
| Views | `resources/views/user/setting/setting.blade.php`, `layouts/partials/profileModal.blade.php` |
| Storage | Photos in `public/images/` |
| JS | `ui.js` → `initAvatarPicker()` (preview before upload) |
| Tests | "changes only the signed-in user's password and requires the current one" |

### 14. App shell (top bar, sidebar, toasts)

| Piece | File |
|---|---|
| HTML shell, CSS/JS loading, toasts | `resources/views/layouts/clean.blade.php` |
| Signed-in layout | `resources/views/layouts/app.blade.php` |
| Top bar and account menu | `layouts/partials/header.blade.php` |
| Sidebar | `layouts/partials/sidebar.blade.php` + `components/enrolled-classess.blade.php` + `app/View/Components/EnrolledClassess.php` |
| `$authUser` in every view | `app/Providers/ViewServiceProvider.php` |
| Drawer, toasts | `public/js/ui.js` → `initSidebar()`; `public/js/core.js` → `toast()` |
| Security headers | `app/Http/Middleware/SecurityHeaders.php`, registered in `bootstrap/app.php` |

---

## Part 2 — Adding a feature

### The checklist

Use this for any new feature. Skip a step when it doesn't apply.

1. **Table** — `php artisan make:migration create_things_table`, then `php artisan migrate`.
2. **Model** — `php artisan make:model Thing`; add `$fillable` and relationships.
3. **Relationship on the class** — add `things()` to `app/Models/Classess.php` if it belongs to a class.
4. **Controller** — `php artisan make:controller ThingController`. In each method: validate, `Gate::authorize(...)`, call `$this->ensureActive($class)` if it changes class content, save, return `$this->success(...)`.
5. **Route** — add it to `routes/web.php` inside the `auth` group. Use `{class}` for the class id.
6. **View** — build forms with `<x-field>` inside `<x-modal>`, and add `data-ajax-form` to the `<form>`.
7. **JavaScript** — usually none. Only add a module in `public/js/` if the feature needs custom behaviour.
8. **Styles** — reuse existing classes (`panel`, `feed-item`, `pill`, `btn`…). Add CSS to `app.css` only when needed.
9. **Test** — add a test to `tests/Feature/ClassroomTest.php` and run `php vendor/bin/pest`.
10. **Docs** — add the feature to Part 1 of this file.

### Worked example: class announcements

Teachers post a short announcement. It appears at the top of the Stream tab for everyone in the class.

Files you'll touch:

```
database/migrations/xxxx_create_announcements_table.php   (new)
app/Models/Announcement.php                               (new)
app/Models/Classess.php                                   (add relationship)
app/Http/Controllers/AnnouncementController.php           (new)
routes/web.php                                            (add route)
app/Http/Controllers/StreamController.php                 (load announcements)
resources/views/user/stream.blade.php                     (show + button)
resources/views/user/partials/classworkModals.blade.php   (form dialog)
tests/Feature/ClassroomTest.php                           (test)
```

#### Step 1 — Migration

```bash
php artisan make:migration create_announcements_table
```

```php
// database/migrations/xxxx_create_announcements_table.php
public function up(): void
{
    Schema::create('announcements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')->constrained('classess')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->text('body');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('announcements');
}
```

```bash
php artisan migrate
```

`cascadeOnDelete()` removes announcements automatically when their class is deleted.

#### Step 2 — Model

```php
// app/Models/Announcement.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['class_id', 'user_id', 'body'];

    public function classroom()
    {
        return $this->belongsTo(Classess::class, 'class_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Step 3 — Relationship on the class

```php
// app/Models/Classess.php — next to questions() and materials()
public function announcements()
{
    return $this->hasMany(Announcement::class, 'class_id');
}
```

#### Step 4 — Controller

```php
// app/Http/Controllers/AnnouncementController.php
<?php

namespace App\Http\Controllers;

use App\Models\Classess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AnnouncementController extends Controller
{
    public function store(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);              // teachers of this class only
        $this->ensureActive($class);                   // archived classes are read-only

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $class->announcements()->create([
            'user_id' => $request->user()->id,          // never trust a user id from the form
            'body' => $data['body'],
        ]);

        return $this->success('Announcement posted.', redirectUrl: route('stream', $class->id).'#stream');
    }
}
```

Which permission should you use?

| Use | When the action… |
|---|---|
| `Gate::authorize('view', $class)` | any member may do it (read, chat) |
| `Gate::authorize('teach', $class)` | only teachers of the class may do it (post, grade, manage people) |
| `Gate::authorize('manage', $class)` | only the owner may do it (edit, archive, delete the class) |

#### Step 5 — Route

```php
// routes/web.php — inside Route::middleware('auth')->group(...)
use App\Http\Controllers\AnnouncementController;

Route::post('/stream/{class}/announcements', [AnnouncementController::class, 'store'])
    ->name('announcement.store');
```

`{class}` is turned into a `Classess` model automatically, and only digits are accepted (see `AppServiceProvider`).

#### Step 6 — Load the data for the page

```php
// app/Http/Controllers/StreamController.php — in show(), add to $class->load([...])
'announcements' => fn ($q) => $q->with('user')->latest(),
```

The view can now use `$class->announcements`.

#### Step 7 — The form dialog

```blade
{{-- resources/views/user/partials/classworkModals.blade.php --}}
<x-modal id="createAnnouncementModal" title="New announcement">
    <form action="{{ route('announcement.store', $class->id) }}" data-ajax-form novalidate>
        <div class="modal-body">
            <div class="form-alert"></div>
            <x-field name="body" label="Message" type="textarea" rows="4" maxlength="2000" required />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Post</button>
        </div>
    </form>
</x-modal>
```

With `data-ajax-form` on the form, `public/js/forms.js` already handles:

- the CSRF token
- the "Working…" button state
- validation errors under each field
- the success toast
- the redirect

No new JavaScript is needed.

#### Step 8 — Show it on the Stream tab

```blade
{{-- resources/views/user/stream.blade.php — at the top of <section id="stream" …> --}}
@if ($canTeach)
    <button type="button" class="btn btn-quiet mb-3" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
        <i class="fa-solid fa-bullhorn" aria-hidden="true"></i> Announce something
    </button>
@endif

@foreach ($class->announcements as $announcement)
    <article class="panel mb-3">
        <div class="panel-body d-flex gap-3">
            <x-avatar :user="$announcement->user" size="sm" />
            <div class="min-w-0">
                <div class="small text-muted-2">
                    {{ $announcement->user->name }} · {{ $announcement->created_at->diffForHumans() }}
                </div>
                <p class="prose mb-0">{{ $announcement->body }}</p>
            </div>
        </div>
    </article>
@endforeach
```

Always print user text with `{{ }}`, which escapes it. Never use `{!! !!}` for user content.

#### Step 9 — Test

```php
// tests/Feature/ClassroomTest.php
it('lets only teachers post announcements', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->create();
    $class = makeClass($teacher, [$student]);

    $this->actingAs($student)
         ->postJson("/stream/{$class->id}/announcements", ['body' => 'Hi'])
         ->assertForbidden();

    $this->actingAs($teacher)
         ->postJson("/stream/{$class->id}/announcements", ['body' => 'Quiz on Friday'])
         ->assertOk();

    $this->actingAs($student)->get("/stream/{$class->id}")->assertSee('Quiz on Friday');
});
```

```bash
php vendor/bin/pest
```

#### Step 10 — Document it

Add an "Announcements" entry to Part 1 of this file with its route, controller, model, migration, views and test.

---

### Smaller recipes

#### Add a new page

1. Route: `Route::get('/user/help', [UserController::class, 'help'])->name('user.help');`
2. Controller method: `return view('user.help');`
3. View `resources/views/user/help.blade.php`:

   ```blade
   @extends('layouts.app')
   @section('title', 'Help')
   @section('crumb', 'Help')

   @section('content')
       <div class="page-narrow">
           <header class="page-header"><h1 class="page-title">Help</h1></header>
           <div class="panel"><div class="panel-body">…</div></div>
       </div>
   @endsection
   ```

4. Sidebar link (optional): copy one `<a class="nav-link-item">` in `layouts/partials/sidebar.blade.php`.

Use `page` for wide pages and `page-narrow` for reading or form pages.

#### Add a tab to the class page

1. Add an entry to the `$tabs` array at the top of `resources/views/user/stream.blade.php`:
   `'files' => ['Files', 'fa-regular fa-file'],`
2. Add the panel inside `<div class="tab-content">`:

   ```blade
   <section id="files" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-files" tabindex="0">
       …
   </section>
   ```

The URL hash (`/stream/1#files`) works automatically.

#### Add a confirm-and-POST action (no form needed)

Any button with `data-confirm-url` opens the shared confirm dialog and POSTs when the user accepts:

```blade
<button type="button" class="btn btn-quiet"
        data-confirm-url="{{ route('announcement.destroy', $announcement->id) }}"
        data-confirm-title="Delete this announcement?"
        data-confirm-body="This can't be undone."
        data-confirm-label="Delete"
        data-confirm-danger>
    Delete
</button>
```

The controller returns `$this->success('Deleted.', redirectUrl: …)`. Handled by `public/js/ui.js` → `initConfirm()`.

#### Add a reusable Blade component

1. Create `resources/views/components/stat-card.blade.php`:

   ```blade
   @props(['label', 'value'])
   <div {{ $attributes->class('panel panel-body') }}>
       <div class="small text-muted-2">{{ $label }}</div>
       <div class="fs-4 fw-semibold">{{ $value }}</div>
   </div>
   ```

2. Use it anywhere: `<x-stat-card label="Students" :value="$students->count()" />`

#### Add JavaScript behaviour

1. Create `public/js/announcements.js`:

   ```js
   import { toast } from './core.js';

   export function initAnnouncements() {
       const root = document.querySelector('[data-announcements]');
       if (!root) return;            // do nothing on other pages
       // …
   }
   ```

2. Register it in `public/js/main.js`:

   ```js
   import { initAnnouncements } from './announcements.js';
   initAnnouncements();
   ```

Rules:

- Find elements with `data-*` attributes.
- Create elements with `el()` from `core.js` rather than `innerHTML`; it keeps user text safe.
- Use `$.post(...)` / `$.getJSON(...)`; the CSRF header is added for you.
- Show errors with `toast(errorMessage(xhr), 'error')`.

#### Add a new permission

1. Add a method to `app/Policies/ClassessPolicy.php`:

   ```php
   public function moderateChat(User $user, Classess $class): bool
   {
       return $this->teach($user, $class);
   }
   ```

2. Use it in a controller: `Gate::authorize('moderateChat', $class);`
3. Use it in Blade: `@can('moderateChat', $class) … @endcan`

#### Add styles

1. Use the design tokens at the top of `public/css/app.css` (`--accent`, `--line`, `--radius`, `--shadow-sm`, …) instead of hard-coded colours.
2. Put the new rules in the matching section ("Components" or "Pages").
3. Add phone-size tweaks to the "Responsive" section at the bottom.

#### Add a column to an existing table

```bash
php artisan make:migration add_color_to_classess_table
```

```php
public function up(): void
{
    Schema::table('classess', fn (Blueprint $t) => $t->string('color')->nullable());
}

public function down(): void
{
    Schema::table('classess', fn (Blueprint $t) => $t->dropColumn('color'));
}
```

Then:

- add `'color'` to `$fillable` in the model
- add a validation rule in the controller
- add an `<x-field>` to the form

---

## Quick reference

| I want to… | Go to |
|---|---|
| Change who can do something | `app/Policies/ClassessPolicy.php` |
| Change a validation rule | The controller method that saves the data |
| Change a URL | `routes/web.php` (keep the route **name** so links keep working) |
| Change page text or layout | `resources/views/…` (see the feature map above) |
| Change colours, spacing or fonts | Tokens at the top of `public/css/app.css` |
| Change how forms submit | `public/js/forms.js` |
| Change demo data | `database/seeders/DatabaseSeeder.php`, then `php artisan migrate:fresh --seed` |
| Run the tests | `php vendor/bin/pest` |
