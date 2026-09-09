---
name: ebook-outline-v2
description: Refined, drafting-ready ebook outline — supersedes arti-proposal-full.md §04 as the reference for Phase 2 drafting; reconciled tool names, source material mapped per chapter, branding decision recorded
metadata:
  type: project
  tier: T1
  created: 2026-09-09 08:50
  updated: 2026-09-09 08:50
---

**Supersedes [[arti-proposal-full]] §04 as the drafting reference.** The proposal file itself is
untouched — it stays as the historical proposal record; this file carries the corrections and
source-material mapping that record needs before Phase 2 drafting starts. Written per
`~/.claude/plans/i-want-to-start-dreamy-tome.md` (refine-outline-and-confirm-context plan,
2026-09-09) — no chapter prose written, no `arti-lms` changes made.

## Branding decision (resolved this session)

**ARTi Framework** — the researcher confirmed the 2026-09-05 rename proposal
(`inbox/260905_OK_loop-closure-and-distribution-checklist.md`) when asked directly this session.
This name goes on the ebook cover and Ch. 1. **Scope note:** this outline only records the
decision for ebook-drafting purposes — renaming the project/package everywhere else (this
project's own name, `~/.arti`, skill copy, landing page, etc.) is a separate, not-yet-scoped
follow-up and is *not* done as part of this plan. See `~/.arti/memory/todo-list.md`'s Open
decisions, updated to reflect this resolution.

## Reconciled tool/document names (post-2026-09-03 efficiency revision)

Per [[arti-package-scope]] and [[arti-decisions]], the following outline corrections apply
(not yet applied to `arti-proposal-full.md` itself — that edit is deferred to the editorial pass
after drafting, per the researcher's decision):

| Stale name (§04 as written 2026-09-03) | Current name | Where it shows up |
|---|---|---|
| Evaluation Log | **Iteration Log** (`iteration-log-template.md`) | Ch. 10 title + tool tie-in |
| Handoff Bundle | **Handoff Manifest** (`idea\handoff.md`) | Appendix "Toolkit Quick Reference" |
| Brief Outline | *(folded into Scratchbook, no longer a separate document)* | Checked Ch. 6, Ch. 8 — neither mentions Brief Outline; no correction needed |
| Revision Log | *(merged into Iteration Log, same as Evaluation Log above)* | Ch. 10, Ch. 12 (Rebuttal cross-links to Iteration Log entries, not a separate Revision Log) |

Sweep result: no other chapter references a renamed document. Ch. 2–9, 11, 13 and the Appendix's
non-Handoff entries all use current names already.

## Chapter-by-chapter outline (finalized — see task.md's 5-section shape)

Resolved against `task.md`'s literal 5-section list (Pendahuluan, Amati, Replikasi, Tambah Inovasi,
BONUS) and `abstraksi-konten-dan-outline.md`'s Chapter 2 (Claude & Claude Code content, previously
homeless in this outline). Part IV ("Publication Loop") is folded into **Tambah Inovasi** as
chapters 12–15, since it followed Part III here. The Appendix becomes **BONUS**, joined by
abstraksi's two BONUS entries (Panduan Instalasi, Prompt Guide). Structural skeleton only — chapter
titles are final, subchapters are not drafted yet (deferred to when each chapter is actually
written).

