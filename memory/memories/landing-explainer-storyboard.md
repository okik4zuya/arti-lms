---
name: landing-explainer-storyboard
description: The 40 s animated explainer of the ARTi landing page (7-scene storyboard, build files, render settings), approved, rendered and installed on the landing page
metadata:
  type: project
  created: 2026-10-14 23:50
  updated: 2026-10-14 23:58
---

Concept video, labelled "ilustrasi konsep" on screen; it does not replace the real demo recording. Part of [[landing-page-concept]]. Every on-screen claim matches a real feature; the "Smith et al." citation and the scratchbook lines are invented and labelled "ilustrasi" / "contoh format".

| Time | Scene | On screen (Indonesian caption) |
|---|---|---|
| 0-4 s | Hook | "AI-nya ngawur dan lupa terus?", logo in the corner |
| 4-9 s | Ngawur | Chat bubble with a fake citation, "?" then a red cross; "Kutipan terlihat meyakinkan. Tapi ada di jurnalnya?" |
| 9-14 s | Lupa | Session 1 with files closes, session 2 empty; "Sesi baru, mulai dari nol." |
| 14-20 s | Solusi | `memory/` tree (MEMORY.md, todo-list.md, status.md, memories/) drawn line by line; "ARTi menyimpannya sebagai berkas." |
| 20-26 s | Bertanda sumber | Scratchbook lines with `[OWN]`, `[LIT: key]`, `[SOURCE NEEDED]`; "Setiap klaim punya penanda asal." |
| 26-34 s | Alur | The staircase redrawn as inline SVG (path draws, labels fade in, blue dot lands on "Submit"); "Amati. Replikasi. Tambah Inovasi." |
| 34-40 s | Penutup | Wordmark, "Dari ide sampai publish, bersama ARTi.", arti.invait.id |

## Decisions (answered 2026-10-14)
- Indonesian captions, generated upbeat music (124 bpm, C-G-Am-F), no 1080x1080 ad cut.
- Placed in the landing "Lihat caranya bekerja" section above the real-demo slot, not as a hero loop.

## Build and render
- Sources: `resources/media-src/explainer/explainer.html` (timeline, Web Animations API, 22 sound cues) and `storyboard.html` (visual sheet). Outputs in `out/`, music in `music.wav`, previews in `prev/`; all three are gitignored.
- Render: `node ~/.claude/skills/html2media/scripts/render.mjs --html explainer.html --cues-only`, then `~/.arti/python/python.exe -I .../music.py --cues cues.json --out music.wav` (about 60 s), then `render.mjs --html explainer.html --music music.wav --out out/arti-explainer.mp4`.
- Result: 1920x1080, 30 fps, 40.0 s, H.264 + AAC, 2.6 MB; audio mean -15.6 dB, peak -1.9 dB. Copied to `public/videos/landing/arti-explainer.mp4` with a 12 KB WebP poster.
- Not verified: the audio was never heard (only levels), and the full video was not watched, only 8 preview frames.

## Change log
- 2026-10-14 23:58 — approved, rendered and installed; questions answered; build notes added.
