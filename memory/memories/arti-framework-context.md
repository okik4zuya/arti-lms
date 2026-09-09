---
name: arti-framework-context
description: What the ARTi Framework/ebook is, and how it relates to this LMS project — background needed to understand what content will eventually be loaded
metadata:
  type: project
  created: 2026-09-08 23:22
  updated: 2026-09-09 13:40
---

**ARTi Workflow / ARTi Framework** is a research-writing methodology and Claude-skill package for
academic researchers, built around three meanings of the name: **ART + i** ("state of the art" +
the researcher's innovation on top of it), **Amati, Replikasi, Tambah inovasi** ("Observe,
Replicate, Add innovation" — the literal operating loop), and Indonesian *arti* ("meaning"). The
package's flagship product is an Indonesian-language **ebook** teaching this loop, alongside Claude
skills (`ARTi-idea`, `ARTi-writing`) that operationalize it.

**Outline (finalized 2026-09-09):** 5 sections / 18 chapters — Pendahuluan (3: AI's
halusinasi/memori problem, Claude & Claude Code, Why ARTi Framework), Amati (3), Replikasi (2),
Tambah Inovasi (7, folds in what was drafted as a separate "Publication Loop" part), BONUS (3:
Toolkit Quick Reference, Panduan Instalasi, Prompt Guide). Full chapter table lives in this repo's
own `resources/inbox/ebook-outline-v2.md` — draft source material (`abstraksi-konten-dan-outline.md`,
narasi files, `task.md`) also lives in `resources/inbox/`, i.e. **ebook drafting now happens
in this repo**, not a separate project as originally assumed. Structural skeleton only so far
(chapter titles final, no subchapters drafted) — see [[subchapter-hierarchy]] for how the LMS
represents this.

**Relationship to arti-lms:** this repo (`arti-lms`) is the **delivery platform** — an LMS — that
the finished ebook content gets loaded into once written. The LMS build can proceed independently
of ebook-writing progress; content migration (pushing drafted Markdown chapters into
`resources/content/arti-framework/`, per [[tech-stack-options]]'s chosen mechanism) happens
per-chapter as each is actually written.

**Business model context:** public-course scale (hundreds–thousands of learners), sold via
Facebook ads → landing page → WhatsApp → manual payment, MVP uses admin-manual learner account
creation (no self-registration); an automated Scalev checkout integration is a documented but
deferred later phase.

## Change log
- 2026-09-08 — captured ARTi Framework background context for arti-lms
- 2026-09-09 13:40 — corrected: ebook drafting happens in this repo's own `resources/inbox/`, not a
  separate project; recorded the finalized 5-section/18-chapter outline and linked
  [[subchapter-hierarchy]]
