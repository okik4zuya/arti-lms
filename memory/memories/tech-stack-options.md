---
name: tech-stack-options
description: Two candidate tech-stack designs for the ARTi LMS (Filament-authored vs Docsify-based content delivery) — pending a redesign brainstorm
metadata:
  type: project
  created: 2026-09-08 23:20
  updated: 2026-09-08 23:56
---

Two stack designs have been drafted in `~/.claude/plans/i-want-to-start-wise-teapot.md` (the plan
file gets overwritten on revision, so both versions are captured here before the next redesign).
Common to both: Laravel + Filament v3 (admin), MySQL/MariaDB, native VPS install (no Docker),
Cloudflare R2 for media, admin-manual learner account creation via Filament "Create Learner"
(Option B access flow; Scalev webhook automation deferred as Option A). Frontend styling was
originally scoped as Sass but revised 2026-09-08 23:56 to **pure CSS** (no preprocessor, no
Tailwind) — see [[option3-implementation-plan]] step 3.

## Option 1 — Filament-authored content (original plan)
- Content (markdown text/image/video) authored *inside* Filament via a markdown textarea + media
  upload field, stored in a `chapters` DB table (`course_id`, `slug`, `title`, `order`, `body`,
  `published_at`).
- Filament handles both content CRUD and learner/access management.
- Learner-facing chapter pages are plain Blade + Sass, rendering `chapters.body` server-side.
- jQuery embedded directly in Blade views for interactivity (no Livewire/Alpine).
- `progress` table: `user_id`, `chapter_id`, `completed_at`.
- Simpler to build (one CMS, one auth boundary, no iframe/static-file-gating problem) but ties
  content authoring to logging into `/admin` and typing markdown into a textarea.

## Option 2 — Docsify-based content delivery (revised plan)
- Content authored as plain `.md` files in a git repo, edited in code, pushed to the server — no
  `chapters` table, no Filament markdown editor. Filament's job shrinks to learner
  account/course-access management only.
- One Docsify instance (`index.html` + `_sidebar.md` + `.md` files) per course, stored on a
  **non-public disk** (e.g. `storage/content/{course}/`, outside `public/`).
- A Laravel route `GET /learn/{course}/{path?}` behind `auth` middleware checks the learner's
  `product_course` grant, then streams files from that non-public disk — this is the part that
  actually gates access, since Docsify itself fetches files via client-side AJAX.
- The gated Docsify instance is shown to the learner inside a same-origin `<iframe>` (isolates
  Docsify's own router/JS from the main app's jQuery/Sass).
- Progress tracking: a small vanilla-JS Docsify plugin (`hook.doneEach`) reads the current chapter
  slug from the hash route and `fetch()`s `POST /progress` using the shared session cookie.
  `progress` table becomes `user_id`, `course_id`, `chapter_slug` (string, no `chapters` FK),
  `completed_at`.
- Auth changed from "Laravel built-in auth" to explicitly **hand-rolled `Auth` facade, no
  Breeze/Fortify/Jetstream/Sanctum** (no self-registration/password-reset flow to scaffold).
- Lets content be authored/edited entirely in a code editor + git, decoupled from logging into
  `/admin`, at the cost of building custom secure file-streaming + an iframe/progress bridge —
  flagged as the highest-risk, most custom part of the whole stack (see [[stack-tradeoff-assessment]]
  if that memory exists, else see plan discussion in this session).

## Option 3 — Git-backed Markdown, rendered server-side (flat-file CMS)

- Content authored as `.md` files in a repo (git-editable, like Option 2), but Laravel parses and
  renders them server-side into Blade instead of shipping to a client-side router like Docsify.
- `resources/content/{course}/{slug}.md` with YAML front matter; `spatie/yaml-front-matter` for
  parsing, `league/commonmark` for HTML conversion. Route `GET /learn/{course}/{slug}` behind the
  same `auth` middleware + course-grant check as any other page — no iframe, no custom secure
  file-streaming layer, since the framework's normal route gating already does the job.
- Sidebar/chapter list from a directory scan or small `manifest.yaml` per course, cached (content
  only changes on deploy/git-pull).
- Progress tracking is a plain `POST /progress` from the same-session Blade page — no cross-frame
  `fetch()` bridge, no separate Docsify JS plugin.
- Filament's role matches Option 2 (learner/course-access management only) without owning file
  streaming/gating.
- Proven pattern: same approach Statamic/Jigsaw/Hyde use (parse Markdown, render through the
  framework's own view layer). Collapses Option 2's two biggest risks (custom secure streaming,
  isolated client-side router in an iframe) while keeping git-authorable content.
- Tradeoff: no in-browser editing via Filament (same as Option 2, unlike Option 1's textarea), and
  loses Filament's media-library UX for images (files committed alongside Markdown, or R2 URLs).

### Image workflow (chosen)
Images live next to the Markdown in the same git repo, referenced with relative paths — no
upload step during authoring:

```text
resources/content/{course}/{slug}/
  index.md
  images/
    diagram-1.png
```

`![Reaction pathway](./images/diagram-1.png)` previews correctly in any editor/GitHub before it
reaches the server. The same auth-gated content route extends to serve
`/learn/{course}/{slug}/images/{file}` from that folder, so images inherit identical access
control to the chapter text — no extra gating logic. Git gives free version history on images too.
Escape hatch if repo size becomes a problem: `git-lfs` on the `images/**` path (workflow doesn't
change) or, for genuinely heavy media (video, large PDFs) only, fall back to a manual R2-URL
paste — R2 stays reserved for video, not per-chapter images.

## Status

**Decided 2026-09-08: Option 3 + the relative-path git image workflow above.** Implementation plan
captured in [[option3-implementation-plan]].

## Change log
- 2026-09-08 23:20 — captured both stack options before the next redesign session
- 2026-09-08 — added Option 3 (git-backed Markdown rendered server-side, flat-file CMS pattern)
- 2026-09-08 23:30 — decided on Option 3; added chosen image workflow; implementation plan drafted
- 2026-09-08 23:56 — revised styling choice from Sass to pure CSS (no preprocessor/framework)
