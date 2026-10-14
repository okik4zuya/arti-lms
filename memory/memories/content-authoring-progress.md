---
name: content-authoring-progress
description: Narrative status of arti-framework ebook content drafting, per chapter — what's done, why, and what's still open; checklist lives in todo-list.md
metadata:
  type: project
  created: 2026-09-10 19:40
  updated: 2026-09-11 07:50
---

Companion to the checklist at the top of `todo-list.md` (which tracks done/not-done only). This
file holds the *why* and *what happened* behind each chapter's status — read it before resuming
work on a chapter you haven't touched in a while. Drafting rules/mechanics live in
[[content-authoring-workflow]], not here.

## Pendahuluan

### 1. Kenapa AI Belum Memuaskan (Halusinasi & Memori) — done, all 4 subchapters drafted

- **2 Masalah: Halusinasi dan Memori** (`01-2-masalah-utama`) — first subchapter drafted this
  project. Establishes the two core pain points (hallucination, no cross-session memory) via the
  author's own AI-tool history (ChatGPT/Gemini/DeepSeek/Grok/z.ai/Claude, a self-built "Fine AI"
  app) and the "50 First Dates" analogy for memory loss.
- **Solusinya** (`02-solusi-parsial`) — the partial-solutions-only follow-up: NotebookLM for
  hallucination (but can't write/edit files), built-in chat memory for session loss (but it bleeds
  across unrelated topics/sessions). Sets up why the framework exists.
