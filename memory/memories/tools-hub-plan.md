---
name: tools-hub-plan
description: Detailed implementation plan for the free tools hub at /tools in arti-lms (hub, composite, diagram, plot tool pages, 4 Gem instruction texts); decisions, file map, specs, phases with acceptance checks, tests, risks
metadata:
  type: project
  created: 2026-10-24 17:58
  updated: 2026-10-24 18:30
---

Source plan (strategy, rationale, extension): `G:\My Drive\hq\memory\memories\free-tools-gem-plan.md`. This file is the **build plan inside arti-lms**; it does not repeat the strategy. The browser extension (E0-E3) is out of scope here, but the contract markers below are chosen now so the extension can reuse them.

## Answers given before planning (2026-10-24)
1. Scope = hub + 3 tool pages (composite, diagram, plot) + 4 Gem instruction texts (diagram, plot, composite, journal-profile).
2. Gems: the researcher creates them in Gemini and supplies share URLs; Claude writes the instruction and knowledge texts. No URL yet = the Gem button is hidden, the copyable instruction block always shows.
3. Navigation: "Tools" in the main menu (desktop + phone) with the `Free` badge, in the sitemap, and a card in `#coba-gratis`.

## Decisions taken here (the source plan left them open)
- **Hosting = routes in arti-lms** (resolves the source plan's open question). JS is bundled by Vite from npm packages with exact pinned versions, one entry per tool page, heavy libraries (Vega) load only on their own page. No CDN: `PublicPageQualityTest` bans leaving for third-party hosts and the data must stay in the browser.
- **Diagram auto-layout is final for the free path** (dagre). ARTi's hand-coordinate rule stays for the paid path.
- **Composite source = the same HTML format as `arti-compose`** (`<meta name="arti:width">`, `arti:height`, `arti:mode`, `arti:min-pt`, `arti:min-px`). A file made by a Gem also renders in the paid tool later.
- **Gem-generated HTML never runs scripts.** Preview iframe uses `sandbox="allow-same-origin"` (no `allow-scripts`), the parent page measures and lints the iframe DOM. Anything with `<script>`, event-handler attributes, or a non-`data:` URL in `src`, `href`, `url()`, `@import` is a FAIL.
- **Fix loop is the product.** One shared error format and stable codes for all tools (below).
- **Trial CTA**: not the licence trial. The Worker's trial path is on the removal list (todo "Remove or disable the unused trial path"), so the funnel uses the existing double-opt-in sign-up (`components/subscribe-form`) plus "Baca bab 1 gratis". Revisit if the trial stays.
- **Plot categorical palette = Okabe-Ito** (colour-blind safe). `figure-style.md` has no categorical palette (only `dark`/`plain` boxes), so this is a new decision, written into the Gem knowledge file and the Vega theme. Everything else in the theme follows `figure-style`: Arial/Helvetica, 7 pt (24 px at 1328 px) floor, strokeWidth 2 for diagram edges.
- **Not ported from `compose.py`**: the pdfminer checks (page count, embedded fonts, Type 3). The PDF comes from the browser's print dialog; the tool says so and replaces "page count" with a content-bounds check. Ported: overflow, clipped SVG text, outside-page text, broken images (`LINT_JS`, lines 95-141, runs as is), text size at final width (computed from `getScreenCTM` instead of from the PDF), raster dpi for `data:` images.

## Public contract (shared with the future extension)
| Tool | Recognised by | Output file |
|---|---|---|
| Diagram | JSON with `"arti": "diagram/1"` | `.drawio`, SVG, PNG |
| Plot | JSON with `$schema` containing `vega-lite` | SVG, PNG |
| Composite | HTML starting `<!doctype html>` with `arti:width` meta, or a bare `<svg` | PNG, PDF (print) |

**Diagram spec v1** (no coordinates):
```json
{ "arti": "diagram/1", "direction": "TB",
  "nodes": [ { "id": "a", "label": "Studi dipilih", "style": "plain" } ],
  "edges": [ { "from": "a", "to": "b", "label": "ya" } ] }
```
`direction` TB|LR; `style` dark|plain|soft (`dark`/`plain` from `figure-style-default`, `soft` = `#F2F2F2` fill, `#4D4D4D` stroke, new); ids `[a-z0-9_]+`; label ≤ 120 chars; ≤ 60 nodes; edges must reference existing nodes. Groups, icons, curved edges are out (composite lane covers them).

**Error format** (all tools, "Salin kesalahan" button, plain text, Indonesian):
```
Perbaiki hasil sebelumnya. Kesalahan dari alat ARTi (diagram/1):
1. [E_EDGE_UNKNOWN_NODE] nodes: edges[3].to = "x9" tidak ada di nodes.
2. [E_LABEL_LONG] nodes[7].label: 143 karakter, maksimal 120.
Kirim ulang JSON lengkap yang sudah diperbaiki, hanya dalam satu blok kode.
```
Codes are stable strings kept in `resources/js/tools/core/errors.js`; the Gem instruction ends with "if a message starting `Perbaiki hasil sebelumnya` is pasted, fix only the listed items and resend the whole block".

## File map
```
config/tools.php                       cards, order, per-tool: slug, title, lane, gem_url (env), instruction file, knowledge file
resources/gems/{slug}.instruksi.md     the text pasted into the Gem (kept short, see Phase 0 limit check)
resources/gems/{slug}.pengetahuan.md   reference + 2 examples, uploaded as Gem knowledge (also downloadable)
app/Http/Controllers/ToolController.php   index(), show($slug) (404 on unknown slug; reads config + gem files)
resources/views/site/tools/index.blade.php        hub
resources/views/site/tools/show.blade.php         shared shell: steps, Gem block, workbench slot, "Jujur", CTA
resources/views/site/tools/partials/{gem-block,workbench-composite,workbench-diagram,workbench-plot,decision-tree,cta}.blade.php
resources/js/tools/core/{errors,download,workbench,clipboard,measure}.js
resources/js/tools/{composite,diagram,plot}.js    Vite entries (page-specific)
resources/js/tools/lib/{composite-lint,composite-render,diagram-spec,diagram-layout,diagram-drawio,diagram-svg,plot-spec,plot-theme,csv}.js
resources/js/tools/**/*.test.js         `node --test`, pure modules only
resources/css/tools.css                 imported from site.css (no new tokens)
tests/Fixtures/tools/{diagram,plot,composite}/*.json|html   valid + invalid samples, shared by JS and PHP tests
tests/Feature/ToolsPagesTest.php
```
Routes (inside the main-domain group, next to `scipeek`): `Route::get('tools', [ToolController::class,'index'])->name('tools.index')`; `Route::get('tools/{tool}', ...)->whereIn('tool', ['composite','diagram','plot','profil-jurnal'])->name('tools.show')`. `vite.config.js` gets the three entries plus `tools.css` if separate (a past commit, "Fix vite entries after landing page rewrite", was exactly this omission). `SeoController::PAGES` gets `tools.index`; the four tool URLs are appended in `sitemap()` because they need a parameter.

**Kill switch**: `SITE_TOOLS_ENABLED` (config/site.php, same pattern as `SITE_CHECKOUT_ENABLED`). Off = pages answer but carry `noindex`, and the menu item, sitemap entries and `#coba-gratis` card are hidden. Lets each phase be deployed dark.

New dependencies (exact versions chosen at install by `npm view`, recorded in this file): `vega`, `vega-lite`, `vega-embed`, `@dagrejs/dagre`, `html-to-image`, `ajv`. Dev: nothing (Node's built-in test runner). `composer` unchanged.

## Phase 0, verify (half a day, before any UI)
- **S1 composite capture spike**: a throwaway page with a sandboxed no-script iframe; confirm (a) parent can read `contentDocument` and run the ported lint, (b) `html-to-image` on the iframe body gives a correct PNG at 1328x531 and at 190 mm x 600 dpi (4488 px wide) with inline SVG text and Arial, (c) print of the iframe to PDF works without `allow-modals` or needs another route (open a Blob URL in a new tab with the injected `@page`). Decide the PDF route here.
- **S2 Vega size**: `npm i` the plot packages, build one entry, record chunk size; confirm lazy load keeps hub and other tool pages free of it. Decide `vega-interpreter` only if a CSP is ever added (none exists today).
- **S3 diagram text measure**: canvas `measureText` with Arial at 20 px, wrap at a fixed max width (draw.io wraps itself; the preview only approximates). Read `~/.arti/tools/arti-render/render.py` and copy its `.drawio` cell style strings so the JS export opens identically.
- **S4 Gem limits (researcher, 5 min)**: in the Gem editor paste a 6,000-character instruction; note accepted/rejected, and the number of knowledge files allowed. Sources disagree (one says ~4,000 characters, 10 files; nothing official found). Result decides how much goes in `.instruksi.md` versus `.pengetahuan.md`. Also test whether a free account gets "Share" on a Gem and whether a second Google account opens the link.
- **S5 keyword check**: `yt-keyword-audit` `suggest --lang id` for "buat diagram penelitian gratis", "grafik penelitian dengan AI", "graphical abstract gratis", "buat flowchart dengan AI"; use the demand words in hub titles. Record in `landing-page-keywords.md`.
- Prerequisite hygiene: commit the uncommitted Mayar work first (`routes/web.php` is modified there), so the tools diff is clean.

**Exit:** S1-S3 answered in this file; S4 numbers known; versions pinned.

## Phase 1, scaffold and hub shell
Build: `config/tools.php`, `ToolController`, routes, `show.blade.php` shell (steps, Gem block, workbench slot, "Jujur", CTA), `index.blade.php` with the four cards (tool pages say "Segera" in the workbench slot until built), `core/*` modules, nav link + badge, `SITE_TOOLS_ENABLED`, sitemap, placeholder Gem files.
**Gem block behaviour:** instruction in a `<textarea readonly>` with a "Salin instruksi" button and a character counter; "Unduh berkas pengetahuan"; "Buka Gem di Gemini" only if `gem_url` is set; always a note "Tidak punya Gem? Tempel instruksi ini ke Gemini, ChatGPT, atau Claude." (so the free path never depends on Google's sharing rules).
**Accept:** `/tools` and 4 tool URLs return 200 with one h1 and unique title/description; copy button works; Gem button hidden when `GEM_*_URL` empty; flag off = noindex and no menu item.

## Phase 2, composite tool (`/tools/composite`)
- Input: `<textarea>` (paste) or "Muat contoh" select: multi-panel 190 mm, graphical abstract 1328x531 (from `~/.arti/skills/ARTi-figure/templates/*` and `arti-compose/examples/*`, minus anything using `<img>`).
- Pipeline: parse `arti:*` metas (port `parse_meta`/`parse_len`/`resolve_spec`, same defaults: dpi 600, min-pt 7, min-px 24; mode pixel when width is in px) → static checks (no script, no external URL, no handlers) → render in sandboxed iframe at the stated size → lint → report list (FAIL/WARN/OK lines like `compose.py`).
- Downloads: PNG (exact px, or mm x dpi), PDF (route from S1), "Unduh sumber .html". Size guard: refuse canvas > 16,000 px side with a message.
- Graphical-abstract rule enforced softly: WARN when text nodes outside `<title>`-like roles exceed ~8 words ("ringkasan ini terlihat seperti kalimat penjelas; gambar harus bercerita sendiri").
- Gem: composite instruction forbids `<img>`, scripts, web fonts, external URLs; inline SVG only; Arial; sets the meta tags; palette and 7 pt / 24 px floor; no explanatory text in graphical abstracts; knowledge file = two templates.
**Accept:** both templates render with zero FAIL; each bad fixture (overflowing text, clipped SVG text, `<script>`, remote `src`, tiny font) gives its own code and the exact line in the error format; PNG dimensions equal the declared size (checked by reading the file header in a headless run); verified at 375 px and 1100 px.

## Phase 3, diagram tool (`/tools/diagram`)
- Pipeline: JSON parse (with line/column message) → `ajv` validate against the diagram/1 schema (schema kept in `resources/js/tools/lib/diagram-spec.js` and copied as text into the Gem knowledge file) → semantic checks (unknown node, duplicate id, cycle allowed, orphan WARN, > 60 nodes) → size nodes by measured text → dagre layout → SVG preview (also the SVG/PNG export) → `.drawio` XML (mxGraphModel; orthogonal edges; text wrapping left to draw.io; node style strings copied from `render.py`).
- Downloads: `.drawio` (primary, label it "bisa diedit di draw.io"), SVG, PNG (canvas, 2x).
- Preview note: "Pembungkusan teks di draw.io bisa sedikit berbeda dari pratinjau."
- Gem: diagram instruction with the spec, style names, label length, direction choice, "hanya satu blok kode JSON", fix-loop handling; knowledge file = schema text + 3 examples (study design, PRISMA-like flow, method pipeline).
**Accept:** fixtures `tests/Fixtures/tools/diagram/*.json` produce stable layouts (JS test asserts no node overlap and all edges attached); `.drawio` opens in draw.io desktop (researcher check, once) with same box order; invalid fixtures give each error code; long-label and 60-node cases render without browser freeze (< 1 s).

## Phase 4, plot tool (`/tools/plot`)
- Input: Vega-Lite JSON. Checks: `$schema` present and Vega-Lite (rewrite older/newer v5 `$schema` to the bundled version before compile, WARN once); inline `data.values` only (`data.url` = FAIL "pakai data.values; Gem tidak boleh membuat tautan data"); `vega-lite` compile errors and `vega` runtime warnings captured into the error format; font-size floor WARN (< 7 pt at the chosen export width); max rows 5,000 WARN.
- House theme `plot-theme.js` merged under the user's `config` (user keys win): Arial, axis and title sizes meeting the floor, Okabe-Ito range, no background fill, thin grid, legend at bottom.
- Export: SVG (`view.toSVG()`), PNG at presets (85 mm / 170 mm wide at 300 dpi, or custom px) via `view.toCanvas(scale)`.
- Optional helper (last, cut if late): paste CSV/TSV → shows the `data.values` JSON snippet to paste into the chat so users do not type data by hand (`csv.js`, no library).
- Gem: plot instruction (chart choice by data type, axis titles with units, no 3D, no dual-axis unless asked, sorted bars, `data.values` only, ≤ 200 rows inline, title in caption not figure unless asked); knowledge file = 4 examples (bar by year, grouped bar, line with two series, scatter with regression note).
**Accept:** 4 fixture charts render; an old-`$schema` fixture renders with a WARN; `data.url` fixture is rejected; PNG at "170 mm / 300 dpi" is 2008 px wide; Vega chunk is requested only on `/tools/plot` (checked in the built manifest).

## Phase 5, journal-profile Gem and page (`/tools/profil-jurnal`)
- No workbench. Page = same shell: Gem block + a "Cara pakai" with the three inputs the user pastes (aims and scope, author guidelines, 2-3 example papers' abstracts/intro) + a worked example output + "Jujur: belum diverifikasi".
- First step: read `ARTi-writing` Stage 1 and Journal Profile blocks A, C, D, then decide what the Gem can do from pasted text only. Output marked per field `[dari teks yang Anda tempel]` or `[belum diverifikasi]`; novelty C/M/E scoring only when example papers are pasted, otherwise "tidak dihitung". Refuses to invent dpi/column widths (same rule as `figure-style`: flag "belum dikonfirmasi").
**Accept:** the Gem text exists, the page renders, the worked example is a real output the researcher tried in a Gem (not invented).

## Phase 6, hub finalisation and release
- Hub (`index.blade.php`) sections: hero ("Buat gambar riset dari hasil AI, gratis, tanpa unggah") with trust line `Gratis · Tanpa akun · Berkas tidak diunggah`; "Cara kerjanya" in 3 steps (Gem/AI → kode → gambar); decision tree (partial, 4 questions → right card); 4 cards with lane badge and "Buka"; "Jujur" block (hasil lebih sederhana dari ARTi: tanpa agen, tanpa verifikasi, tanpa data proyek; output gratis sering perlu diperbaiki, itulah gunanya tombol Salin kesalahan); privacy (client-only, what the server sees: the page request only); FAQ (bisa dengan ChatGPT/Claude? tanpa Gem? kenapa JSON/HTML? format apa yang bisa diedit?); soft ARTi bridge + sign-up form + bab 1 gratis. Page rules as in [[landing-scipeek-artipdf]]: 600px column, "saya/Anda", "Jujur:" passages.
- `#coba-gratis` card, `site-links` item (`Tools` + `nav-badge` Free), `/privasi` one sentence ("alat gambar berjalan di browser, isi tidak dikirim ke server"), sitemap, `PublicPageQualityTest::PAGES` + the four URLs.
- Release steps (researcher on the VPS as `deploy`): `git pull`, `npm ci`, `npm run build`, `config:cache`; set `SITE_TOOLS_ENABLED=true` and `GEM_*_URL`; `php artisan test` locally first.

## Tests (what proves it)
- **PHP** `ToolsPagesTest`: 200 on hub and 4 tools; unknown slug 404; flag off = noindex + no nav item + not in sitemap; flag on = in sitemap; Gem URL absent = no button, present = button with `rel="noopener"`; Gem instruction/knowledge files exist and are non-empty and instruction length ≤ limit from S4; rendered HTML contains no `http` origin other than the site (no CDN/Google); every Vite entry used by a view is listed in `vite.config.js`; one h1 per page.
- **JS** `node --test` on pure modules: `parse_len` parity with Python values (190mm = 718.11 px), spec validators for each fixture (valid passes, each invalid gives its code), dagre layout non-overlap, `.drawio` XML is well formed with matching node/edge counts, theme merge (user keys win), CSV parser edge cases (quotes, comma decimal).
- **Headless Chrome** (recipe in [[learner-app-ux]]): load each tool with each fixture, assert report lines, PNG dimensions, no horizontal overflow at 375 px, console clean.
- **Manual (researcher)**: one real Gem round per tool (idea → Gem → paste → image), open the `.drawio` in draw.io, print-to-PDF of a composite, phone check.
- Full `php artisan test` stays green apart from the known `DomainRoutingTest` failure ([[todo-list]]).

## Definition of done ("siap digunakan")
All four pages live with `SITE_TOOLS_ENABLED=true`; each tool takes its valid fixtures to a downloadable file and each invalid fixture to a pasteable error; 4 Gem texts written and the 3 tool Gems created and linked by the researcher; hub nav, sitemap and `#coba-gratis` in place; tests above green; checked at 375 and 1100 px; memory updated. Validation with about 5 students is a **post-release** step, not a gate.

## Order and rough effort
P0 0.5 day · P1 1 day · P2 2 days · P3 2 days · P4 1.5 days · P5 0.5 day (plus the researcher's Gem trials) · P6 1 day. Composite first because it has the most browser-reuse and the largest uncertainty (S1); diagram before plot because layout/export is riskier than Vega. Each phase can be deployed dark behind the flag.

## Researcher tasks (Claude cannot do these)
S4 limit check; create 4 Gems (after the texts exist) and send share URLs; one real round per Gem; open `.drawio` in draw.io; phone check; the deploy steps; decide whether the trial CTA should come back.

## Risks specific to the build
- Capture fidelity of `html-to-image` for complex SVG (filters, `foreignObject`); S1 decides, fallback is "unduh SVG + cetak PDF" only.
- Printing from a sandboxed iframe may be blocked; fallback Blob tab.
- Gem free-tier output is often invalid; the error format and examples carry the experience, so write them first and test with a weak model.
- JS and Python drift (diagram): shared fixtures; any change to the spec bumps `diagram/2`.
- Vega bundle weight on slow phones: lazy chunk, show a loading state on the workbench only.
- Hub page `content-visibility` / anchor behaviour from `site.css` (`no-cv` class) applies to long sections; keep workbench sections out of `content-visibility`.

## Build record (2026-10-24, uncommitted)
Built P1-P5 and most of P6 in one session; the flag `SITE_TOOLS_ENABLED` is **false by default**, so nothing is public yet.
- **Backend:** `config/tools.php` (cards, `limits`, Gem URLs from `GEM_*_URL`), `ToolController` (hub, tool page, knowledge download `/tools/{tool}/pengetahuan.md`), routes, `SeoController` sitemap entries only when enabled, nav item in `site-links`, `#coba-gratis` card in `home.blade.php`, a sentence in `/privasi`, subscriber source `tools`, `.env.example` block.
- **Views:** `site/tools/{index,show}` plus partials (gem-block, cta, workbench-start/-composite/-diagram/-plot). CSS in `resources/css/tools.css` (imported by `site.css`).
- **JS** (`resources/js/tools/`): `core/` (errors with the paste-back format, clipboard, download, workbench wiring), `lib/` (composite-spec/-lint/-render, diagram-spec/-layout/-svg, plot-spec/-theme, csv), entries `composite.js`, `diagram.js`, `plot.js`, `gem.js`, `examples/`. Dependencies pinned: vega 6.4.0, vega-lite 6.5.0, @dagrejs/dagre 3.1.2 (ajv, html-to-image, vega-embed were tried and dropped). Plot chunk is 795 kB (274 kB gzip) and loads only on `/tools/plot`.
- **Gem texts** in `resources/gems/`: 4 instructions (2.5-3.4k characters, all under 4,000) and 4 knowledge files (plot and composite ones were generated from the examples).
- **Tests:** `tests/Feature/ToolsPagesTest.php` (12), pages added to `PublicPageQualityTest`, `npm run test:tools` = `node --test resources/js/tools/tools.test.js` (21, Node's runner, no extra dependency). Full PHP suite: 478 pass, 4 fail, none in tools files (DomainRoutingTest is the known one; FilamentPricingTest and two RefundAndReconcileTest cases fail on gateway/order state and were not investigated, likely the uncommitted Mayar work).
- **Checked in headless Chrome** (CDP script over Node 22 WebSocket, `public/hot` moved aside): composite PNG exact size (4488x2362 for 190x100 mm at 600 dpi, 1328x531 px), both templates pass; bad fixtures give E_SCRIPT, E_EXTERNAL, E_OVERFLOW, E_SVG_TEXT_CLIPPED, W_TEXT_SMALL; diagram examples lay out and export .drawio/SVG/PNG; plot examples render, SVG/PNG (170 mm = 2008 px), E_DATA_URL/E_COMPILE/E_JSON; no horizontal overflow at 375 px on hub, tools and journal page; Gem copy button works.
- **Bug found by the browser check:** the lint measured a hidden iframe (preview container `hidden`), so overflow checks passed vacuously; fixed by unhiding the preview before mounting. Remember when adding checks: measure only visible DOM.
- **S1 answered:** iframe `sandbox="allow-same-origin"` without scripts lets the parent lint it; PNG via SVG `foreignObject` serialisation of the document works in Chrome (no html-to-image needed). PDF = new tab from a Blob with an injected `print()`; **not tested** (only the code path). Safari PNG untested.
- **Not done / open:** the PDF print tab and Safari/Firefox; `.drawio` opened in draw.io desktop; real Gem rounds (Gem editor limits S4 still unchecked); the journal-profile page has no worked example output yet; hub decision tree is a plain list, not the 4-question tree; keyword run (S5) not done; the screenshot showed blank space under the workbench section, probably `content-visibility` in a captureBeyondViewport shot, not checked in a real scroll.

## Change log
- 2026-10-24 18:30: build record added (P1-P5 built, hub finished except the items listed under "Not done / open").
- 2026-10-24 17:58: created from the planning session (source plan read, `compose.py` and `render.py` inspected, Gemini facts searched: free-account Gems confirmed, sharing and limits unverified).
