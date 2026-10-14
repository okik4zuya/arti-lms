---
name: content-authoring-workflow
description: Rules + repeatable workflow for drafting arti-framework ebook subchapter content, plus the markdown rendering pipeline (GFM tables, wiki-links, disallowed-raw-html config) so each new chapter doesn't require re-deriving conventions
metadata:
  type: project
  created: 2026-09-10 04:08
  updated: 2026-09-11 07:37
---

Started 2026-09-10 when the user said content-writing will now be the main ongoing work on this
repo. Codifies what was learned drafting the first real chapter (`kenapa-ai-belum-memuaskan`'s two
subchapters), replacing the last `01-intro` pipeline-placeholder content with real material.

## Source material

- Raw draft prose/outline lives in `resources/inbox/` — currently `abstraksi-konten-dan-outline.md`
  (chapter-by-chapter narasi and bullet outlines, the actual writing voice to match) and
  `ebook-outline-v2.md` (finalized 5-section/18-chapter structural skeleton, see
  [[arti-framework-context]]). Always check `resources/inbox/` first for source content before
  drafting from scratch — most chapters already have at least a bullet outline there.
- Some inbox sections are **already-written narasi** (use near-verbatim, lightly cleaned up) and
  some are **bullet outlines** (must be expanded into full narrative prose in the same voice as the
  written sections). Don't blend scope from a later outline section into the subchapter it wasn't
  asked for — e.g. a "Hilang memori = pekerjaan tidak berarti" aside that follows an outline block
  belongs in its own future subchapter, not folded into the current one, unless the user says so.

## Voice / style (Bahasa Indonesia)

