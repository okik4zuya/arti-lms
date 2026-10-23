---
name: real-workflow-analysis
description: Evidence-based analysis (2026-10-20) of the workflow the researcher actually runs across five paper projects, compared with the designed ARTi-SLR/ARTi-idea + ARTi-writing diagrams; basis for restructuring the ebook and for the Prompt Guide
metadata:
  type: project
  created: 2026-10-20 10:25
  updated: 2026-10-20 11:56
---

Analysis requested before restructuring the ebook. The two designed diagrams (ARTi-SLR + ARTi-writing; ARTi-idea + ARTi-writing) were used as the baseline. Sources are listed per project; the Prompt Guide seeds are at the end.

## Evidence base and its limits

| Project | Source | Coverage |
|---|---|---|
| Paper Review SLR SnO2 Photocatalyst | 34 session transcripts (+12 subagent runs), 276 researcher messages, 2026-09-27 to 10-12 | Complete from project start |
| Paper Metnum ECE | 17 transcripts (10-02 to 10-03) + 22 raw logs in `~/.arti/workflow-sessions/` (09-03 to 09-07) + project memory | Sessions between 09-08 and 10-01 exist only as memory notes |
| Paper SLR Nanobubble in Reaction | No transcripts (older than the retention window); 6 raw logs (09-15, 09-16), project memory, `inbox/prompt_walkthrough.md` | Reconstructed from logs; first sessions (09-14) only from memory |
| Paper AI-ML-LLM IJMMM | 2 transcripts (10-02), 35 messages | Complete (project is 1 day old) |
| Paper CO2 Photoreduction | 2 short transcripts + project memory | Manuscript pre-dates ARTi (Aug 2026), scaffolded 09-06 |

The other ~25 logs in `workflow-sessions/` (dashboard, installer, chat feature) are tool development, not paper workflow; only their titles were checked. Counts below are regex estimates.

## Real flow per project (order of events)

- **SnO2 SLR (review).** Topic one-liner and `/ARTi-SLR` -> Scopus strings tuned live with the researcher pasting hit counts (22,062 -> 14,000 without SnO2 -> 14 -> 43) -> RIS into arti-lit (80 deduped) -> screening -> researcher downloads fulltexts from a generated list -> Gate 2 by 8 read-only subagents -> extraction -> PRISMA locked (10-01) -> novelty/prior-review check (only because the researcher asked "kapan menganalisis novelty") -> journal candidates via arti-jfinder -> Idea Canvas as by-product, saved to idea bank -> journal profile from 5+3 example reviews they supplied -> blueprint revised -> Scratchbook in 15 chunked sessions (S1-S15) -> 6 "finishing gates" (decisions, fulltext number checks, late find su-2020 forcing a protocol change) -> Stage 4 figures/tables from scripts -> handoff (10-02) -> draft 1 written in one yolo run (S2-S9, ~13,950 words, 28 decision flags) -> figure iteration, inline annotation rounds, new-evidence additions (Fig. 1 "under-explored" search, "why SnO2" paragraph), section reorder (10-03 to 10-12).
- **Metnum ECE (empirical, own data).** One-line topic + "What to do next?" -> Researcher Profile gets an education section -> gap map from Claude's own web search -> Idea Canvas -> Experiment Blueprint -> journal target -> handoff (all in one session, 09-03) -> same-day revision: researcher corrects the construct, Scopus novelty check with RIS exports -> voice profile, journal profile from 7 example papers -> blueprint -> Scratchbook -> literature feed (Claude-run OpenAlex shortlist, researcher supplies fulltext) -> intro draft -> instrument design for H1-H3 (continues inside the writing stage) -> methods -> compression and voice passes -> data arrives (pretest N=187, 10-02): Python analysis, `[OWN]` entries in Scratchbook, targeted literature enrichment -> writing plan S1-S7 with four hard rules; S4-S7 wait for posttest.
- **Nanobubble SLR (narrative review built through ARTi-idea).** Gap map from 353 references already in arti-lit ("infer the scope, flag what looks off") -> Idea Canvas -> Research Design with a 3-axis classification -> PDF ingestion + citation chase -> classification pass with a 15% blind re-check -> reframing decision ("reconciling" -> "importing an unasked question") -> figure + table -> journal target (JECE) -> journal profile from 96 JECE papers (title-pattern error found and fixed) -> blueprint -> Scratchbook in 9 sessions -> Draft 1 (Introduction, Methods) -> word-choice passes with propose-then-approve.
- **IJMMM (constraint-driven).** `notes/call-for-papers.md` + deadline + "computation only" -> brainstorm against Researcher Profile -> "blue sea" topic pick -> gap map from OpenAlex first, Scopus later -> Scopus kill-tests A-F pasted back as counts -> gap map revised 13 times, claim narrowed after finding competitors -> go/redesign/stop checkpoint 2026-10-22.
- **CO2 Photoreduction (retrofit).** Manuscript written before ARTi; ARTi used as on-demand lookups ("jelaskan paper xie 2026", "carikan referensi untuk kalimat ini").

