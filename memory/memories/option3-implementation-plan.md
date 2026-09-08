---
name: option3-implementation-plan
description: Step-by-step implementation plan for the chosen Option 3 stack (git-backed Markdown rendered server-side) plus the relative-path image workflow
metadata:
  type: project
  created: 2026-09-08 23:30
  updated: 2026-09-09 00:32
---

Full build plan for [[tech-stack-options]] Option 3. Repo is currently empty (only
`memory/`/`wdyt/`/`CLAUDE.md`) — no `git init`, no Laravel install yet. User runs all commands
themselves; this file is the checklist, not a transcript of what's been run.

## 0. Baseline decisions carried over (common to all 3 options)
- Laravel (latest) + Filament v3 for admin
- MySQL/MariaDB
- Pure CSS (no Sass, no Tailwind) — design system in `resources/css/app.css`, see step 3
- Native VPS install, no Docker
- Cloudflare R2 — reserved for video only under Option 3 (images are git-committed, see below)
- Hand-rolled `Auth` facade login — no Breeze/Fortify/Jetstream/Sanctum (no self-registration)
- Admin-manual learner account creation via Filament ("Create Learner"); Scalev webhook automation
  is a deferred later phase, not MVP

## 1. Project scaffolding
```bash
git init
composer create-project laravel/laravel . "^11.0"
```
Since `.` isn't empty (memory/wdyt/CLAUDE.md already exist), `composer create-project` may refuse
a non-empty dir — if so, scaffold into a temp dir and merge, or use
`laravel new arti-lms-tmp && rsync` contents in minus `.git`. Confirm before overwriting anything.

## 2. Core packages
```bash
composer require filament/filament:"^3.0"
composer require league/commonmark
composer require spatie/yaml-front-matter
php artisan filament:install --panels
```

## 3. Pure CSS design system (Vite ships Tailwind by default — strip it) — DONE 2026-09-08 23:56
```bash
npm uninstall tailwindcss postcss autoprefixer
rm tailwind.config.js postcss.config.js
```
- No preprocessor added (dropped the earlier Sass plan too — plain CSS via Vite's native CSS
  handling, no build-step dependency at all).
- `resources/css/app.css` rewritten as a token-based design system (CSS custom properties for
  color/spacing/typography, then component classes: `.app-shell`, `.sidebar`, `.topbar` /
  `.topbar--dark`, `.tree-item`, `.card`/`.card-grid`, `.badge`, `.btn`, `.progress`), derived from
  the two reference screenshots in `wdyt/` (learner "My Purchases" card grid +
  course-reader sidebar/progress-bar/content layout).
- `resources/views/welcome.blade.php` replaced (the stock Tailwind-based Laravel welcome page was
  irrelevant to this project and now unstyled) with a minimal placeholder using the new shell/topbar
  classes.
- Verified with `npm run build` — builds clean, `public/build/assets/app-*.css` ~6.3kB.

## 4. Database schema — DONE 2026-09-09 08:00
```bash
php artisan make:model Course -m
php artisan make:model CourseAccess -m
php artisan make:model Progress -m
```
- `courses`: `id`, `slug` (unique), `title`, `published_at` (nullable timestamp)
- `course_access` (model table name overridden from Laravel's default `course_accesses` via
  `protected $table`): `user_id`, `course_id` (both `foreignId`→`constrained()->cascadeOnDelete()`),
  `granted_at`, unique on `(user_id, course_id)` — this is what the content route checks instead of
  a `chapters` FK (there is no `chapters` table in Option 3; chapters are files, not rows)
- `progress`: `user_id`, `course_id`, `chapter_slug` (string), `completed_at` (nullable), unique on
  `(user_id, course_id, chapter_slug)`
- `users` table: kept Laravel's default plus one addition — `role` string column (default
  `learner`); `User` model implements `Filament\Models\Contracts\FilamentUser` with
  `canAccessPanel()` returning `$this->role === 'admin'` (decided: Filament's own gate, no second
  admins table)
- All three new models have `$fillable`, `casts()`, and `belongsTo`/`hasMany` relations wired to
  each other and to `User`

```bash
php artisan migrate
```
- **Deviation from step 0's "MySQL/MariaDB" baseline execution detail**: Laravel 11's default
  scaffold set `.env` to `DB_CONNECTION=sqlite`, but this PHP install has no `pdo_sqlite` driver
  (only `pdo_mysql`) — switched `.env` to `mysql` / db `arti_lms` (created via Laragon's bundled
  MySQL 8.0 client at `C:\laragon6\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe`) to match the
  already-decided baseline. Migrated clean on first try.