| Section | Ch. | Title | Teaching goal | Current tool tie-in | Source material | Open question |
|---|---|---|---|---|---|---|
| Pendahuluan | 1 | Kenapa AI Belum Memuaskan (Halusinasi & Memori) | Halusinasi vs. memori as the two failure modes that make chat-AI unreliable for scientific writing; the Alzheimer/50-First-Dates memory analogy | — | `abstraksi-konten-dan-outline.md` Ch.1; `inbox/260906_OK_narasi-ai-bar-bar-dan-analogi-alzheimer.md`; `inbox/260906_OK_narasi-pain-point-tenun-dan-solusi-arti-memory.md` | — |
| Pendahuluan | 2 | Claude dan Claude Code | Claude's "project" feature, memory, skills; why Claude Code specifically (vs. other AI tools), comparison table | — | `abstraksi-konten-dan-outline.md` Ch.2; `inbox/260906_OK_narasi-kenapa-claude-code.md` | New chapter per this session's decision — folds abstraksi's homeless Ch.2 content in here, restoring abstraksi's original 3-chapter Pendahuluan shape |
| Pendahuluan | 3 | Why ARTi Framework | Three meanings of the word (ART+i, Amati-Replikasi-Tambah inovasi, *arti*=meaning); why linear "research→write→find a journal" causes desk rejections; the loop diagram as the book's spine; reactive-vs-proactive journal-choice framing; also folds in abstraksi Ch.3's ARTi-overview material (3 Filosofi, Arsitektur, Sistem Memori, Sistem Referensi) | — (loop diagram, no single template) | `workflow-prompt.md` (project root); `abstraksi-konten-dan-outline.md` Ch.3; `inbox/260906_OK_narasi-kenapa-claude-code.md`; `~/.arti/memory/memories/landing-page-feature-inventory.md`'s "One-liner options" + "The core pitch" sections | Update the title/cover copy to say "ARTi Framework" once branding rename actually lands elsewhere |
| Amati | 4 | The Researcher Profile | Why the workflow starts with self-assessment, not literature; how the Novelty Ceiling is derived | `researcher-profile-template.md` | none yet | — |
| Amati | 5 | Mapping the Gap, Not Guessing It | Building a Gap Map from real literature; flagging contradictions instead of hiding them; the pseudo-gap trap (adapted from Inovelty, see [[inovelty-reference]]) | Gap Map (ARTi-idea) | none yet | — |
| Amati | 6 | Reading a Journal Tactically | Reading Aim & Scope for unwritten editorial preferences, not just keywords; populating Journal Profile Block A/B/C | `journal-profile-template.md` | none yet | — |
| Replikasi | 7 | Feasibility Before Ambition | The Experiment Blueprint's 🔴🟡🟢 flags; a red flag is a redesign trigger, never a rejection | Experiment Blueprint (ARTi-idea, Stage 4) | none yet | — |
| Replikasi | 8 | The Scratchbook Discipline | Raw, tagged, unpolished dumping beats premature synthesis; the full tagging system; source-tagging every claim as you go | `scratchbook-template.md` | `inbox/narasi-pdf-ke-md-sebelum-jadi-konteks.md` | — |
| Tambah Inovasi | 9 | Scoring Novelty Honestly | Full C/M/E rubric, composite label table; gap vs. novelty distinction (adapted from Inovelty) | `novelty-scoring-guide.md` | none yet | — |
| Tambah Inovasi | 10 | Building the Manuscript Blueprint | Turning Block B/C patterns into a paragraph-level outline before drafting; Contribution Statement written first | `manuscript-blueprint-template.md` | none yet | — |
| Tambah Inovasi | 11 | Matching Voice and Technical Depth | Using a Voice Profile deliberately; reading Block C against the Discussion | `voice-profile-template.md`; `gtmk-voice` skill (case study) | none yet | — |
| Tambah Inovasi | 12 | The Iteration Log and the Novelty Floor | Why a below-threshold novelty score is a desk-rejection risk prose can't outrun; framing problem vs. research problem | `iteration-log-template.md` | none yet | Title/prose must say "Iteration Log," not "Evaluation Log" — see reconciliation table above |
| Tambah Inovasi | 13 | Cover Letters and the Submission Checklist | A cover letter that argues fit, not summary; predatory-journal + compliance checks before sending | Cover Letter generator | none yet | — |
| Tambah Inovasi | 14 | Answering Reviewers Without Losing the Paper | Four comment categories; Strategic vs. Technical; resubmit/redirect/revise decision (adapted from Inovelty) | Rebuttal document, cross-linked to Iteration Log entries | none yet | — |
| Tambah Inovasi | 15 | After Acceptance: Feeding the Next Amati | Minimum-viable post-publication checklist; turning Limitations into the next Idea Bank entry | Publication Growth Log → Research Idea Bank | none yet | — |
| BONUS | 16 | Toolkit Quick Reference | One page per document: what it is, when to create it, file-naming convention; one-page checklist reprinted at full size | All of the above, current names | `~/.arti/memory/memories/landing-page-feature-inventory.md`'s per-stage feature tables | Use "Handoff Manifest" (`idea\handoff.md`), not "Handoff Bundle" — see reconciliation table above |
| BONUS | 17 | Panduan Instalasi | Installation walkthrough | — | `abstraksi-konten-dan-outline.md` BONUS list | — |
| BONUS | 18 | Prompt Guide | Prompt-writing reference | — | `abstraksi-konten-dan-outline.md` BONUS list | — |

**Coverage check:** all 18 chapters across 5 sections (Pendahuluan 3, Amati 3, Replikasi 2, Tambah
Inovasi 7, BONUS 3) accounted for. Most chapters still have no assigned source material beyond
Pendahuluan — expected at this stage; subchapter-level content is deferred per-chapter until
actually drafted.

## Change log
- 2026-09-09 08:50 — Created. Reconciled Ch. 10 + Appendix terminology against
  [[arti-package-scope]]; mapped six candidate source files (`workflow-prompt.md`, four `inbox/`
  Narasi files, `landing-page-feature-inventory.md`) onto chapters; recorded the ARTi Framework
  branding decision (confirmed by researcher this session); marked 10/13 chapters + Appendix as
  awaiting source material.
- 2026-09-09 — Finalized against `task.md`'s literal 5-section shape (Pendahuluan, Amati, Replikasi,
  Tambah Inovasi, BONUS): renumbered to 18 chapters, folded the old Part IV (Publication Loop) into
  Tambah Inovasi as Ch. 12–15, folded the old Appendix into BONUS as Ch. 16 alongside two new BONUS
  chapters (Panduan Instalasi, Prompt Guide) sourced from `abstraksi-konten-dan-outline.md`'s BONUS
  list. Added a new Ch. 2 "Claude dan Claude Code" inside Pendahuluan, restoring
  `abstraksi-konten-dan-outline.md`'s original 3-chapter Pendahuluan shape and giving its Claude/
  Claude Code content (previously homeless in this outline) a home. This table is now the single
  finalized outline skeleton; `abstraksi-konten-dan-outline.md` stays untouched as a historical
  scratch source. Structural skeleton only — no subchapters drafted yet.