- First-person, conversational, rhetorical questions to the reader ("Apakah Anda merasakannya
  juga?"), short punchy paragraphs, occasional sentence fragments for emphasis ("Plek ketiplek.").
  Concrete personal anecdotes over abstract claims.
- When expanding a bullet outline into prose, mirror the sentence rhythm and connective tissue of
  the nearest already-written section (e.g. "Namun...", "Akhirnya...", "Ternyata...") rather than
  writing generic explainer prose.
- Section headers inside a subchapter use `##`, matching the H1 page title.

## File + manifest mechanics

- One subchapter = one dir: `resources/content/arti-framework/{subchapter-slug}/index.md`. Slugs
  are **flat and globally unique per course** (not nested under chapter), per
  [[subchapter-hierarchy]].
- Frontmatter: `title`, `order` (position within its chapter — cosmetic only, **not currently read
  by any app code**; actual display order comes from list order in `manifest.yaml`, so keep both in
  sync by hand), `published: true` (false = 404s, useful for staging a draft in place before it's
  ready).
- Every new/changed subchapter must be added to `resources/content/arti-framework/manifest.yaml`
  under its chapter's `subchapters:` list (slug + title) — content with no manifest entry is
  unreachable from the sidebar/dashboard, and a manifest entry with no content dir 404s. Chapters
  themselves are pure grouping labels with no content file of their own.
- Manifest is cached for 1 hour (`ContentController::manifest()`); run `php artisan cache:clear`
  after any manifest.yaml edit before verifying.

## Verification (no browser tool available in this environment)

Standard tinker snippet to confirm a subchapter actually renders (404 vs real page) without a
browser — log in as `test@example.com`/`password`, build a `Request` with an explicit
`setUserResolver` (plain `Request::create()` has no authenticated user, `$request->user()` is null
and `ContentController::show()` throws on `->progress()`), then call the controller directly and
inspect `getData()['title']`/`html` length. A thrown `abort_unless` (404/403) means either the
manifest wasn't updated, the file isn't at the right path, or `published` isn't `true`.

`ContentController::show(Request $request, Course $course, string $slug)` takes a resolved
`Course` model as its 2nd arg, not a slug string — resolve it first
(`Course::where('slug', 'arti-framework')->first()`) and pass the model, or the call throws a
`TypeError` before it even gets to the `abort_unless` checks.

**`php artisan tinker path\to\verify.php` no longer works in this environment** (found
2026-09-10 drafting chapter 3): the `include` argument just echoes the file's source text back
verbatim without executing it — no error, just silent non-execution, easy to mistake for success
if you don't check the output content. Piping the script via stdin (`Get-Content | php artisan
tinker`) also fails, with a PHP parse error on multi-line syntax. **Working alternative**: skip
tinker entirely and boot Laravel directly in a standalone script —
`require 'C:/laragon6/www/arti-lms/vendor/autoload.php'; $app = require
'C:/laragon6/www/arti-lms/bootstrap/app.php'; $app->make(Illuminate\Contracts\Console\Kernel::class)
->bootstrap();` at the top of the `.php` file, then run it with plain `php path\to\verify.php` (not
`artisan tinker`). Confirmed working end-to-end (controller calls, `route()` helper, DB queries all
resolve correctly) verifying all 8 of chapter 3's subchapters.

## Readability styling (approved 2026-09-10, subchapter 1 of 2-masalah-halusinasi-dan-memori)

User wants a dialog-style reading experience, not textbook prose: bigger font, narrower reading
column, one sentence per line. Approved pattern, replicated to each subchapter as it's drafted or
touched (still manual — each subchapter needs the same block pasted in, nothing generates it):

- **Per-file scoped `<style>` block**, pasted right after the YAML frontmatter, before the H1 — not
  a global `resources/css/app.css` change. Raw HTML/`<style>` in a subchapter's `index.md` passes
  straight through the markdown pipeline (see **Markdown rendering pipeline** below for the exact
  config that makes this true — it's not the library default). This means styling can be
  iterated/approved per-subchapter before ever touching the shared stylesheet.
- Font: **Atkinson Hyperlegible** (Google Font, purpose-built for legibility), loaded via `@import`
  inside the style block, falling back to the existing `var(--font-sans)` token.
- Reading column: `.content { max-width: 600px; }` (down from the global 860px, "agar kepala tidak
  perlu memutar").
- Font size: body `1.2rem` / line-height `1.75`, h1 `1.85rem`, h2 `1.5rem` (global tokens are
  smaller — see `--font-size-*` in `app.css:31-36`).
- **One sentence = one paragraph** (blank line between each in the `.md` source), so CommonMark
  emits one `<p>` per sentence instead of one `<p>` per multi-sentence block. Only fuse two
  sentences into one paragraph when they're inseparable (the user's own example: "Agar lebih mudah,
  kalau saya analogikan, model AI itu sangat mirip dengan penderita penyakit Alzheimer. Plek
  ketiplek.").
- Verified by running the exact `YamlFrontMatter` + `CommonMarkConverter` pipeline standalone (no
  browser available in this environment) — confirmed the `<style>` tag survives conversion
  unescaped and sentence count matched `<p>` count.

## Markdown rendering pipeline (2026-09-10 21:27)

`ContentController::show()` uses `League\CommonMark\GithubFlavoredMarkdownConverter` (not the plain
`CommonMarkConverter` used before), with explicit config — needed because two real chapters
(05-claude-model-dengan-branding-logika's benchmark table, and `⟦Title⟧`-style cross-references in
05 and 06) surfaced two gaps:

- **GFM pipe tables** (`| a | b |` syntax) need `GithubFlavoredMarkdownConverter`, not plain
  `CommonMarkConverter` — the latter has no table support at all, pipes rendered as literal text.
- **Raw `<style>` blocks getting escaped to visible text** turned out to be a *GFM-specific*
  regression, not a config default: GFM bundles `DisallowedRawHtmlExtension`, which neuters
  `<style>`/`<script>`/`<iframe>`/`<title>`/`<textarea>`/`<xmp>`/`<noembed>`/`<noframes>`/
  `<plaintext>` by escaping their angle brackets — a built-in XSS guard aimed at untrusted content,
  but this repo's content is author-written and git-committed (trusted). Fixed by passing
  `'disallowed_raw_html' => ['disallowed_tags' => [...]]` with `style` removed from the list —
  `script`/`iframe`/etc. stay blocked. `'html_input' => 'allow'` is also set explicitly (matches the
  library default, but pinned so it doesn't silently drift on a future upgrade).
- **`⟦Title⟧` wiki-link syntax (written with two square brackets in the Markdown, shown here with ⟦ ⟧ so memory tooling does not treat it as a memory link)** (used in 05 and 06 to cross-reference other subchapters by title,
  mirroring this project's own `⟦slug⟧` memory-linking convention) is not standard Markdown at
  all — CommonMark's link syntax is `[text](url)` only. Added
  `ContentController::resolveWikiLinks()`, run on the raw Markdown body *before* conversion: matches
  `⟦Title⟧` case-insensitively against every subchapter title in the course manifest and rewrites
  matches to a real `[Title](route)` link; an unresolved reference degrades to plain text (brackets
  stripped) rather than breaking rendering. **Known dangling ref**: `06-masih-ada-yang-kurang` has
  `⟦Solusinya⟧`, which doesn't match any subchapter title exactly (nearest is "Solusi Parsial",
  slug `02-solusi-parsial`) — renders as plain text "Solusinya" for now; fix the source text to
  `⟦Solusi Parsial⟧` next time that file is touched, or ask the user which subchapter it meant.
- **GFM table CSS**: `resources/css/app.css`'s `.content table` block (rounded bordered container,
  horizontal-scroll-on-overflow for mobile, tinted header row, zebra-striped body rows, row hover,
  bold first column) styles *any* pipe table automatically — no per-subchapter `<style>` override
  needed for tables, unlike the per-subchapter readability pattern above.
- Verified via a tinker script converting real chapter-2 Markdown files directly (not just
  synthetic snippets): table renders as `<table>`, `⟦Taaruf⟧` resolves to a working
  `<a href="…/04-taaruf">`, `⟦Solusinya⟧` degrades to plain text, and the `<style>` block in 05
  survives unescaped. No real-browser visual check this pass (no browser tool available this
  session).

## Images

When a subchapter calls for an image, create `{subchapter-slug}/images/` and reference it with a
relative Markdown path (`./images/foo.jpg`) right where it belongs in the prose — resolved by
`ContentController::resolveImageUrls()`. If a suitable public-domain image can be found (e.g. via
Wikimedia Commons — search, confirm PD/CC0 license on the file page, `curl` the direct
`upload.wikimedia.org` URL), download and use it directly as the placeholder rather than leaving a
broken reference; note in conversation that the user can swap it later. Don't resize/optimize
unless asked — first pass is about having *something* correctly wired, not final asset weight.

## Progress tracking

Split across two places, kept in sync on every content-authoring memory update (session-end or
whenever asked to "update memory" mid-session):
- **`todo-list.md`**, dedicated "Content authoring progress" section at the top of the file —
  done/not-done checkboxes only, one per subchapter, grouped by chapter. No narrative.
- **[[content-authoring-progress]]** — the narrative behind each chapter's status: why it's
  blocked, what source material it drew from, style/image decisions made while drafting it. Update
  this whenever a chapter's status changes or something chapter-specific worth remembering happens
  (a style choice, a found source, a user substitution like swapping a placeholder image).

This file (content-authoring-workflow) stays for rules that don't change per-chapter.

## Change log
- 2026-09-11 07:37 — New rule: **inserting a subchapter mid-book** (not appending at the end) means
  renumbering every downstream folder's numeric prefix by +1 across all chapters, to preserve the
  whole-book sequential-numbering convention established since ch.1 (`01-`…`16-` — the file prefix
  has always run continuously across chapter boundaries, not restarting per chapter). Only update
  the `order` frontmatter field on the one subchapter whose in-chapter position actually changed —
  `order` is scoped per-chapter (position within its own chapter's `subchapters:` list in
  manifest.yaml), so a subchapter's slug-prefix can shift without its `order` value changing.
- 2026-09-10 22:07 — Updated Verification: `php artisan tinker <file>` stopped executing (echoes
  source without running it); switched to a standalone script that boots Laravel directly
  (`require bootstrap/app.php`) run with plain `php`, used to verify all 8 of chapter 3's
  subchapters (title, html length, ASCII-diagram `<pre>` block, and wiki-link hrefs).
- 2026-09-10 04:08 — Created after drafting the first two real subchapters (2-masalah-halusinasi-dan-memori, solusinya).
- 2026-09-10 04:23 — Added approved readability styling pattern (Atkinson Hyperlegible, 600px column, one-sentence-per-paragraph).
- 2026-09-10 04:29 — Backfilled the style block onto `solusinya/index.md` (it had been drafted before the pattern was approved, so it was missing it); both of chapter 1's subchapters are now consistent.
- 2026-09-10 19:19 — Added the Images section (Wikimedia Commons PD-search-and-download pattern), after drafting subchapter 3 (`03-hilang-memori-tak-berarti`) with a downloaded placeholder image.
- 2026-09-10 19:40 — Split Progress tracking into checklist ([[todo-list]] top section, unchanged) + narrative ([[content-authoring-progress]], new), per the user's explicit request for a dedicated progress memory kept in sync on every content-authoring memory update.
- 2026-09-10 20:26 — Updated the Verification snippet: `ContentController::show()` now requires a
  resolved `Course $course` model (signature changed since this memory was written), and noted the
  tinker-stdin-vs-file-arg gotcha found while re-verifying chapter 2's renumbered subchapters.
- 2026-09-10 21:27 — Added the Markdown rendering pipeline section: switched to
  `GithubFlavoredMarkdownConverter` for table support, fixed GFM's `DisallowedRawHtmlExtension`
  silently escaping `<style>` blocks (whitelisted `style` while keeping `script`/`iframe`/etc.
  blocked), added `⟦Title⟧` wiki-link resolution (`ContentController::resolveWikiLinks()`), and
  designed table CSS in `app.css`. Corrected the now-stale claim in the Readability styling section
  above that credited plain `CommonMarkConverter`/library-default `html_input` for `<style>` passthrough.
