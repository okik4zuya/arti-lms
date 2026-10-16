---
name: legal-review-notes
description: What /syarat, /privasi and /kontak say today, which parts need a lawyer, and proposed wording for the missing clauses (not published)
metadata:
  type: project
  created: 2026-10-15 09:26
  updated: 2026-10-15 09:26
---

Prepared in the 2026-10-15 finalisation session ([[landing-page-concept]], [[landing-page-copy]]). The live pages restate only decisions already published on the landing; they keep a "Draf ... belum ditinjau secara hukum" notice. Nothing below is legal advice and none of it is on the site.

## Verified against reality (2026-10-15)
- Public pages set two cookies (`laravel_session`, `XSRF-TOKEN`) even before login: now stated on `/privasi`.
- Fonts are self-hosted. Chapters used to `@import` Google Fonts (Atkinson Hyperlegible); fixed by self-hosting and stripping the import in `MarkdownRenderer`, so the "no third-party request" line is true. If `SITE_DEMO_VIDEO_EMBED_URL` is ever an embedded player (YouTube), its host will see visitors: update `/privasi` then.
- Activation sends the licence key and a hashed machine code to the licensing Worker (design in `~/.arti/memory/memories/licensing-design.md`; the design was "nothing built" on 2026-10-13, so **confirm the shipped Worker sends nothing more**) and re-read the privacy line.
- Naskah and project files stay local; Claude Code sends processed content to Anthropic; literature search sends query terms to OpenAlex, Semantic Scholar, Google Scholar.

## Needs a lawyer (flagged in Blade comments on both views)
1. **Refund clause** (7 days from purchase date, no reason, by WhatsApp; plus "installation failed on ARTi's side: refund any time during the subscription"): enforceability, what counts as the purchase date, treatment of payment fees.
2. **Personal data (UU PDP):** who is the data controller, legal basis, retention periods, data-subject rights, cross-border transfer (Anthropic, Cloudflare), children.
3. **Licence scope:** one person, devices allowed, no sharing, what "satu tahun" counts from, what happens on expiry beyond the dashboard locking.
4. **Liability and disclaimers**, governing law and dispute forum.
5. **Separate EULA** for the software (still unpublished).

## Proposed wording (to review, not live)
- Liability: "ARTi disediakan apa adanya. Sejauh diizinkan hukum, tanggung jawab ARTi dibatasi pada jumlah yang Anda bayar untuk paket ini. ARTi tidak bertanggung jawab atas keputusan jurnal, kebijakan penggunaan AI sebuah jurnal, atau perubahan layanan Anthropic."
- Licence: "Lisensi berlaku untuk satu pengguna dan [jumlah] perangkat, tidak dapat dipindahtangankan atau dibagikan. Masa berlaku satu tahun sejak [tanggal aktivasi / tanggal pembelian]."
- Governing law: "Syarat ini tunduk pada hukum Republik Indonesia. Sengketa diselesaikan lebih dulu secara musyawarah." (forum to be decided).
- Data: "Data akun disimpan selama akun aktif dan dihapus dalam [n] hari setelah permintaan penghapusan, kecuali data yang wajib disimpan menurut hukum." (period to be decided).

## Finalised 2026-10-16
The three texts (`/syarat`, `/privasi`, the software EULA in `~/.arti/LICENSE.txt`) were rewritten from research on UU 8/1999 Pasal 18, UU 27/2022, PP 80/2019 and UU 28/2014 and published without the draft banners, at the researcher's request. No lawyer reviewed them. Still open: the seller's legal identity on `/kontak`, and confirming the retention periods and the dispute forum.

## Change log
- 2026-10-15 09:26 — created.
- 2026-10-16 17:00 — texts finalised from research (see above).
