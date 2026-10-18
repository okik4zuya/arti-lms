---
name: learner-app-ux
description: What the 2026-10-18 night session built in the learner app and course pages (avatar menu, spinner, password UX, instant mark-complete, prev/next, resume card, content cache in Redis), the empty-dashboard bug and its cause, UX ideas not built, and how the UI was checked
metadata:
  type: project
  created: 2026-10-18 21:54
  updated: 2026-10-18 21:54
---

All uncommitted, tests run per file and green. Context: [[automatic-purchase-plan]] (accounts and set-password links), [[redis-setup]], [[landing-page-concept]] (public pages changed the same night).

## The bug that started it
A buyer opened the course dashboard on the ngrok host and saw "You don't have access to any courses yet" although the order was `fulfilled`, the licence issued and `course_access` present. Cause: the browser was still signed in as another account (one with no access, probably the admin) on that host; the dead set-password link led to "Minta tautan baru" (`/lupa-sandi`), which was behind `guest`, so it bounced silently to that other account's empty dashboard. Data and code were correct (dashboard rendered with the card for the buyer). Fixes: `/lupa-sandi` is no longer `guest`-only; the dead-link page offers "Masuk di sini" or shows the signed-in email with a logout button; a successful set-password signs the browser out and the login page opens with the email prefilled (no automatic sign-in); the dashboard and the avatar menu show which account is signed in. A set-password link already used is dead by design.

## Built
- **Avatar menu** (`components/user-menu.blade.php`, `user-menu.js`): initial in a blue circle, panel with name, email, "Ubah password", "Logout" in red (`--color-danger`); disclosure pattern (aria-expanded), closes on outside click and Esc. Used on the dashboard and the course page.
- **Card hover:** no colour change anywhere; only a link card lifts (shadow, 1 px). **Course card:** slim, one quiet "MULAI/LANJUTKAN BELAJAR + next subchapter title" row, no divider; progress bar fixed (the fill was an inline span, so its width was ignored); phone: full width.
- **Spinner on pressed buttons** (`submit-loading.js`, `loading.css`, shared by the LMS and the public site; skips `target=_blank` and script-handled forms; reset on bfcache restore; reduced motion slows it).
- **Passwords:** eye icon show/hide (`components/password-input.blade.php`, `password-field.js`), live requirement list (`StrongPassword` rule, one message "Password belum memenuhi syarat."), `/app/password` (current, new, confirm; other devices signed out via `Auth::logoutOtherDevices` after saving the new hash).
- **Course page:** "Course Saya" back button with a chevron icon, Sebelumnya/Selanjutnya boxes (skip unpublished), skip link, Indonesian labels, `lang="id"`.
- **Instant "Tandai selesai"** (`progress-instant.js`): the page marks the subchapter and the sidebar item at once and saves with `fetch`; on failure it reverts with a message; the controller answers JSON for `expectsJson()`, the plain form still redirects. (A first `keep-scroll.js` approach was removed.)
- **Content cache** (`app/Support/CourseContent.php`, `config/content.php`): manifest, per-subchapter meta and rendered HTML cached with keys carrying file mtimes (and the site host for the page), TTL `CONTENT_CACHE_DAYS` (7), store `CONTENT_CACHE_STORE` (empty = default store; set `redis` in dev). A cache error falls back to rendering and logs a warning. `phpunit.xml` pins `CONTENT_CACHE_STORE=array` (without it the tests read and wrote the dev Redis). Measured with local Redis: render 230 ms, cache hit about 1 ms after the connection is warm. The free chapter pages (`FreeChapterController`) still use their own default-store cache.
- **Other:** `/beli/selesai/...` reloads every 8 s while the payment is pending; dashboard empty state names the signed-in account and says what to do; labels "Course Saya", "Kemajuan", "Tandai selesai".

## Not built (proposed)
Undo "Tandai selesai"; highlight "Selanjutnya" after marking; prefetch the next subchapter; SPA-style navigation (Turbo/htmx, option C); a "Download ARTi" + licence block on the dashboard (researcher declined it for now, only "Lanjutkan belajar" was wanted).

## How the UI was checked
Headless Chrome works: `"C:/Program Files/Google/Chrome/Application/chrome.exe" --headless=new --disable-gpu --hide-scrollbars --window-size=W,H --virtual-time-budget=6000 --screenshot=<png> <url>` against `http://arti-lms.local/` (Vite dev server running, `public/hot`). Gotchas: the window is never narrower than about 500 px, so phone widths come out clipped on the right; `--force-prefers-reduced-motion` plus a `#hash` URL gave a blank image (also for the old `#harga`), so anchors were checked without it and are best checked in a normal browser; authenticated pages need a real login, so those were checked by rendering through the kernel in a PHP process. The JavaScript (spinner, menu, instant mark, password toggle) has only had test coverage of its markup, not a browser run.

## Change log
- 2026-10-18 21:54 — created at the end of the night session.
