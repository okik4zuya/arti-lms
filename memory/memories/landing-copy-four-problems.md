---
name: landing-copy-four-problems
description: The 2026-10-19 landing copy pass — four problems agitated in order and answered in the same order, value summary, Claude subscription block, reptilian-brain analysis and the decisions behind it; what is still open
metadata:
  type: project
  created: 2026-10-19 08:15
  updated: 2026-10-19 08:15
---

Session 2026-10-19, brainstormed with the researcher then implemented in `resources/views/site/home.blade.php` (the Blade file stays the source of truth; [[landing-page-copy]] was not rewritten). Builds on [[landing-page-concept]] (rules for honest marketing) and [[landing-checkout-form]]. All uncommitted. Hero audience assumption confirmed: the **disappointed AI user** (not the dosen who has never heard of Claude Code).

## Structure now (section 2 to 4 and additions)
- **Masalah** (unchanged): four one-sentence pains, bold phrases kept (tests pin `<strong>gambarnya terlalu AI</strong>, jadi ragu memasukkan ke naskah.`).
- **Agitasi** `#soal`, h2 "Empat soal yang terus berulang.", four `article.soal` in the same order as the pains: **Soal kredibilitas** (error chain, ends "Yang dipertaruhkan adalah nama Anda di baris penulis."), **Soal tenaga** (micro-scene "Pukul sebelas malam…" plus Rithah and the image), **Soal gambar**, **Soal output** (the researcher chose "output" over "naskah"). Closing aside: "Bukan berarti AI-nya buruk. Alatnya hebat, tapi belum jadi sistem."
- **Solusi**: the model + skill + tool story kept, then "Empat soal, empat jawaban": four `.panel.answer` cards with the same tags, each "Dulu / Dengan ARTi / Yang kembali ke Anda". The old three foundation panels and the two loose blocks (figures, Word + Zotero) were folded into the cards. Strings pinned by tests were kept ("Gambar dari data, bukan dari imajinasi AI", "ARTi mengeluarkan berkas Word yang sudah berisi field Zotero.", "Butuh Zotero di Word.", "Bukan untuk foto atau ilustrasi bebas.").
- **Hero**: lead "dari ide sampai publish" (the researcher's choice, because ARTi also covers the reviewer rebuttal); new line `.hero__nots` with four "Tanpa…" phrases; the h1 stays.
- **Value summary** `#manfaat` "Yang kembali ke Anda." (cards: Waktu, Tenaga, Kualitas, Bayar sekali; no numbers on purpose, the researcher may add measured ones later) plus "Pengalaman saya: tiga paper saya tulis paralel dengan ARTi, sambil menyiapkan topik riset lain." (the researcher's own statement, labelled as such).
- **Soal langganan Claude** `#langganan`: about US$20, roughly Rp400.000 a month (reader told to check Claude's site), "sekitar Rp13.000 per hari", a month can hold naskah, proposal, reviewer reply, teaching material and other work with the explicit line "Ini bukan janji hasil", and "Anda tidak harus berlangganan terus" (ARTi is one-time, the Claude subscription can be used in the months you work, then stopped; the researcher vouches for it), plus an honest line that it is not worth it for occasional AI use. The order before the price: Syarat, Yang kembali ke Anda, Soal langganan Claude, Penawaran.
- **Comparison table** (Claude Code polos vs ARTi) got rows "Gambar" and "Naskah Word dengan sitasi Zotero" with "Tergantung Anda" on the plain side; the second table (Claude AI, NotebookLM, others) was not touched, its Claude AI claims still need checking.
- **FAQ** gained "sitasi ke gaya jurnal lain?" and "gambarnya boleh dipakai di jurnal?"; "Berapa total biayanya?" links to `#langganan`. **Closing** repeats "Referensi yang bisa dilacak. Sesi yang melanjutkan. Gambar dari data. Sitasi yang bisa disegarkan." before the tagline (positive wording, no "tidak ada lagi").
- Figure claim decision: the researcher said figures come from Python libraries, not image generation, so no publisher-policy research was needed; the copy says "dibuat dengan kode dari data".
- CSS in `site.css`: `.hero__nots`, `.closing__lines`, `.soal`, `.soal__tag`, `.answer` (grid label column, single column under 520 px), `.answer__gain`.

## Reptilian-brain notes kept for later rounds (Renvoisé and Morin six stimuli)
Biggest gains in order: solution pairs (done), hero (done, but no visual above the fold yet), real demo (the "Segera" badge still stands in for it, and the demo should move nearer the solution), closing (done), micro-scenes (done). Lower: "Cara kerja" is abstract (one concrete output per stage would help), tabs and FAQ. Still open: an image of a Word file with Zotero fields (show before building), the real 1 to 2 minute demo, a hero visual.

## Not done
- `/fitur`, email series 1 to 4, `landing-page-copy` mirror, OG image text, the second comparison table. The h2 "Anda pasti pernah mengalaminya." casual replacement is still undecided.
- Seen only in headless Chrome at 700 px; no 375 px or real phone check.
- Tests: LandingPageTest (23, five new), PublicSiteTest, PublicPageQualityTest, FeaturePageTest, EmailSeriesTest green; the full suite was not run.

## Change log
- 2026-10-19 08:15 — created.
