---
name: artipdf-landing-facts
description: Facts and caveats about the ARTi PDF desktop GUI app (C:\python_tools\artipdf) for adding it to the landing's free section; release state, real features, claims to avoid
metadata:
  type: reference
  created: 2026-10-15 07:49
  updated: 2026-10-15 08:20
---

Studied 2026-10-15 for the "Kenalan dulu, tanpa membayar" section ([[landing-page-concept]], [[landing-page-info]]). **ARTi PDF** is a standalone offline PDF GUI (Python, tkinter, PyInstaller), separate from the internal `~/.arti/tools/arti-pdf` (Markdown to A4 PDF, used by skills). Source of truth: `C:\python_tools\artipdf` (its `CLAUDE.md` and `readme.md`).

## Release state
- Public repo `https://github.com/okik4zuya/artipdf`, **one release v1.0.6 (2026-09-18)**: `ArtiPDFSetup-1.0.6.exe` (76.8 MB) and `ArtiPDF.dmg` (59.7 MB). 0 downloads at check time. Stable link: `https://github.com/okik4zuya/artipdf/releases/latest`. No `gh` CLI here; the GitHub REST API via curl works.
- Local source is **v1.0.7**, not released, with uncommitted changes (Compress right-click exe `ArtiPDFCompress.exe`, `app/compress_core.py`). Local `dist/` has 1.0.7 installer (44.6 MB; differs from the 76.8 MB release build).
- **License decided 2026-10-15: MIT** (researcher's choice), prepared in the artipdf repo but **uncommitted and not pushed**: `LICENSE` (same copyright line as SciPeek), `THIRD-PARTY-NOTICES.md` (Ghostscript 10.07.0 AGPL, Poppler 25.07.0 GPL, Tesseract 5.5.0 Apache, Python deps) and a readme License section. Until that is pushed, the public repo still has no LICENSE and the GitHub license field is empty. Not legal advice: the AGPL/GPL components ship as separate programs called via subprocess, with a source offer in the notices. The installer has no licence page (`installer.nsi` untouched). Windows installer and macOS dmg are **unsigned** (SmartScreen warning on Windows; right-click Open on macOS).
- **Screenshots prepared 2026-10-15** (uncommitted): `C:/python_tools/artipdf/assets/screenshots/artipdf-editor.png`, `artipdf-merge.png`, `artipdf-convert.png` (778x688, real app from source, v1.0.7, sample PDFs are generated placeholders, no real paths in the logs) plus a readme Screenshots table. Not used: the About tab shot (it says "up to 80%"). Capture recipe: scratchpad scripts drive `app.main.App` with `after()` + `mainloop()` (thumbnails need the real mainloop) and grab the DWM frame with PIL `ImageGrab`. A leftover demo folder `C:/arti-demo` (sample PDFs, outputs) still needs deleting by the researcher.

## Real features (from the code)
Tabs: PDF to MD, MD to PDF, Compress, Merge, Split, Page Editor, About. Output is saved next to the source PDF. Installed build adds Explorer right-click "Convert to Markdown" and "Compress PDF". Readme feature table and `CHANGELOG.md` are stale (no MD to PDF; changelog stops at 1.0.3 and still says UglyPDF).

## Claims to avoid or soften
- PDF to MD is plain text extraction (heading "Extracted PDF Content"), OCR fallback with **English-only** `eng.traineddata`. Sample output from scientific papers has margin watermark text as one character per line and `(cid:1)` for symbols. Readme itself says tables are not preserved. Do **not** claim table or equation fidelity.
- The About tab says "Reduce PDF file size up to 80%"; readme says results vary. Do not put a percentage on the landing.
- MD to PDF needs Edge or Chrome installed (headless print).
- Name clash risk: `/fitur` lists the internal tool `arti-pdf` ("Markdown jadi PDF A4"), which readers may confuse with this app.

## Change log
- 2026-10-15 08:20 — MIT licence files and three screenshots prepared in the artipdf repo (uncommitted).
- 2026-10-15 07:49 — created from a study of the repo, GitHub releases API and sample outputs.
