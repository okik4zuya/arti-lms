---
name: landing-page-keywords
description: YouTube keyword findings (2026-10-14) on how Indonesian dosen/students word AI-for-research pain points; which pain words have demand, which do not, and the copy implications for the landing page
metadata:
  type: reference
  created: 2026-10-14 22:16
  updated: 2026-10-19 08:36
---

Method: `yt-keyword-audit` (`suggest` ~47 seeds, `videos` 8 queries, `--lang id --region ID`, about 800 API units). Autocomplete = relative popularity only, not volume; an empty seed means rarely searched on YouTube, not "no pain". Video view counts are cumulative and sampled from the top results. The project CLAUDE.md has no `## YouTube research` block yet, so settings were session-only. Companion data for the channel: `G:\My Drive\YT Dosen Berarti\memory\memories\keyword-research.md`. Feeds [[landing-page-concept]].

## Main finding: the pain exists, the pain vocabulary does not
- **Empty autocomplete:** "halusinasi ai jurnal", "referensi palsu", "sitasi palsu ai", "chatgpt referensi palsu", "chatgpt lupa konteks", "chatgpt tidak ingat", "cara mengatasi halusinasi ai", "bahaya ai untuk penelitian", "kelemahan chatgpt penelitian", "chatgpt bohong", "kesulitan/malas menulis jurnal", "dosen sibuk menulis jurnal", "cara membalas reviewer", "tidak punya ide penelitian", "claude code untuk penelitian", "claude ai penelitian/jurnal", "notebooklm penelitian".
- **Videos on hallucination/fake citations are small:** best "gampang yaa ngubah referensi fake jadi asli" 13k (Short), Ahmad Fauzi "Kutipan & Referensi Asli" 2.9k, everything else under 300. Memory/context videos: "Jangan curhat ke ChatGPT" 17k, "Kenapa AI bisa lupa percakapan" 328, "How to make Claude remember" 3.8k (EN).
- Meaning: the market has not named these problems. The landing page must **teach** the problem (the ebook's stories do this); it cannot rely on people searching for it. Use "halusinasi/memori" as the explanatory frame, not as search keywords.

## Words people do use (with demand)
- **Colloquial:** "chatgpt ngawur" (autocomplete exists; matches the ebook's own "ngawur"). Positive framing beats negative: "**referensi asli**", "**referensi akurat**", "bukti kecocokan" (Ahmad Fauzi 13.8k and 2.9k; "Cari Sumber Jurnal... Sitasi" 18k).
- **AI + academic work:** "ai untuk tesis" (many variants: membuat, menyusun, proposal), "ai untuk disertasi", "chatgpt untuk skripsi" (+ prompt), "chatgpt untuk menulis artikel jurnal ilmiah", "ai terbaik untuk penelitian".
- **Fear of AI detection (strong, real pain):** "turnitin ai detector" (many variants), "cek plagiarisme ai", "cara menurunkan/menghilangkan plagiarisme ai"; video "Cara menghindari AI Checker cuma pake chatGPT" 139k. Caution: that audience wants evasion; ARTi should answer with "tulisan berbasis sumber dan pemikiran sendiri", never promise to beat detectors.
- **Idea/novelty:** "novelty penelitian" cluster (contoh, mencari, menemukan dengan vosviewer, cara menentukan), "research gap ai", "research gap dan novelty", "state of the art penelitian", "cara menentukan topik penelitian".
- **Journal quality:** "jurnal palsu" / "loa jurnal palsu", "cara cek doi", "jurnal predator" (earlier research).
- **Rejection/revision:** "desk rejection", "jika jurnal ditolak", "revisi jurnal"; videos: "Submit artikel tapi ditolak padahal berbayar" 22k, "Cara merespon revisi jurnal" 40k (2021), "revisi sesuai komentar reviewer menggunakan chatgpt" 1.8k (2025).
- **Speed promise sells:** "Menulis artikel jurnal Scopus kurang dari 1 jam" 29k, "...jauh lebih cepat dengan prompting" 14.9k, "Buat draft artikel jurnal cepat, berkualitas, lengkap dengan referensi" 9.9k (Ahmad Fauzi, Mimbar Intelek). ARTi's honest stance is a counter-position, not the market default.
- **Tools people compare:** elicit ai (vs scispace, systematic review), scispace (tutorial, gratis, vs scite), consensus ai (tutorial, vs scispace), notebooklm (podcast, tutorial indonesia, untuk guru), "claude vs chatgpt (for research)". Good basis for a comparison section/FAQ.
- **Claude Code in Indonesian:** "claude code indonesia", "tutorial indonesia", "windows", "vscode" have autocomplete; top Claude videos are general (William Jakfar 195k, Freddie Kashawan 170k, Agusleo Halim 126k), none for academics. "claude code untuk penelitian" is empty: the angle is open but the term must be taught.

## Copy implications for the landing page
- Hero and agitate in colloquial words: "ngawur", "lupa", "mulai dari nol", "referensi asli". Keep "halusinasi" as an explained term in one sentence.
- Turn each pain into the benefit phrase people already search: "referensi asli dan bisa dilacak", "ide dan novelty yang diukur", "revisi dan balasan reviewer yang rapi".
- Add a FAQ/section on AI detection and academic ethics, answered honestly (no evasion promise).
- Add a short comparison block against ChatGPT, NotebookLM, Elicit/SciSpace/Consensus (already drafted in ebook ch. 3, subchapter 17).
- Do not lead with the speed promise; if used, pair it with "tanpa mengarang".
- Memory pain is persuasive only through story (Alzheimer, Rithah), not search; keep it in the page body.

## Limits
Autocomplete is relative, not volume. Videos are the top results of 8 queries, mixed ages (2020-2026). "Pain" is inferred from titles, not from viewer interviews; still validate with 5-10 dosen. Not checked: Google web search, TikTok/Instagram, Facebook group language (where the ads would run).

## Course-package demand run (2026-10-19)
Method: `suggest` 20 seeds (`--lang id`, free) and `videos` 7 queries (`--lang id --region ID`, about 700 units). Top 10 per query only, views cumulative, ages mixed. Feeds the course-package brainstorm ([[course-packages-prompt]]).
- **SLR: strong, proven demand in Indonesian.** Autocomplete is rich ("cara membuat artikel slr dengan ai", "slr tembus scopus", "systematic literature review scispace/prisma/scopus", "prisma flow diagram"). Indonesian videos: Syamsul Hadi UST 107k (Scopus+VOSviewer+SciSpace, 2025), Hendra Nusa Putra Elicit 58k (2023), Ahmad Fauzi SLR series Part 1 54k (2024) and automatic extraction 30k (2025), Dr. Fauzi Channel ChatGPT+Scholar 24k (2026), dunia kampus PRISMA flow diagram 53k and SLR example 14k, Sobat Mahasiswa 13k. Occupied by Elicit, SciSpace, ChatGPT, NotebookLM; **no Indonesian video on Claude Code + PRISMA found**. English Claude-for-research videos are big and recent (Stuckler 153k, Stapleton 85k to 89k, 2025-2026), so interest in Claude among academics is rising. Trap: "slr ai" returns airsoft/car results; use "cara membuat artikel SLR dengan AI", "SLR PRISMA", "SLR Scopus".
- **Paper writing with AI: big but generic.** "menulis artikel scopus dengan ai" has autocomplete; Ahmad Fauzi 73k (2024), Mimbar Intelek "kurang dari 1 jam" 30k (2025); English "0% AI detection" 335k. "menulis artikel jurnal dengan ai" itself is empty. Thesis/skripsi AI videos (Braintext 100k, 89k) show a larger student audience than dosen; ARTi targets dosen.
- **BIMA: real, but about the portal, not the writing.** Autocomplete is very rich ("proposal bima 2025/2026", "cara mengisi tkt/rab/proposal bima", "template", "contoh", "submit/upload", "bedah proposal"). The leader is the official DPPM tutorial (74.9k penelitian, 21k PkM, 2023). Titles cluster in Nov-Dec 2025 and May 2025 (call and revision), which suggests an annual cycle (not verified). AI-for-BIMA videos exist but are small: Hendra Nusa Putra 1.8k, sarwono wono 0.8k and 0.2k. Demand is for form-filling (TKT, RAB, upload) and yearly rules, so a course tied to one year's guide goes stale. **The angle "AI for the proposal substance" is not proven.**
- Not checked: Google web search, Facebook groups, whether ARTi has a grant-proposal skill, BIMA call dates.

## Change log
- 2026-10-19 08:36 — added the course-package demand run (SLR, paper writing, BIMA).
- 2026-10-14 22:16 — created from the pain-point keyword run.
