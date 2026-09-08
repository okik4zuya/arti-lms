---
name: status
description: Living current-state narrative for arti-lms
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-09 01:28
---

## Current state

Steps 1-8 of [[option3-implementation-plan]] are done, plus §1-2 of the follow-on
`option3-implementation-currently-finish-wobbly-swan` plan (logout UI, learner dashboard, progress
tracking), plus the separate [[course-layout-responsive]] plan (grouped sidebar sections +
off-canvas mobile drawer, this session). Not yet browser-verified through the real
`arti-lms.local` vhost — still outstanding from three sessions ago; all verification so far is
`artisan serve`/`artisan tinker` + curl/PowerShell/PHPUnit.

**Course layout (this session)**: implemented `~/.claude/plans/refine-course-layout-kind-pascal.md`
in full — manifest schema is now `sections: [{title, chapters: [{slug, title}]}]` (both
manifests; `second-course`'s stale copy-pasted title/slug also fixed), `DashboardController`
gained `flattenChapters()`, `content/show.blade.php` renders grouped `.tree-group` sidebar
sections + a hamburger toggle + backdrop, `app.css` got a `@media (max-width: 900px)` off-canvas
drawer block, `app.js` got guarded toggle/backdrop/Escape JS. Verified functionally via
`artisan tinker` simulated HTTP requests (`app()->handle(Request::create(...))` after
`Auth::loginUsingId()`) rather than a real browser — no `chromium-cli`/Playwright available in
this environment. Full detail, including the manifest-cache-must-be-cleared gotcha hit during
verification, in [[course-layout-responsive]]. **Real-browser visual QA (drawer animation,
backdrop/Escape close, no horizontal scroll at ~375/~768px) is still open** — do this first if a
browser-driving tool becomes available.

**Step 8 (Filament admin, this session)**: `CourseResource` (`php artisan make:filament-resource
Course --generate`, then added `unique` + helper text to the slug field) manages slug/title/
published_at. `CourseResource\RelationManagers\AccessRelationManager` (relationship `access`) lists
each course's `course_access` rows (learner name/email/granted_at) with two header actions: "Grant
access" (select an existing learner, `granted_at` set on submit) and "Create learner" (name/email/
generated temp password → creates a `role=learner` User + a `CourseAccess` row in one
`->action()` closure, shows the temp password in a persistent success notification). Table row
action relabeled "Revoke" (still `DeleteAction`). The admin panel's id/path is **`jamrud`**, not
`admin` (`JamrudPanelProvider`, pre-existing) — the todo item referencing `/admin` was stale.
`User::canAccessPanel()` gate (`role === 'admin'`) verified two ways: `tinker` (learner→false,
admin→true) and a new `tests/Feature/FilamentPanelAccessTest.php` (admin `GET /jamrud` → 200,
learner → 403), both passing under `php artisan test`.

**Step 7 (progress tracking, this session)**: new `App\Http\Controllers\ProgressController@store`
upserts `Progress` on `(user_id, course_id, chapter_slug)` (unique constraint already existed from
step 4) and sets `completed_at`. Route `POST learn/{course}/{slug}/progress` (`progress.store`)
added inside the existing `auth`+`course.access` group in `routes/web.php`.
`ContentController@show` now loads the user's completed chapter slugs for the course and passes
`completedSlugs`/`isCompleted` to the view. `resources/views/content/show.blade.php` renders a
"Mark as complete" form (swaps to a static "Completed" indicator once done) below the chapter body,
and the sidebar `tree-item` shows a `.tree-item__check--done` checkmark for completed chapters.
Minor CSS added to `resources/css/app.css` (`.chapter-complete`, centered `.tree-item__check`
glyph). Curl/PowerShell-verified end-to-end: chapter page shows the button pre-completion, POST to
`/progress` redirects back showing "Completed" + sidebar check.

