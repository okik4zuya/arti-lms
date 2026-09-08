<!-- arti-claude-template: v1 (dev-project variant) -->
# arti-lms — Project Instructions

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
- `todo-list.md` = contiguous `- [ ]`/`- [x]` items only, no narrative.
- `status.md` = narrative: one "Current state" block overwritten in place each session (never
  stacked), archive below.
- One file per topic — update it rather than creating a near-duplicate; read it before advising
  on that topic. Link with `[[slug]]`, don't restate across files.
- **Timestamps:** run a date command, never guess. Set `created` on creation, `updated` on every
  write, add one `## Change log` line. That is the whole ritual — no history file, no change log
  of the index.

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
2. Overwrite `status.md`'s "Current state" block.
3. Check off / add `todo-list.md` items.
4. Promote any correction on *how* to work to `~/.arti/memory/working-preferences.md`.

There is no `~/.arti/memory/progress-index.md` row for this project — that dashboard tracks
paper projects (Category: Paper), not Dev-category ones.

<!-- arti: local additions below — preserved on regeneration -->
