---
name: feedback-check-memory-before-env-probes
description: Check project memory for known environment constraints before re-running discovery commands (e.g. browser tooling availability)
metadata:
  type: feedback
  created: 2026-09-09 12:00
  updated: 2026-09-09 12:00
---

Don't re-run environment-discovery commands (`where <tool>`, `npm ls -g <pkg>`, etc.) to answer
a question this project's memory already answered. User rejected a `where chromium-cli` /
`npm ls -g playwright` tool call mid-debugging session because [[course-layout-responsive]]
already recorded "no browser automation available in this environment" from an earlier session.

**Why:** re-discovering a known-false environment fact burns a turn and reads as not having done
the homework — the memory system exists specifically so this doesn't need re-deriving each
session.

**How to apply:** before reaching for Bash/PowerShell to probe "is X installed / available /
running", grep `memory/` (and `memory/memories/*.md`) for the tool/capability name first. If a
memory already answers it, use that answer (and note it may be stale, verify lightly if the
consequence of being wrong is high) rather than re-probing from scratch.

## Change log
- 2026-09-09 12:00 — created after user flagged a redundant chromium-cli/playwright probe