Dashboard completion % (this session, closing step 7's earlier deferral): `DashboardController`
now derives each course's chapter list from its `manifest.yaml`, counts the user's completed
`Progress` rows restricted to slugs in that manifest, and sets `totalChapters`/`completedChapters`/
`progressPercent` on the course before handing it to the view. `dashboard.blade.php` renders the
existing (previously unused) `.progress`/`.progress__track`/`.progress__fill` design-system
component inside each course card. Curl-verified: dashboard shows 100% for the example course
(its one chapter was already marked complete from the step-7 test).

**Learner dashboard + logout UI (prior session, §1 of the same plan)**: `content/show.blade.php`'s
topbar has a `topbar--dark` logout form (POST /logout); `DashboardController@index` (queries
`courseAccess()->with('course')`, resolves each course's first chapter slug from its
`manifest.yaml`), `GET /dashboard` route (named, `auth`-gated), and
`resources/views/dashboard.blade.php` (card-grid, reuses `.card`/`.card-grid`).
`AuthController@store` redirects to `route('dashboard')` instead of `/`.

Laravel scaffolded and building. Composer has Filament v3, `league/commonmark`,
`spatie/yaml-front-matter` installed. Frontend: Tailwind AND Sass both dropped — styling is
**pure CSS**, a token-based design system in `resources/css/app.css` (`.app-shell`, `.sidebar`,
`.topbar`/`.topbar--dark`, `.tree-item`, `.card`/`.card-grid`, `.badge`, `.btn`, `.progress`, plus
`.form-field`/`.form-label`/`.form-input`/`.form-error`/`.auth-content`/`.auth-card`), derived from
the two `wdyt/screenshot-*.png` references.

Tech stack is **decided: Option 3** (git-backed Markdown, parsed and rendered server-side into
Blade — no Docsify/iframe, no Filament DB-authored content) plus a relative-path git image workflow
(images committed next to each chapter's Markdown, served through the same auth-gated route; R2
reserved for video only). Full rationale in [[tech-stack-options]].

Database (step 4): `courses`, `course_access`, `progress` migrated on **MySQL** (`.env` switched
from the Laravel-11-default sqlite — no pdo_sqlite driver on this PHP install). `users` gained
`role` (default `learner`); `User` implements `FilamentUser::canAccessPanel()` gated on
`role === 'admin'`.

Content (step 5): `resources/content/example-course/manifest.yaml` (chapter order = list order) +
`01-intro/index.md` + a real placeholder PNG — still the only course/chapter that exists.

Content controller + routes (step 6): `EnsureCourseAccess` middleware (`course.access` alias),
`Course::getRouteKeyName() → slug`, `ContentController@show`/`@image`,
`learn/{course}/{slug}[/images/{file}]` routes, plus `AuthController` (create/store/destroy) +
`login`/`logout` routes + `resources/views/auth/login.blade.php`.

Known still-open items: the Laragon vhost `DocumentRoot` fix (user-applied) has never been
browser-verified end to end; `npm run dev` needed `vite.config.js` pinned to `server.port: 5273`
(Windows reserves TCP 5094-5193 on this machine).

**Incident (this session, caught by user report "can't login to /jamrud")**: the first
`FilamentPanelAccessTest` run used `RefreshDatabase` with no separate testing DB configured —
`phpunit.xml` had `DB_CONNECTION`/`DB_DATABASE` commented out (sqlite was ruled out earlier,
no pdo_sqlite driver), so tests ran `migrate:fresh` against the real dev `arti_lms` MySQL database
and wiped all users/courses/course_access/progress rows. Fixed by creating a separate
`arti_lms_testing` MySQL database and pointing `phpunit.xml`'s `DB_CONNECTION`/`DB_DATABASE` at it
(both env lines were previously commented out — now `mysql` / `arti_lms_testing`). Re-ran
`php artisan test`: passes, and dev DB user count stayed unchanged across the run, confirming
isolation. Restored dev data by hand: `test@example.com`/`password` (learner), `admin@example.com`/
`password` (admin), `example-course` Course row + CourseAccess grant for the learner — filesystem
content (`resources/content/example-course/`) was never touched, only DB rows were lost. The
`Progress` row for the previously-completed 01-intro chapter was NOT restored (cosmetic, re-click
"mark complete" to redo).

**Next session**: real-browser visual QA of the course-layout-responsive drawer (see above), or
Step 9 (Cloudflare R2 for video), or the browser-verify-vhost item still open since three
sessions ago. Test credentials: `test@example.com` / `password` (learner), `admin@example.com` /
`password` (admin); user id 5 has `CourseAccess` to `second-course` specifically (the seeded
`test@example.com` user only has `example-course` access).

## Archive

(none yet)

## Change log
- 2026-09-08 — scaffolded, empty project
- 2026-09-08 23:20 — drafted two stack options, deferred decision to a redesign brainstorm next session
- 2026-09-08 23:30 — decided Option 3 + image workflow; wrote full implementation plan, ready to build next session
- 2026-09-08 23:56 — Laravel scaffolded (steps 1-2); dropped Sass plan, built pure-CSS design system from wdyt screenshots (step 3)
- 2026-09-09 08:00 — step 4 done: DB schema migrated on MySQL (switched off default sqlite, no driver), models wired, users.role + FilamentUser panel gate added
- 2026-09-09 00:07 — step 5 done: content dir scaffold, manifest.yaml schema decided, example-course/01-intro seeded with index.md + placeholder image
- 2026-09-09 00:18 — closed step-6 login gap (AuthController + login/logout routes + view + form CSS); fixed unrelated Vite EACCES (port pinned to 5273, host has 5094-5193 excluded)
- 2026-09-09 00:32 — end-to-end HTTP verification via artisan serve + curl (all core paths pass); found + user-fixed Laragon vhost DocumentRoot misconfig; found still-open logout-UI gap in content topbar
- 2026-09-09 00:53 — step 7 done: ProgressController + progress.store route + mark-complete UI + sidebar check state, curl/PowerShell-verified end-to-end; dashboard completion % explicitly deferred
- 2026-09-09 — closed the dashboard-% deferral: DashboardController computes per-course completion from manifest.yaml + Progress rows, dashboard card renders the existing .progress bar component, curl-verified (100% shown for the already-completed example chapter)
- 2026-09-09 01:05 — step 8 done: CourseResource + AccessRelationManager (grant-existing-learner and create-learner-and-grant actions), Filament panel gate verified via tinker + new FilamentPanelAccessTest (admin/learner at /jamrud)
- 2026-09-09 01:20 — fixed RefreshDatabase wiping the real dev MySQL DB (no testing DB was configured in phpunit.xml); added arti_lms_testing DB + phpunit.xml env; restored dev users/course/access by hand
- 2026-09-09 01:28 — implemented course-layout-responsive plan (grouped sidebar + off-canvas mobile drawer), functionally verified via artisan tinker (no browser tool available); real-browser visual QA left open
