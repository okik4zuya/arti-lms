---
name: branding-login
description: Login page redesign, home route redirect, real logo/favicon, and design-system blue sourced from the ARTi logo
metadata:
  type: project
  created: 2026-09-09 06:39
  updated: 2026-09-09 06:39
---

Login page (`resources/views/auth/login.blade.php`) has no header/topbar — it's a standalone
`.auth-page` shell that centers the card vertically+horizontally, with an `.auth-brand` block
(logo + "ARTi LMS" name) above the card. CSS lives under `/* ---- Auth pages ---- */` in
`resources/css/app.css`.

Real brand assets are copied in (not generated) from `~/.arti/logo/export/`:
- `public/favicon.ico` ← `~/.arti/logo/export/icon/favicon.ico` (was a 0-byte placeholder)
- `public/images/logo.png` ← `~/.arti/logo/export/icon/icon-192.png`
- A `wordmark/` variant also exists there (icon + "ARTi" text baked into one image) but isn't
  used — the login page composes its own icon + "ARTi LMS" text instead, since the wordmark says
  "ARTi" not "ARTi LMS".

`routes/web.php` root route (`/`) redirects rather than rendering `welcome.blade.php`: guests →
`login`, authenticated users → `dashboard`. `welcome.blade.php` is now dead/unused.

Design-system primary blue was re-sourced from the logo's dot color, not picked by eye: sampled
via PIL from `~/.arti/logo/export/icon/icon-512.png` → `#2563eb` (this is Tailwind's blue-600).
Updated the three `--color-primary*` tokens in `resources/css/app.css` (`:root`, near the top):
`--color-primary: #2563eb`, `--color-primary-dark: #1d4ed8` (blue-700), `--color-primary-light:
#eff6ff` (blue-50). These three tokens are the only place blue is defined — everything else
(buttons, links, focus rings, active nav/tree states) references them, so no other files needed
touching.

**Why:** user wants the app's visual identity to trace back to the actual logo file rather than
an arbitrary palette choice, and wants `/login` to be the functional front door of the app.

**How to apply:** any future branding/color work should keep sourcing from
`~/.arti/logo/export/` rather than re-guessing colors or fetching a logo from elsewhere. If the
logo assets are regenerated, re-sample the dot color and re-copy the icon/favicon files rather
than assuming the old hex/paths still match. Rebuild Vite assets (`npm run build`) after any
`app.css` edit — the login/dashboard pages are served from the compiled bundle, not live CSS.

## Change log
- 2026-09-09 06:39 — created after login redesign + home redirect + logo-sourced blue palette work
