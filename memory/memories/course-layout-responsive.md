---
name: course-layout-responsive
description: Responsive off-canvas sidebar + sections/chapters manifest grouping — implemented from a saved plan, functionally verified without a browser
metadata:
  type: project
  created: 2026-09-09 01:28
  updated: 2026-09-09 07:10
---

Implemented the full plan at `~/.claude/plans/refine-course-layout-kind-pascal.md` (course
reader had zero `@media` queries anywhere in the codebase, and the sidebar rendered a flat list
of raw chapter slugs instead of grouped sections).

**Manifest schema change** (both `resources/content/example-course/manifest.yaml` and
`second-course/manifest.yaml`, clean cutover, no back-compat): flat `chapters:` → nested
`sections: [{title, chapters: [{slug, title}]}]`. `second-course/manifest.yaml`'s stale
`title`/`slug` (previously copy-pasted "Example Course"/"example-course") fixed to "Second
Course"/"second-course" incidentally.

**Two call sites both had to change together** (this was the main regression risk — the plan
flagged it explicitly): `DashboardController` added `flattenChapters()` (flatMap over
`sections[].chapters`) and now calls it instead of reading `manifest['chapters']` directly;
`ContentController` needed no change (passthrough — hands the whole manifest to the view).
`content/show.blade.php`'s sidebar `@foreach` now nests `sections → chapters`, renders a
`.tree-group` header per section, and uses `$chapter['title'] ?? $chapter['slug']` as the link
label (previously always the raw slug).

**Responsive drawer**: `app.css` got one `@media (max-width: 900px)` block (no smaller tier
added speculatively) — `.sidebar` becomes `position: fixed` + `transform: translateX(-100%)`,
toggled via a `.is-open` class; `.sidebar-backdrop` dims/dismisses it; `.sidebar-toggle`
(hamburger button, hidden above 900px) added to the course-reader topbar only —
`dashboard.blade.php` has no sidebar and was left untouched. `app.js` (previously just
`import './bootstrap'`) now has ~20 lines of vanilla open/close/backdrop-click/Escape-key
handling, all three-element-guarded so it's a no-op on pages without the drawer markup.

**No browser automation in this environment — confirmed twice now, stop re-checking.**
No `chromium-cli`, no Playwright, no globally installed browser driver. Re-probing for it
(`where chromium-cli`, `npm ls -g playwright`, etc.) wastes a turn and was flagged by the user
the second time it happened (2026-09-09, mobile-sidebar-JS debugging session) — read this memory
first instead. `arti-lms.local` is the real dev URL (Laragon vhost, see hosts file) and is
`curl`-reachable for HTTP-level checks, but nothing renders JS/CSS visually from this session.

**Verification method — no browser available in this environment.**
Instead: `npm run build` (Vite compiles clean), `php -l` on the
controller, and — most importantly — `php artisan tinker --execute="..."` simulating full HTTP
requests via `app()->handle(Illuminate\Http\Request::create(...))` after `Auth::loginUsingId()`,
asserting on response status + `str_contains($content, 'tree-group')` etc. This caught one real
bug mid-verification: `ContentController@manifest()` caches parsed YAML for 1 hour
(`Cache::remember`), so the first tinker request against `example-course` returned the *old*
flat-schema HTML even though the file was already rewritten — fixed by `php artisan cache:clear`
before re-testing. **Anyone re-verifying this content-manifest area after an edit must clear
cache first**, or will see stale sidebar structure and wrongly conclude the Blade change didn't
work.

Also found mid-verification (not a bug, expected): hitting `/learn/second-course/01-intro` as
the first seeded user 403'd — that user only has `CourseAccess` to `example-course`; user id 5
has access to `second-course` and rendering succeeded once logged in as that user instead.

**Still genuinely unverified**: real-browser visual QA — drawer transform animation, backdrop
click/Escape actually closing it, no-horizontal-scroll at ~375px/~768px. Only the markup/CSS/JS
presence and logic were confirmed, not the rendered visual behavior. If `chromium-cli` or
similar becomes available in a future session, that's the first thing to run against this
change before considering it fully done. Local PHP binary used for all tinker/build calls this
session (project's default `php` alias points at 8.3, but composer requires 8.4.1+):
`C:\laragon6\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe`.

**Real-browser QA has now actually started** (2026-09-09, user opened `arti-lms.local` in their
own browser via the IDE and reported console errors directly — Claude still has no browser
automation of its own in this environment, this is the user driving it). Two bugs surfaced and
were fixed:
- Vite `@vite/client` and `app.js` blocked by CORS from `[::1]:5273` — Windows resolves
  `localhost` to the IPv6 loopback, so Vite's dev server bound there while the page origin was
  `http://arti-lms.local`. Fixed in `vite.config.js`: `server.host: '127.0.0.1'`, `cors: true`,
  explicit `hmr.host: 'localhost'`. Requires restarting `npm run dev` to take effect — not yet
  re-confirmed clean in the browser after the restart.

- `/learn/example-course/images/placeholder.png` 404 (missing the `01-intro` segment) — Markdown
  image refs use relative paths (`./images/placeholder.png`); the browser resolves those against
  the page URL's directory, which drops the chapter slug since the route has no trailing slash.
  Fixed by rewriting relative `<img src>` in `ContentController::resolveImageUrls()` (new method)
  to the real `content.image` route URL, applied to `$html` right after Markdown conversion in
  `show()`. Verified via tinker HTTP simulation (200 on the rewritten URL) — still not
  re-confirmed visually in the browser.
- Drawer animation/backdrop/Escape/no-horizontal-scroll items above are **still open** —
  fixing the console errors is a prerequisite for seeing the layout at all, not the QA itself.

**Fixed topbar + sticky sidebar header + hidden scrollbars** (this session): `.app-shell` changed
from `min-height: 100vh` (grows with page, document-level scroll) to `height: 100vh; overflow:
hidden` — the shell is now pinned to the viewport instead. Because the flex row's default
`align-items: stretch` already gives `.sidebar`/`.main` the container's full height, no explicit
height was needed on either. `.topbar` got `flex-shrink: 0` (stays fixed at the top of `.main`,
both the light dashboard variant and `.topbar--dark` reader variant); `.sidebar__header` got
`flex-shrink: 0` (wordmark + course name pinned at the top of the sidebar); `.content` and
`.sidebar__nav` each got `min-height: 0; overflow-y: auto` so they scroll independently below/
under their fixed siblings instead of growing the page. This applies to `dashboard.blade.php` too
since it shares the same `.app-shell`/`.main`/`.topbar`/`.content` classes (no sidebar there, so
only the topbar-fixed part is relevant). Scrollbars on both `.content` and `.sidebar__nav` are
hidden (`scrollbar-width: none` + `::-webkit-scrollbar { display: none }`) — scrolling still
works, just no visible scrollbar track. Build-verified only (`npm run build` clean) — same
no-browser-automation limitation as the rest of this memory; folds into the same open real-browser
visual QA item (also now covering: does content/sidebar actually scroll independently, does the
topbar stay visually fixed).

## Change log
- 2026-09-09 01:28 — created after implementing + functionally verifying the plan
- 2026-09-09 07:04 — real-browser QA started by the user; fixed two console errors found (Vite
  IPv6/CORS dev-server binding, relative Markdown image paths resolving to the wrong URL);
  drawer-specific visual QA still open
- 2026-09-09 07:10 — fixed-viewport shell: topbar pinned top, sidebar header pinned top, content +
  sidebar nav independently scrollable with hidden scrollbars; build-verified only
