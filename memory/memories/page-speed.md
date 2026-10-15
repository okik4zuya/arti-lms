---
name: page-speed
description: Page-speed analysis and optimisations of the public site (landing, /fitur), with the measuring recipe, pitfalls, results and what is still unverified or open
metadata:
  type: project
  created: 2026-10-15 08:32
  updated: 2026-10-15 08:32
---

Measured and optimised 2026-10-15 for the landing and `/fitur` ([[landing-page-concept]]). Test: `tests/Feature/PerformanceTest.php`.

## Result (Chrome, mobile 375px, slow 4G 1.6 Mbps / 150 ms RTT, local `artisan serve`, no gzip)
| | before | after |
|---|---|---|
| Landing load event (CPU 4x slower) | 2.9 s | ~0.9 s |
| Landing FCP/LCP (CPU 4x slower) | ~2.35 s | ~1.05 s |
| Landing FCP/LCP (CPU 1x) | n/a | ~0.85 s |
| `/fitur` FCP (CPU 4x) | ~2.0 s | ~1.2 s |
| Initial requests (landing) | 15 | 7 |
| Landing transfer (uncompressed) | 138 KB | 97 KB |
CLS 0 throughout. These are emulated numbers, not a real device.

## What was done
- **Figtree self-hosted** (`public/fonts/figtree/`, 3 weights + `OFL.txt`, `@font-face` in `resources/css/tokens.css`, preload of 400 and 700 on the public layout). Removed the `fonts.bunny.net` preconnect and stylesheet from all four layouts. Bonus: the privacy page says no trackers, and a third-party font host sees every visitor's IP.
- Header logo `images/wordmark-site.webp` (146x64, 3 KB, with width/height) replaces the 584x256 PNG (20 KB) on the public site; `favicon.ico` now 16/32/48 (2.7 KB, was one 256px frame 14 KB). The LMS layouts still use `wordmark.png`.
- **`content-visibility: auto`** on `.landing-section`/`.landing-closing` (`site.css`): the biggest first-paint win (about 2.0 s to 1.05 s at CPU 4x). Skipped sections only have an estimated height, which broke every anchor jump, so an inline script in `layouts/site.blade.php` adds `.no-cv` to `<html>` when the URL has a `#hash` or an anchor link is clicked. Verified 25 of 26 anchor cases land 64 px from the top (load with hash and in-page clicks, mobile and desktop); the 26th was a smooth-scroll timing artefact.
- `/fitur` animation: `preload="none"` and starts only when within 200 px of the viewport (and never for reduced motion).
- `public/.htaccess`: deflate for text types and `Cache-Control` (1 year immutable for `/build` and `/fonts`, 1 week for `/images` and `/videos`).
- Removed the unused `model-saja` images; the test fails if an unused landing image is left in `public/images/landing`.

## Pitfalls for measuring
- **`public/hot` makes `@vite` load from the dev server** (`localhost:5600`), so numbers are meaningless. Measure with the file renamed (`hot.perf-off`) and **restore it in a `finally`**; a timed-out command once left it renamed. The researcher runs the Vite dev server.
- Headless first-frame cost at CPU 4x is partly an emulation artefact; a bisect showed a full relayout of the page takes only ~4 ms at normal speed.
- Scripts used: puppeteer from `~/.cache/html2media` (CDP network and CPU throttling, `PerformanceObserver` for LCP, layout-shift, long-animation-frame).

## Not verified / open
- **Production server unknown.** Local Apache (Laragon) has neither `mod_deflate` nor `mod_headers`, so compression and cache headers are untested; the `.htaccess` blocks are harmless without them. On nginx the same rules must be written in its config. Confirm gzip/brotli and the headers on the real host.
- Not tested on a real phone. Lighthouse was not run.
- Candidates left alone: `arti-explainer.mp4` is 2.6 MB (only fetched on play; about 1 MB of it is 192 kbps audio, re-encoding at 96 kbps would save roughly half of that); on load with a `#hash` the page smooth-scrolls across the whole page (could be instant); LMS layouts still use the large wordmark PNG.

## Change log
- 2026-10-15 08:32 — created after the first analysis and optimisation pass.