- **Halusinasi = Meruntuhkan Kredibilitas Akademik** (`03-halusinasi-meruntuhkan-kredibilitas-akademik`,
  new, inserted 2026-09-11) — drafted from the outline
  (`resources/inbox/abstraksi-konten-dan-outline.md` line 50) plus the "AI bar-bar" narasi verbatim
  material in `resources/inbox/260906_OK_narasi-ai-bar-bar-dan-analogi-alzheimer.md`. Opens with
  the "dosa jariyah" chain-of-citation argument for why hallucination is dangerous specifically for
  academic work, briefly acknowledges the pro/kontra debate around AI use, then makes the author's
  actual position: fine to use, conditioned on staying deliberate about it. The bulk of the
  subchapter expands the "wajar vs bar-bar" distinction from the inbox narasi — todo-list/dosen
  analogy, the expertise-vs-no-expertise task split (outline/reference-selection needs expertise,
  synthesis/grammar-polish doesn't) — closing with a bridge line pointing at the still-unsolved
  memory problem, teeing up the next subchapter. Untitled H1 fixed the outline's typo
  ("kerdibilitas" → "kredibilitas").
- **Hilang Memori: Pekerjaan jadi Tak Berarti** (`04-hilang-memori-tak-berarti`, renumbered from
  `03-` to make room for the halusinasi subchapter above) — drafted 2026-09-10 from
  `resources/inbox/pemintal-benang.md` (the Rithah/spinner parable, Qur'an an-Nahl 92). Retells the
  parable, then draws the analogy: re-explaining context every new AI session is like Rithah
  spinning thread all day and unraveling it every night — effort that never accumulates. Image:
  first drafted with a downloaded Wikimedia Commons public-domain placeholder (Bouguereau's "The
  Spinner"); the user replaced it with their own `pemintal-benang.png` shortly after. A second
  image, `images/3-spinners.PNG`, was also dropped into that folder by the user but is **not yet
  referenced** in the subchapter text — revisit if asked to use it.
- All four folders were originally unprefixed or off-by-one; the first three were renamed
  2026-09-10 to match `manifest.yaml`'s numbered slugs. Inserting the halusinasi subchapter
  2026-09-11 required renumbering every downstream subchapter folder in the whole book by +1
  (chapters 2 and 3 too — `04-taaruf` through `15-dan-lainnya` all shifted to `05-`…`16-`) to keep
  the established sequential-numbering convention; only `hilang-memori`'s `order` frontmatter value
  actually changed (3→4, its real position within the Masalah chapter) — everyone else's in-chapter
  `order` was unaffected, since that field is scoped per-chapter, not global (see
  [[content-authoring-workflow]]). The old `01-intro` pipeline-placeholder chapter (superseded by
  these four) was deleted by the user in an earlier session.
- **Not yet verified this pass**: MySQL wasn't running (Laragon likely needs starting), so
  `php artisan cache:clear` failed and the standalone-PHP-script render check wasn't run. Re-verify
  all 4 subchapters (especially the new one and the renumbered `04-hilang-memori-tak-berarti`) once
  the DB is back up.

### 2. Claude dan Claude Code — in progress, 4 of 6 outline sections drafted

Source outline: Chapter 2 ("Claude: Apakah Ini Jawabannya?") in
`resources/inbox/abstraksi-konten-dan-outline.md`.

- **Taaruf** (`04-taaruf`, renumbered from `03-`) — author's first Claude experience ("langsung
  jatuh cinta"), framed with the Islamic-courtship pun (taaruf = getting-acquainted-before-
  committing). Covers: Claude is more analytical but weaker at prose than ChatGPT (teed up, not
  fully explained — deferred to the next subchapter); the "Project" feature (instructions + files
  + now per-project memory) as the answer to chapter 1's two pain points — project files for
  hallucination, per-project-scoped memory (not bleeding across topics like ChatGPT/Gemini/
  Claude's global memory, per `02-solusi-parsial`) for the memory problem; Claude's file-producing
  tools (docx/xlsx) mentioned briefly. Deliberately doesn't declare the problem fully solved —
  closes with "masih ada beberapa hal yang perlu saya cek", matching the chapter title's open
  question.
- **Claude: Model dengan Branding Logika** (`05-claude-model-dengan-branding-logika`, renumbered
  from `04-`) — expanded from bullets (model lineup, benchmark vs other providers, the "branding"
  framing, the programmer evidence, why logic matters for article-writing). Added an angle not in
  the original bullets to make the "branding" title concrete: Anthropic's tier names (Haiku/
  Sonnet/Opus/Fable) are literary terms, but the real brand equity is logic/analysis, not prose —
  pivots from that framing device into the outline's actual claims (Sonnet code mostly runs first
  try vs other providers needing more fixes; suited for article-writing because it needs analysis,
  not just wordsmithing). Added a qualitative benchmark table (2026-09-10) comparing Claude against
  research-oriented models/features — ChatGPT Deep Research, Gemini Deep Research, Perplexity Deep
  Research — across fitur riset / analisis & logika / menulis & edit file / memori per-topik;
  deliberately framed as personal notes ("bukan angka benchmark resmi"), not fabricated numeric
  scores, to stay consistent with the chapter's own anti-hallucination stance. Ends on an open note
  ("belum sepenuhnya") rather than declaring Claude the final answer, consistent with Taaruf's
  ending and the chapter's overall "Apakah Ini Jawabannya?" question.
- **Masih Ada yang Kurang** (`06-masih-ada-yang-kurang`, new 2026-09-10) — expanded from the
  outline bullets: memory still not flexible enough, file write/edit not suited to an iteratively-
  revised manuscript, so the author still copy-pastes Claude's output into the actual manuscript
  file by hand. Frames this as "not a real problem, but could the workflow be more efficient?" —
  ends with a forward-pointing hook ("jawabannya ada") teeing up Claude Code in the next stub
  section.
- **Claude Code** (`07-claude-code`, new 2026-09-10) — bridges directly from `06`'s closing
  question ("apakah workflow-nya bisa dibuat lebih efisien?"). Explains Claude Code as the
  file-native counterpart to Claude AI, then a 6-row × 4-col comparison table (Claude Code vs
  Claude AI vs NotebookLM vs "LLM chat lain") covering file sumber / memori / tools / penggunaan
  iterasi / keamanan data / biaya pemakaian (biaya added as the "usulkan lainnya" 6th row the
  outline asked for). Closes by explicitly answering `06`'s open question (yes — no more manual
  copy-paste) and teeing up ARTi Framework as the next chapter, since a file-native tool alone
  isn't yet a *system* for persistent, structured memory. Fixed the dangling `⟦Solusinya⟧`
  wiki-link bug noted below by using the correct title `⟦Solusi Parsial⟧` instead (didn't touch
  `06`'s existing dangling ref, just avoided repeating the mistake here).
- Remaining outline sections in this chapter are still empty `##` stubs, not yet expandable without
  more input from the user: "Claude bisa pakai skill agar hasil lebih maksimal".
- A stray/erroneous subchapter dir (`claude-ai-solusi-yang-hampir-sempurna`, untracked, content was
  a duplicate of `02-solusi-parsial`'s "Solusinya" text under the wrong title) was found sitting
  under this chapter in the manifest before drafting started; deleted and replaced with the two
  real subchapters above.

### 3. Why ARTi Framework — done, all 11 subchapters drafted

Source outline: Chapter 3 in `resources/inbox/abstraksi-konten-dan-outline.md`, which for this
chapter was already fully fleshed-out bullets (unlike chapter 2's empty stubs) — drafted straight
through in one pass, no user input needed mid-draft.

- **Solusi dari Masalah Saya Selama Ini (Mungkin Anda Juga)** (`09-solusi-dari-masalah-saya`) —
  bridges from `08-claude-code`'s closing line (a tool alone isn't a system); restates the 2
  problems from ch.1 + the 1 gap from ch.2; introduces the ARTi Framework name; "mungkin anda juga"
  section; honest disclaimer it's still evolving but already proven for the author.
- **3 Filosofi** (`10-3-filosofi`) — the 3-layer name meaning: ART+i (state of the art + own
  innovation), Amati/Replikasi/Tambah Inovasi (the 3-stage workflow = the book's 3 main sections
  after this one), and "arti" (Indonesian for meaning, callback to
  `04-hilang-memori-tak-berarti`'s spinner parable).
- **Menjadikan Pekerjaan Berarti** (`11-menjadikan-pekerjaan-berarti`) — returns to the spinner
  parable, contrasts it with ARTi's persistent sessions; previews the 3 pillars (memori persistent,
  referensi terlacak, scratchbook) that the next 3 subchapters each expand on.
- **Arsitektur** (`12-arsitektur`) — first subchapter in this project with an ASCII-art diagram
  (fenced code block, renders as `<pre><code>`, verified unescaped): Claude Code as the engine,
  skills (ARTi-setup/idea/writing/figure) + `memory/`/`inbox/`/dashboard as the map on top; a
  component table; callback to the ch.2 comparison table's "keamanan data" row (fully local, no
  third-party server).
- **Sistem Memori** (`13-sistem-memori`) — contrasts with Claude AI's cross-topic-bleeding memory
  (`07-masih-ada-yang-kurang`, `02-solusi-parsial`); explains the `memory/` folder's small-file-
  per-topic pattern + one-line index file (this project's own actual memory system, described
  in-universe as the framework's feature — a deliberate parallel, not just an example).
- **Sistem Referensi** (`14-sistem-referensi`) — answers ch.1's hallucination problem: mandatory
  source-tagging, literature pre-converted to searchable Markdown before use, contrasted with
  NotebookLM (`02-solusi-parsial`) being summarization-only and disconnected from the writing
  process.
- **Scratchbook: File Sakti Anti Halusinasi** (`15-scratchbook`) — raw tagged quotes as the only
  source of truth before any claim reaches the manuscript; honest that this is slower up front but
  is what actually blocks hallucination; closing section ties all 3 pillars together.
- **Fitur-fitur ARTi** (`16-fitur-fitur-arti`, new 2026-09-11) — concrete feature inventory pulled
  from the memory/referensi/scratchbook pillars plus the 4 skills (ARTi-setup/idea/writing/figure,
  described from their actual skill-frontmatter descriptions since this repo has no built landing
  page yet — `welcome.blade.php` is still todo-list.md's unbuilt placeholder), the local dashboard,
  and the local-only data-security angle.
- **Perbandingan ARTi Framework dengan yang Lain** (`17-perbandingan-arti-framework`, new
  2026-09-11) — two comparison tables: Claude Code polos vs Claude Code+ARTi (the framework-as-
  layer framing), then ARTi vs Claude AI native vs NotebookLM vs other chat LLMs (extending
  `08-claude-code`'s tool-level table up one level to framework-vs-tool); closes with an honest
  trade-off paragraph (setup cost, discipline required) rather than declaring ARTi strictly better.
- **dan Lainnya** (`18-dan-lainnya`, renumbered from `16-` to make room for the two subchapters
  above) — explicitly scopes this chapter as the "big map" only; defers skill details (ARTi-idea,
  ARTi-writing, novelty scoring, dashboard) to the Amati/Replikasi/Tambah Inovasi sections; closing
  bridged to the new Cara Instalasi subchapter instead of straight to Chapter 4, since installation
  now sits after it in the outline order.
- **Cara Instalasi ARTi Framework** (`19-cara-instalasi-arti-framework`, new 2026-09-11) — from the
  outline's 4 bullets (VSCode+Claude Code extension, Drawio, ArtiFrameworkSetup, ArtiPdfSetup);
  kept deliberately high-level (what each component is for, not a click-by-click sequence) and
  explicitly defers the step-by-step walkthrough to the BONUS "Panduan Instalasi" chapter; closes
  the chapter and bridges to Chapter 4 (Amati).
- This chapter now has 11 subchapters (was 8); manifest.yaml updated. **Not yet cache-cleared or
  render-verified** — MySQL/Laragon wasn't running this session (same blocker as the 2026-09-11
  halusinasi-insert session); re-run `php artisan cache:clear` + the standalone-PHP-script
  verification once it's up.

## Amati / Replikasi / Tambah Inovasi / BONUS (chapters 4–18)

Not started. No inbox source material confirmed yet beyond the finalized structural skeleton in
`resources/inbox/ebook-outline-v2.md` (titles/grouping only, no prose or bullets per chapter) — check
`resources/inbox/` for a chapter-specific outline before drafting from scratch, per
[[content-authoring-workflow]].

## Change log
- 2026-09-11 07:50 — Drafted chapter 3's 3 remaining subchapters (Fitur-fitur ARTi, Perbandingan
  ARTi Framework dengan yang Lain, Cara Instalasi ARTi Framework); renumbered `16-dan-lainnya` to
  `18-dan-lainnya` to preserve outline order (Fitur-fitur/Perbandingan before Penutup, Cara
  Instalasi after), updated manifest.yaml and `dan-lainnya`'s `order` field (8→10); fixed a
  pre-existing off-by-one in this section's own subchapter-slug references (was still citing
  `08-`…`15-` after the 2026-09-11 07:37 halusinasi-insert renumbered them to `09-`…`16-`). Chapter
  3 now has all 11 subchapters from the outline. Not cache-cleared or render-verified — MySQL down.
- 2026-09-11 07:37 — Inserted "Halusinasi = Meruntuhkan Kredibilitas Akademik"
  (`03-halusinasi-meruntuhkan-kredibilitas-akademik`) as chapter 1's new 3rd subchapter, pushing
  Hilang Memori to 4th; renumbered every downstream subchapter folder in the book by +1 to keep the
  sequential-numbering convention; updated manifest.yaml. Not yet cache-cleared or render-verified
  — MySQL was down this session.
- 2026-09-10 22:07 — Drafted all 8 subchapters of chapter 3 (Why ARTi Framework) in one pass from
  the already-fleshed-out outline; manifest.yaml updated (8 real slugs replacing the
  `why-arti-framework-01` placeholder); verified via a standalone PHP script since
  `tinker <file>` stopped working in this session (see [[content-authoring-workflow]]).
- 2026-09-10 21:52 — Drafted `07-claude-code`, added to manifest.yaml, tinker-verified (title,
  table, style block, and `⟦Solusi Parsial⟧` wiki-link all resolve correctly).
- 2026-09-10 19:40 — Created (split out of the todo-list.md checklist, which now only tracks
  done/not-done) after the user asked for a dedicated progress memory + todo section, to be kept in
  sync on every content-authoring memory update going forward.
- 2026-09-10 19:52 — Drafted chapter 2's first two subchapters (Taaruf,
  Claude: Model dengan Branding Logika); deleted a stray erroneous placeholder dir found under the
  chapter first; tinker-verified both render.
- 2026-09-10 20:26 — Renumbered `03-taaruf`→`04-taaruf` and
  `04-claude-model-dengan-branding-logika`→`05-claude-model-dengan-branding-logika`, added a
  research-model benchmark table to the latter, drafted new `06-masih-ada-yang-kurang`, updated
  manifest.yaml. Noted `ContentController::show()`'s signature now takes `Course $course` (not a
  slug string) in [[content-authoring-workflow]]'s verification snippet — all three
  tinker-verified rendering after the fix.
