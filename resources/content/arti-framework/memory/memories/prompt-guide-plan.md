---
name: prompt-guide-plan
description: Structure, decisions and progress of the Prompt Guide chapter (BONUS): one terse guide ordered by the arti-workflow.png stages, usable by every runbook stage
metadata:
  type: project
  created: 2026-10-20 11:30
  updated: 2026-10-20 11:56
---

Built from [[real-workflow-analysis]]. Chapter slug `prompt-guide` in the BONUS section; subchapter folders `prompt-guide-NN-<name>/`.

## Decisions (researcher, 2026-10-20)
- **Terse:** short titles, prompts straight away, almost no prose (first version was rejected as too wordy and replaced).
- **One guide for every runbook stage.** Order follows the usage order; the runbook itself must follow `G:\My Drive\RESEARCH\ARTi Workflow\figures\arti-workflow.png` (ARTi-idea + ARTi-writing: Topik awal, Search keyword, RIS, Gap map, Researcher Profile from Wawancara, Fulltext, Reference Database, Idea Canvas + Novelty, Idea Bank, Research Design, Journal Target Sheet with Jfinder, Eksperimen -> Data, Contoh paper -> Journal profile and Writing style, Blueprint, Scratchbook, Manuscript).
- Language: Indonesian, English where needed; tags/terms (yolo, Recommended, `[LIT: key]`) stay English.
- Placeholders in prompts, no real project or paper names.
- Literature: OpenAlex (`arti-search`) main; Scopus only to verify (Claude prepares bare strings, researcher searches and uploads RIS plus counts); PDFs: OA via arti-search, the rest from the researcher.
- Cover letter, rebuttal, growth log: later.
- Prompts not yet used on a finished project are labelled "(saran)".

## Pages (all written, in `manifest.yaml`)
01 Sesi (cross-cutting: mulai, lanjut, templat sesi, yolo, simpan, cek memory, setuju/tanya/koreksi, wrap-up) · 02 Profil dan Topik · 03 Keyword dan RIS · 04 Gap Map (+ novelty, kill-test) · 05 Fulltext · 06 Idea Canvas (+ Idea Bank) · 07 Research Design · 08 Journal Target · 09 Journal Profile · 10 Blueprint · 11 Data (empirical path only) · 12 Scratchbook · 13 Style dan Manuscript · 14 Revisi (annotations, figures, SI) · 15 Skill (meta loop).

Page format: title, one line "Anda: ... Claude: ...", prompts with short labels (Ringkas, Lengkap, Yolo, Tanya, Koreksi), one line "Lanjut bila: ...".

## Open
- SLR-specific stages (protokol, screening Gate 1/2, ekstraksi, PRISMA, matriks sintesis) follow the ARTi-SLR diagram, not arti-workflow.png: decide whether to add them as extra pages.
- Retrofit path (existing manuscript) and the deadline-first path have no dedicated page; their prompts sit in Profil dan Topik and Keyword dan RIS.
- Browser render check still open (MySQL and Redis were down; a standalone GFM + frontmatter + wiki-link check passed for all pages).

## Change log
- 2026-10-20 11:30 — created; first version (12 wordy subbab, 4 written).
- 2026-10-20 11:56 — rewritten after the "too wordy" feedback: 15 terse pages ordered by arti-workflow.png; old four pages deleted.

## Gap-filling prompts (2026-10-20 11:56)
After the researcher asked to develop prompts instead of copying only verbatim ones, each page got 3-7 new "(saran)" sections (about 75 prompts) that close gaps found by comparing the workflow diagram and the skills with real failures in the five projects: stale or empty search log, truncated example titles, numbers changing after editing, "done" claims without proof, no pre-mortem or analysis plan before data, no self-test of claims. They are untested drafts: label stays "(saran)" until used on a finished project, then the label is dropped. The legend on the Sesi page explains the label.