## The common thread (eight recurring moves)

1. **Anchor in constraints and what only the researcher holds:** profile, deadline, own data, supplied example papers, supplied fulltext, Scopus access.
2. **Literature loop:** Claude proposes narrow bare-string queries (one code block each) -> researcher runs Scopus, pastes counts, drops RIS -> Claude logs, dedupes, screens -> researcher downloads PDFs from a DOI list -> markdown twins. Evolved on 10-03 into Claude-run OpenAlex/Semantic Scholar/Scholar via a self-built MCP; researcher keeps only paywalled PDFs and Scopus kill-tests.
3. **Decide with evidence, narrow rather than abandon:** novelty re-scoring, prior-review check, kill-tests, protocol-change log, go/no-go checkpoint.
4. **Observe -> replicate -> add:** pick 3-8 example papers of the target journal, extract patterns (journal profile), copy structure, add a "value layer" (documented literally in `strategy_observe-replicate-innovate_MTC.md`).
5. **Scratchbook as the only staging area:** chunked sessions, fixed templates, tags, "Argumen saat ini" line, max 5 fulltexts per session, density and contradiction audits, a gate list before drafting.
6. **Numbers by script, verified at the source:** audit scripts, T/D/C/DC markers, pdftotext or page render for caveated values; figures built from CSV by script and checked by reading the PNG; captions kept short.
7. **Draft in batches under hard rules with audits, then read and annotate:** yolo drafting with a decision-flag file, claim-audit table, counted voice audit, slip ledger, versioned drafts; researcher annotates in the dashboard; ARTi-annotate resolves; new evidence triggers new search/figure.
8. **Meta-loop:** after each stage, convert friction into a skill or tool change ("berdasarkan pengalaman ini usulkan perbaikan skill"), implemented in a separate session via "implement plan: <path>".

Session mechanics: one session = one bounded task; close with "update memory" and "pindah sesi chat baru, buat prompt siap copy"; the generated prompt (often saved as `next-session-prompt_*.md`) carries state; `/compact` in long sessions; occasionally two parallel sessions. In SnO2, 96 of 276 messages (35%) were six words or fewer ("ya", "setuju", "lanjut", "proceed").

## Divergence from the designed diagrams

Matches closely: ARTi-SLR front half (topic -> RQ/PICO -> protocol -> search -> RIS -> screening -> fulltext -> extraction -> eligibility -> appraisal -> PRISMA + synthesis matrix -> handoff -> journal target sheet -> idea canvas/idea bank loop) and ARTi-writing (example papers -> journal profile -> blueprint -> scratchbook -> manuscript).

