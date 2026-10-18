---
name: legal-review-notes
description: What /syarat, /privasi and /kontak say today, which parts need a lawyer, and proposed wording for the missing clauses (not published)
metadata:
  type: project
  created: 2026-10-15 09:26
  updated: 2026-10-18 15:50
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

## Re-researched and rewritten 2026-10-18 (purchase automation, one-time lifetime, refund policy)
The owner delegated the wording of `/syarat`, `/kontak` and `/privasi` ("riset dan finalisasi, saya cek di sesi lain"). Not reviewed by a lawyer.
- **Research:** UU 8/1999 Pasal 18(1) huruf b and c forbid a standard clause that refuses to take goods back or to return money paid; Pasal 18(3) makes such a clause void (hukumonline gives "pembayaran yang sudah dilakukan tidak dapat ditarik kembali" as a forbidden example). Pasal 4 huruf h gives the right to compensation or replacement when goods or services do not match the agreement. So the terms cannot say "no refunds"; they list when money is returned and keep the statutory rights. UU PDP Pasal 56 (cross-border transfer) was reviewed by the Constitutional Court (decision 19 January 2026, not read); PP 80/2019 asks for identity and contract information in electronic trade.
- **Refund policy now in `/syarat#pengembalian` (my proposal, owner to confirm):** no unconditional 7-day guarantee; full refund when the account or key was not delivered within 3 days of a report, on a double or overcharged payment, when ARTi cannot be installed or activated on a qualifying Windows machine because of ARTi and is not fixed within 14 days (reported within 30 days of purchase), or when the package differs materially from the offer page; other requests judged one by one; answer within 3 working days, money back via Xendit within about 14 working days; then the key is revoked and course access removed.
- **Lifetime clause in `/syarat#masa-berlaku`:** no expiry date; "selamanya" means as long as ARTi is available; updates free while ARTi is developed; a fundamentally different major version may be sold separately; permanent shutdown announced 90 days ahead, online services (new activation, device moves, updates) may then stop. **Check with the client before relying on it:** that an already activated machine keeps working offline after the Worker is gone.
- **Privacy:** names Xendit (receives name, email, optional phone and the amount; ARTi gets status, method, invoice id, no card or account numbers), Brevo (purchase, reset and list mails), the optional WhatsApp copy (carries the key), hashed set-password tokens, IP use for throttling, unpaid orders deleted after a year (`orders:prune`), paid orders kept about 10 years.
- **Contact:** the seller block shows only when `SITE_SELLER_NAME` (and `SITE_SELLER_CITY`) are set; nothing is invented. The topic "Uang kembali (garansi)" is now "Pengembalian dana".
- **Still outside this repo:** `~/.arti/LICENSE.txt` (the EULA) still says refund "sesuai garansi", a license period "sejak kunci diterbitkan" and renewals; bring it in line with `/syarat` before the next installer build.
- **Open for a lawyer:** the refund conditions and time limits, the lifetime and shutdown clauses, the major-version clause, whether an individual seller needs a business licence or registration for electronic trade (PP 80/2019 Pasal 15 and 21), and the cross-border paragraph.

## Change log
- 2026-10-15 09:26 — created.
- 2026-10-16 17:00 — texts finalised from research (see above).
- 2026-10-18 15:50 — refund, lifetime and purchase-flow rewrite of syarat, kontak, privasi.