## 5. Content directory + first course — DONE 2026-09-09 00:07
```bash
mkdir -p resources/content/example-course/01-intro/images
```
- `resources/content/{course}/{slug}/index.md` with YAML front matter: `title`, `order`,
  `published: true`
- Images alongside: `resources/content/{course}/{slug}/images/*.png`, referenced as
  `./images/foo.png` in the Markdown (see [[tech-stack-options]] Image workflow section)
- A `manifest.yaml` per course (or a directory scan cached via `Cache::remember`) drives the
  sidebar/chapter order — don't hand-roll a fragile filesystem-order dependency
- **Manifest schema decided** (resolves the step-11-adjacent open decision): `manifest.yaml` per
  course has `title`, `slug`, `chapters` (a plain list of chapter-slug entries) — chapter order is
  the list order, not a separate numeric field. `index.md` front matter still carries its own
  `order` key too, kept as a redundant/display value, not the source of truth for sidebar sequence.
- Created `resources/content/example-course/manifest.yaml` (one chapter: `01-intro`) and
  `resources/content/example-course/01-intro/index.md` (front matter `title`/`order`/`published`,
  body references `./images/placeholder.png`) plus a real 1×1 `placeholder.png` (67 bytes, valid
  PNG) so step 6's image route has a real file to serve, not a dangling reference.

## 6. Content controller + routes — DONE 2026-09-09 00:11
- Access check extracted to `app/Http/Middleware/EnsureCourseAccess.php` (alias `course.access`,
  registered in `bootstrap/app.php`'s `withMiddleware`) instead of a form request — checks
  `$request->user()->courseAccess()->where('course_id', $course->id)->exists()`, `abort_unless`
  403. Applied at the route-group level so both `show` and `image` share it without duplication.
- `Course` got `getRouteKeyName(): string { return 'slug'; }` so `{course}` in the route binds by
  the `courses.slug` column (matching the `resources/content/{course-slug}/...` directory name),
  not the numeric `id`.
- `app/Http/Controllers/ContentController.php`:
  - `show(Course $course, string $slug)` — reads `resources/content/{course->slug}/{slug}/index.md`
    via `resource_path()`, 404s if the file doesn't exist; `YamlFrontMatter::parseFile()`, 404s if
    `published` front matter isn't truthy; converts body via `CommonMarkConverter`; loads the
    course's `manifest.yaml` (via `Symfony\Component\Yaml\Yaml::parseFile`, transitively available
    through `spatie/yaml-front-matter`) cached under `content.manifest.{course-slug}` for an hour;
    renders `resources/views/content/show.blade.php`
  - `image(Course $course, string $slug, string $file)` — `response()->file()` on
    `resources/content/{course->slug}/{slug}/images/{file}`, 404s if missing or if `$file` contains
    `..` (path-traversal guard on the route-captured filename)
