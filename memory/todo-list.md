---
name: todo-list
description: Checklist of open tasks for arti-lms — no narrative, checkboxes only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-09-09 01:28
---

- [x] Brainstorm and redesign the tech stack next session (see [[tech-stack-options]] for the two prior drafts)
- [x] Get the design-system screenshot from the user (blocks phase 2 in either stack draft)
- [ ] Decide content-repo layout and deploy mechanism (only relevant if Docsify option is kept)
- [x] Step 1-2: Laravel + Filament/commonmark/yaml-front-matter scaffolded
- [x] Step 3: pure CSS design system built (Tailwind + Sass both dropped)
- [x] Step 4: database schema (Course, CourseAccess, Progress migrations)
- [x] Step 5: content directory + first course
- [x] Step 6: content controller + routes
- [x] Close login gap (AuthController + login/logout routes + view) — flagged at end of step 6
- [x] Fix `npm run dev` EACCES (Vite port pinned to 5273)
- [x] HTTP-verify login → content → image → access-control via `artisan serve` (curl walkthrough)
- [ ] Browser-verify same flow through the `arti-lms.local` vhost now that its DocumentRoot is fixed
- [x] Add a logout control to content/show.blade.php's topbar (route/controller exist, no UI link)
- [x] Build learner dashboard (DashboardController + /dashboard route + view); login now redirects there
- [x] Step 7: progress tracking (mark-complete + sidebar check state + dashboard completion %)

### Step 8 — Filament admin

- [x] `php artisan make:filament-resource Course` (manage slug/title/published_at only)
- [x] "Create Learner" custom Filament action: creates a User (admin-set temp password, role=learner) + a CourseAccess grant in one step
- [x] CourseAccess relation manager on the Course resource (grant/revoke access inline)
- [x] Verify `role === 'admin'` FilamentUser gate actually blocks learner accounts from /jamrud (panel path, not /admin)

### Step 9 — Cloudflare R2 (video only)
- [ ] `composer require league/flysystem-aws-s3-v3`
- [ ] Configure `r2` disk in `config/filesystems.php` (endpoint/credentials via `.env`, not committed)
- [ ] Confirm video workflow stays manual-paste-URL-in-Markdown — no upload UI, not wired into ContentController@image

### Testing

- [ ] Replace stub tests (`tests/Feature/ExampleTest.php`, `tests/Unit/ExampleTest.php`) with real coverage: login, access control, image path-traversal guard, dashboard scoping
- [x] Filament panel gate coverage (`tests/Feature/FilamentPanelAccessTest.php`: admin 200, learner 403)

- [ ] `php artisan test` clean run

### Security / production hardening

- [ ] Flip `.env` for prod: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`
- [ ] Rate-limit the login route (`throttle` middleware) — currently unthrottled
- [ ] Confirm session/cookie config is prod-appropriate (`SESSION_SECURE_COOKIE`, `SESSION_DRIVER`)
- [ ] Re-confirm the vhost dotfile-exposure fix holds (add to pre-deploy checklist)
- [ ] Styled 403/404 error pages matching the app shell

### Deploy

- [ ] Document native-VPS deploy steps (composer install --no-dev, npm run build, migrate --force, config/route/view cache, storage:link)
- [ ] Provision R2 credentials + other secrets on the VPS `.env`
- [ ] Confirm MySQL/MariaDB set up on target VPS (no pdo_sqlite)
- [ ] Decide real learner-facing domain/vhost for production

### Content polish

- [ ] Replace `welcome.blade.php` placeholder copy with real landing content (or confirm bare page is fine)
- [ ] Add real course content beyond the single example-course/01-intro placeholder
- [x] Sanity-check manifest.yaml schema + image workflow scale to more than one course/chapter (nested sections: schema now covers both existing manifests)

### Course layout — responsive + grouped sidebar

- [x] Nest manifest `chapters:` under `sections:`, add per-chapter `title` (both manifests); fix second-course's stale copy-pasted title/slug
- [x] `DashboardController::flattenChapters()` to match new manifest shape
- [x] `content/show.blade.php` grouped sidebar (`.tree-group` headers) + hamburger toggle + backdrop markup
- [x] `app.css` `.tree-group` styling + `@media (max-width: 900px)` off-canvas drawer block
- [x] `app.js` toggle/backdrop-click/Escape handling
- [ ] Real-browser visual QA (drawer animation, backdrop/Escape close, no horizontal scroll at ~375px/~768px) — no chromium-cli/Playwright available this session, only functionally verified via `artisan tinker` simulated requests (see [[course-layout-responsive]])

## Change log
- 2026-09-08 — scaffolded, empty
- 2026-09-08 23:20 — added redesign-brainstorm and open-item tasks
- 2026-09-08 23:56 — checked off stack decision + design-system screenshot; Laravel scaffolding and
  pure-CSS design system done; expanded remaining items to match [[option3-implementation-plan]]
- 2026-09-09 — step 4 done: Course/CourseAccess/Progress migrations + models, users.role column,
  switched .env to MySQL (arti_lms db), migrated clean
- 2026-09-09 00:18 — step 5-6 checked off; closed login gap + Vite EACCES fix; added browser-verify item
- 2026-09-09 00:32 — checked off curl-based end-to-end HTTP verification; split remaining work into
  vhost re-verify + logout-UI-missing items (found during verification)
- 2026-09-09 09:15 — closed §1 of option3-implementation-currently-finish-wobbly-swan plan: logout
  button + learner dashboard, curl-verified end-to-end via artisan serve
- 2026-09-09 00:53 — closed §2 (step 7) of the same plan: progress tracking (ProgressController +
  route + mark-complete UI + sidebar check state), curl-verified end-to-end via artisan serve
- 2026-09-09 — closed step 7's deferred dashboard-% item: DashboardController now computes
  completed/total chapters per course from manifest.yaml, dashboard card shows the existing
  `.progress` bar component, curl-verified (100% for the one already-completed chapter)
- 2026-09-09 01:01 — expanded to the full comprehensive plan (§3-8 of
  `~/.claude/plans/option3-implementation-currently-finish-wobbly-swan.md`): replaced the flat
  Step 8/9 lines with the plan's actual sub-items and added the Testing, Security/production
  hardening, Deploy, and Content polish sections it also covers
- 2026-09-09 01:05 — step 8 done: CourseResource (slug/title/published_at), AccessRelationManager on
  Course with grant-existing-learner + create-learner-and-grant actions, Filament panel gate
  test-verified (admin 200 / learner 403 at /jamrud, not /admin — panel id is "jamrud")
- 2026-09-09 01:28 — implemented the course-layout-responsive plan (grouped sidebar + off-canvas
  mobile drawer); added dedicated section above; left the browser-visual-QA line open
