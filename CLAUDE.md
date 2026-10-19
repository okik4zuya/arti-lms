<!-- arti-claude-template: v1 (dev-project variant) -->
# arti-lms — Project Instructions

@~/.arti/CAPABILITIES.md

**Never invoke a bare `python`/`python3`/`py`** — nothing on this machine puts it on `PATH`. Always
call the vendored interpreter by full path: `"~/.arti/python/python.exe" <script>.py ...`
(Windows); a top-level `~/.arti/python.cmd` wrapper also forwards to the same binary.

ARTi general/dev project (category: Dev, registered in `~/.arti/memory/launcher-projects.json`).
This project's `memory/` is the single source of truth — never read or write the default
`~/.claude/projects/.../memory/` for it.

Read every session: `memory/MEMORY.md`, `memory/todo-list.md`, `memory/status.md`,
`~/.arti/memory/working-preferences.md`, `wdyt/index.md`.

Unlike an ARTi paper project, there is no `idea/`/`writing/`/`literature/`/`data/`/`figures/`/
`submission/` scaffold here — this is a code repo, not a manuscript.

## `wdyt/` — the one folder meant for direct editing

Everything else is Claude-managed. `wdyt/index.md` columns:
`File · Tipe · Status · One-line hook · Dipakai di · Date added`.

- **Tipe = Ide** (default) — a note *about* something to do/decide. Triage renames it
  `YYMMDD_STATUS_<slug>.md` (date = triaged, not written), status one of **OK** (ingested
  somewhere) / **SKIP** (reviewed, unused) / **PARKED** (good, not for now → `~/.arti/wdyt/` if
  it's about the ARTi framework itself, otherwise just keep it here for later).
- **Tipe = Narasi** — verbatim prose that *is* the content itself. Never summarize, rewrite, or
  condense it; triage fills only `One-line hook` and `Dipakai di`. Never archived, exempt from
  the cap.
- **Caps:** untriaged files (no prefix) uncapped, never archived. Triaged files capped at 10
  outside `wdyt/archive/`; past that, move the oldest in, **delete its row** (don't relabel the
  path in place), and fold it into one collapsed line under `## Archived` at the bottom of
  `index.md`, overwritten on each later batch.
- **Findability:** if asked about `wdyt/` content absent from the live table, grep
  `wdyt/archive/*.md` — only index rows are deleted, never files.

## Memory rules

- `memory/` holds exactly three files flat (`MEMORY.md`, `todo-list.md`, `status.md`). Every
  topic file goes in `memory/memories/`, created only when the first one is actually needed, read
  on demand.
- `MEMORY.md` = index, pointers only, one line each — never content.
- `todo-list.md` = contiguous `- [ ]`/`- [x]` items only, one line each, no narrative continuation
  — link `[[slug]]` for context instead of inlining it. No `## Change log` section. A phase's
  checked items get deleted once it's closed and captured in a topic file — this file tracks
  outstanding work, not history.
- `status.md` = narrative, but only as a pointer: one "Current state" block overwritten in place
  each session (never stacked), 2-4 sentences naming what changed and a `[[topic-file]]` link for
  every detail — the topic file carries the report, not this block. No Archive section and no
  `## Change log` section here either; the linked topic file's own change log and git history
  already cover it.
- One file per topic — update it rather than creating a near-duplicate; read it before advising
  on that topic. Link with `[[slug]]`, don't restate across files.
- **Timestamps:** run a date command, never guess. Set `created` on creation, `updated` on every
  write. Every memory file except `status.md`/`todo-list.md` adds one `## Change log` line on
  write — no history file, no change log of the index.

Frontmatter for every memory file:

```
---
name: kebab-case-slug
description: one-line summary, specific enough to judge relevance later
metadata:
  type: project | user | feedback | reference
  created: yyyy-mm-dd HH:mm
  updated: yyyy-mm-dd HH:mm
---
```

...content, then `## Change log` at the bottom.

## Session start

Surface, unasked: any open question a memory file recorded ("ask whether X"), and any untriaged
`wdyt/index.md` rows.

## Session end

Fired by *any* phrasing meaning "we're done" — "update memory", "update status", "end session",
and the like:

1. Update the relevant memory file(s).
2. **Only if the session moved outstanding work or produced a state change worth recording:**
   overwrite `status.md`'s "Current state" pointer and/or check off / add `todo-list.md` items. A
   session that was pure discussion, research, or Q&A with no checklist or state change skips both
   files entirely — there is nothing to overwrite.
3. Write any correction on *how* to work straight into `~/.arti/memory/working-preferences.md`, the
   same turn it's given — never deferred to session end.

There is no `~/.arti/memory/progress-index.md` row for this project — that dashboard tracks
paper projects (Category: Paper), not Dev-category ones.

<!-- arti: local additions below — preserved on regeneration -->

## YouTube research (yt-keyword-audit)
- Audience: language `id`, region `ID`. Pass `--lang id --region ID` to `videos` and `find-channels`, `--lang id` to `suggest`.
- Run: `"~/.arti/python/python.exe" -I` (never a bare `python`).
- Record: keyword findings in `memory/memories/landing-page-keywords.md`; competitor audits in `G:\My Drive\YT Dosen Berarti\memory\memories\competitor-research.md`. Update these files, do not create new ones.