- `resources/views/content/show.blade.php` — sidebar built from `sidebar__nav`/`tree-item` classes
  (step 3's design system) looping the manifest's `chapters` list, `tree-item--active` on the
  current slug; body content in `.content` via `{!! $html !!}`
- Routes in `routes/web.php`, named `content.show` / `content.image`:
```php
Route::middleware(['auth', 'course.access'])->prefix('learn/{course}')->group(function () {
    Route::get('{slug}', [ContentController::class, 'show'])->name('content.show');
    Route::get('{slug}/images/{file}', [ContentController::class, 'image'])->name('content.image');
});
```
- Verified by calling `show()`/`image()` directly via `artisan tinker` against a tinker-seeded
  `test@example.com` user + `example-course` `CourseAccess` grant (no login flow exists yet to
  drive this through an actual HTTP request — see gap noted below): rendered HTML contained the
  parsed `<h1>Introduction</h1>`, the untouched `./images/placeholder.png` relative src, and the
  active sidebar nav item; `image()` returned a 200 `BinaryFileResponse`.
- **Gap closed 2026-09-09 00:18**: `app/Http/Controllers/AuthController.php`
  (`create`/`store`/`destroy`) + `guest`-gated `login` (GET/POST) and `auth`-gated `logout` (POST)
  routes in `routes/web.php` + `resources/views/auth/login.blade.php` (reuses `.app-shell`/`.card`,
  new `.form-field`/`.form-label`/`.form-input`/`.form-error`/`.auth-content`/`.auth-card` classes
  added to `resources/css/app.css`). `store()` uses `Auth::attempt()` + session regenerate +
  `redirect()->intended('/')`; failure throws `ValidationException::withMessages()` with a literal
  message string (no `lang/` dir published in this Laravel 11 install, so `__('auth.failed')` would
  have echoed the raw key). Verified via `php artisan route:list` (login GET/POST + logout POST
  present) and `php -l`; **not yet verified end-to-end in a browser** — that's the next-session
  step using the tinker-seeded `test@example.com` + `example-course` access grant.

## 7. Progress tracking
- `POST /progress` route, same `auth` middleware, plain Blade form/fetch from the same-origin
  page (no cross-frame bridge needed — this was Option 2's hard part, gone in Option 3)
- Upserts into `progress` on `(user_id, course_id, chapter_slug)`

## 8. Filament admin
```bash
php artisan make:filament-resource Course
```
- Course resource: manage `slug`/`title`/`published_at` only (chapters aren't DB rows — Filament
  doesn't author content in Option 3)
- A "Create Learner" custom Filament page/action: creates a `User` + a `CourseAccess` grant in one
  step (admin-manual, no self-registration flow to build)
- Consider a Filament `CourseAccess` relation-manager on the Course resource for granting/revoking
  learner access without a separate screen

## 9. Cloudflare R2 (video only under Option 3)
```bash
composer require league/flysystem-aws-s3-v3
```
- Configure an `r2` disk in `config/filesystems.php` (S3-compatible driver, R2 endpoint/credentials
  in `.env`)
- Only referenced manually in Markdown via pasted R2 URLs for video embeds — images stay
  git-committed per the chosen workflow, so this disk is *not* wired into the content/image routes

## 10. Sass + frontend
- Learner-facing chapter layout: plain Blade + Sass, no Livewire/Alpine (matches Option 1's
  frontend approach, since Option 3 also renders everything server-side)
- jQuery only if actually needed for interactivity — don't add it speculatively

## 11. Deploy considerations (native VPS, no Docker)
- `resources/content/` ships via the same git deploy as the app code — no separate content deploy
  pipeline needed, since content lives in the same repo (or as a git submodule if the ebook content
  repo stays separate — decide this only if the ebook project and arti-lms end up as two repos)
- Cache the manifest/directory-scan on deploy (`php artisan cache:clear` + warm) since content only
  changes on `git pull`, not per-request

## Open decisions for next session
- Single repo (content lives inside `arti-lms`) vs. content as a separate repo/submodule pulled in
  at deploy — not yet decided, matters once the ebook migration ([[arti-framework-context]])
  actually happens
- Exact `manifest.yaml` schema vs. pure directory-scan for chapter ordering

## Change log
- 2026-09-09 00:32 — step 6 end-to-end HTTP verification done via `php artisan serve` + curl
  (tinker-seeded `test@example.com` / `password`, `example-course` access grant): login, content
  render, image route, path-traversal guard, and unauthenticated-redirect all confirmed working.
  Found two real gaps in the process: (1) the Laragon vhost `auto.arti-lms.local.conf`
  (`C:\laragon6\etc\apache2\sites-enabled\`) had `DocumentRoot` pointing at the project root instead
  of `public/`, so `arti-lms.local` directory-listed the raw repo (`.env` included) and `/login`
  404'd through the vhost — fixed by the user, not yet re-verified in an actual browser through the
  corrected vhost; (2) `resources/views/content/show.blade.php`'s topbar has no logout
  link/button — the `/logout` route and `AuthController@destroy` work, but nothing in the UI reaches
  them. Both are open items (see [[option3-implementation-plan]] file's own todo cross-refs in
  `memory/todo-list.md`).
- 2026-09-09 00:18 — login gap (flagged at end of step 6) closed: AuthController + login/logout
  routes + auth.login Blade view + form-primitive CSS. Also fixed an unrelated host-machine issue
  found in the same session: `npm run dev` EACCES on `::1:5173` — Windows reserves TCP 5094-5193
  (Hyper-V/WSL dynamic port range) on this machine, so `vite.config.js` now pins
  `server.port: 5273` + `strictPort: true`; confirmed binding clean.
- 2026-09-08 23:30 — drafted full implementation plan for Option 3 + chosen image workflow
- 2026-09-08 23:56 — step 1-2 done (Laravel + Filament/commonmark/yaml-front-matter installed);
  step 3 done as pure CSS instead of Sass (dropped both Tailwind and Sass); design system built
  from the two `wdyt/` reference screenshots
- 2026-09-09 08:00 — step 4 done: migrations + models for Course/CourseAccess/Progress, users.role
  + FilamentUser gate, switched .env to MySQL (no sqlite driver on this PHP), migrated clean
- 2026-09-09 00:11 — step 6 done: EnsureCourseAccess middleware, Course route-key-name → slug,
  ContentController show/image, content.show Blade view, named learn/{course}/{slug} routes;
  verified via tinker against seeded test user/course/access; flagged missing login flow as a gap
  blocking real HTTP verification
