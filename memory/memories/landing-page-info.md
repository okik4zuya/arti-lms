---
name: landing-page-info
description: Information gathered 2026-10-14 for the ARTi landing page (assets, proof, gaps, open decisions); read before designing or writing landing copy
metadata:
  type: project
  created: 2026-10-14 21:50
  updated: 2026-10-14 21:50
---

Discussion-only session; nothing built. Current public site is the honest phase-1 skeleton (see [[arti-app-architecture]]); this file is the input for the real landing page.

## Where the facts live (read these, do not copy them here)
- Feature source: `~/.arti/memory/memories/landing-page-feature-inventory.md` (**stale**, dated 2026-09-06: lacks ARTi-SLR, jfinder, crosscite, annotate, compose, search MCP, dashboard features, licensing, SciPeek).
- Skills/tools map: `~/.arti/CAPABILITIES.md` (10 skills, 16 tool folders). `~/.arti/README.md` says "nine skills" but lists ten.
- Brand: `~/.arti/logo/export`, `~/.arti/memory/memories/branding-logo.md`, `resources/css/tokens.css`.
- Licensing/product status: `~/.arti/memory/memories/licensing-update-plan.md` (yearly subscription decided; Worker not deployed; no prices, EULA, gateway, domain).
- Channel and audience: `G:\My Drive\YT Dosen Berarti\memory` (`channel-strategy`, `competitor-research`, `keyword-research`).
- SciPeek: `C:\python_tools\arti_jfinder\ARTi Journal Finder\extension` (store assets, GIF, video, screenshots).

## Status facts (2026-10-14)
- **ARTi SciPeek is published on the Chrome Web Store**: https://chromewebstore.google.com/detail/arti-scipeek-%E2%80%94-journal-qu/dhbbdhnepmodmcheapjhcaolpcmpoanj (free, offline, MIT code). It is the one product that can have a live CTA today.
- Framework installer exists (NSIS, Windows only; Mac untested, Linux broken) but was never run on a clean machine; license not deployed, so no purchase CTA yet.
- Ebook: 3 of 18 chapters drafted (Pendahuluan); Amati/Replikasi/Tambah Inovasi/BONUS empty. Landing must not promise a finished ebook.
- Users also need a paid Claude plan plus Claude Code and VS Code; landing should say so.

## Proof available (ask permission before using)
Paper Metnum ECE (10 sessions to full Introduction+Methods, 43 tracked sources), SnO2 SLR (445 hits, 57 studies), Nanobubble SLR. No testimonials, no dashboard screenshots, no ARTi demo video yet.

## Decisions (researcher, 2026-10-14; supersede the recommendations of the first discussion)
1. **Main product = e-course** read in the LMS; the ARTi Framework is a licensed tool that supports it, **sold in the same package**. SciPeek stays the free entry point.
2. **Sales flow:** admin sends the license key by WhatsApp; the framework calls the Cloudflare Worker to check the key is valid and never used on any machine; if it fails, installation fails. The Midtrans/Xendit webhook flow is deferred ([[arti-app-architecture]]). Where memory conflicts, this wins.
3. **Audience:** dosen first, then S2/S3 (approved).
4. **Case studies/proof:** skipped for now; landing carries no case-study numbers.
5. **Price (confirmed):** Rp199.000 per year; launch price **Rp99.000 for the first 100 buyers** (50% discount, ends at 100 buyers, not at a date). One package replaces the five planned tiers on `/harga`. The buyer count must be tracked by the admin (issued licenses).
6. **Domain (confirmed):** `arti.invait.id` hosts the landing page; set `APP_DOMAIN_MAIN` accordingly.
7. **Internet required:** installation must connect to the internet so the Worker can verify the license; no offline install.
8. **Trial removed:** no trial key for the paid launch (drop the "Uji coba" tier from `/harga`; `/trial` Worker endpoint and CLI command become unused).
9. **Ebook chapter 3 will be revised later** to match the licensed-tool story; the landing is not blocked on it.

## Remaining gaps
- Name "ARTi Workflow" vs "ARTi Framework" still open outside the ebook.
- Landing must state that Claude (paid plan), Claude Code and VS Code are needed, Windows only, internet needed to install, and that e-course chapters beyond Pendahuluan are still being written.
- Refund policy and EULA are undecided; do not imply either on the page.
- Whether an already-activated install may run offline afterwards (grace period) is still unconfirmed.

## Change log
- 2026-10-14 22:11 — concept moved to [[landing-page-concept]].
- 2026-10-14 22:10 — confirmed domain, Rp99.000 for the first 100 buyers, internet-required install, trial removed, ebook ch. 3 revision deferred.
- 2026-10-14 21:59 — recorded the researcher's answers to the six decisions (bundle, WhatsApp issuing with fail-closed activation, price, domain).
- 2026-10-14 21:50 — created from the cross-project information sweep; SciPeek publication recorded.