Differs or missing in the diagrams:
- Novelty check sits right after RQ in the SLR diagram; in practice it came after screening and only on the researcher's prompt. Skill was then updated (Prior Review Check).
- Blueprint is produced twice in SLR: preliminary SLR blueprint before the journal is profiled, revised after the journal profile.
- Literature loop is not linear (query tuning, late finds, protocol-change log).
- Metnum: gap map came before RIS/Scopus; Scopus came after the canvas as verification, and the idea was revised after handoff (construct correction). Research Design is not frozen at handoff: instruments are designed during writing.
- Not in diagrams: Scratchbook finishing gates; script-computed data and figures before drafting; claim audit, voice ledger, decision flags, drafting tracker; annotation loop; Supplementary Information as its own deliverable; constraint-driven topic selection with go/no-go (IJMMM); retrofit path for an existing manuscript (CO2); hybrid review via ARTi-idea (Nanobubble); session handoff prompts.
- Writing style: diagram derives it from example papers; in practice `gtmk-voice` (or `hm-simple`) is fixed with researcher-specific modifiers.
- Back half untested: no project has reached cover letter, rebuttal or growth log; Iteration Log exists only in Nanobubble (10 KB). Metnum's was never created.

## Prompt Guide seeds

Real prompt types found (generalise before publishing; many are Indonesian, IJMMM/SnO2 search prompts partly English):
1. Opening one-liner: topic + "What to do next?" / "Brainstorm. What is the possibility considering my research profile. notes/call-for-papers.md".
2. Query request with fixed style rules (bare strings, 5-8 queries, specific technical phrase, one code block each, remind me to log counts) — `inbox/prompt_walkthrough.md` in Nanobubble holds the researcher's own versions for keywords, Gap Map, Idea Canvas, Research Design (review and article), PDF ingestion, fulltext registration.
3. Report-back format: pasted counts with filter columns, "ris files uploaded in literature/exports".
4. Next-session prompt anatomy: task and stage; BACA DULU (ordered file list + working-preferences); KEADAAN / locked facts; TUGAS (numbered); ATURAN TETAP (language split, python path, no git, no iteration log, tag system); decisions open (ask briefly, do not block).
5. Decision gate: "present N decisions at once, each with one (Recommended) option and reason"; reply "semua setuju kecuali no. 10 - <reason>".
6. Yolo: "answer yourself, think like I am; flag every decision and need-verification item at the end".
7. Drafting hard rules: voice skill + modifiers, every statement sourced (elaboration only after literature), concise sentences, session ends with claim audit + voice audit + ledger.
8. Propose-before-edit for wording: "propose, do not edit before I approve".
9. Verification questions: "apakah semua poin yang perlu dicek sudah dikumpulkan?", "bagaimana anda menghitung estimasi...?", "apakah angka band gap sudah dicek selain dari ravi?".
10. Annotation: "perbaiki manuskrip berdasarkan anotasi <path>".
11. Closing: "update memory. pindah ke sesi chat baru, buatkan prompt awal siap copy".
12. Skill feedback: "berdasarkan pengalaman ini usulkan perbaikan di skill X" then "implement plan: <path>".
13. Preference capture: "tambahkan preferensi: ...", "pelajari polanya, tambahkan ke preferensi saya".

## Change log
- 2026-10-20 10:25 — created from the transcript, workflow-session and project-artifact analysis.

## Decisions (2026-10-20, researcher)
- Restructure by path, not by a fixed Amati -> Replikasi -> Inovasi order; ARI is a lens that can apply at any stage.
- Prompt Guide language: Indonesian, with English where needed.
- Back-half chapters (cover letter, rebuttal, growth log) are written later.
- Runbook per path must account for `arti-search` (SnO2 SLR was run before it existed: Scopus-first iteration). Several prompt variants per stage are welcome; add a wrap-up prompt that asks for the next-session prompt.
- 2026-10-20 (later): literature module = OpenAlex via `arti-search` as the main search; Scopus only for verification, keeping the researcher's mechanic (Claude prepares bare keyword strings, researcher searches, uploads the export and counts); PDFs still supplied by the researcher from a DOI list (OA first via `arti-search fulltext`). A dedicated Prompt Guide chapter in the ARTi-framework course is next; outline first.
