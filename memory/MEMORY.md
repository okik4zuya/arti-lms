---
name: memory-index
description: Index of arti-lms project memory files, pointers only
metadata:
  type: project
  tier: T0
  created: 2026-09-08 21:36
  updated: 2026-10-18 21:54
---

# arti-lms — Memory Index

- [ARTi Framework context](memories/arti-framework-context.md) — what the ebook/framework is (finalized 5-section/18-chapter outline), drafted in this repo's own resources/inbox/
- [Subchapter hierarchy](memories/subchapter-hierarchy.md) — Section>Chapter>Subchapter content level, per-subchapter progress; sidebar is a `<details>` accordion with per-level type scale and badges numbered continuously per section
- [Tech stack options](memories/tech-stack-options.md) — 3 candidate stacks; Option 3 (git-backed Markdown, server-rendered) + relative-path image workflow chosen
- [Option 3 implementation plan](memories/option3-implementation-plan.md) — full build checklist + Laravel setup commands
- [Testing DB isolation](memories/testing-db-isolation.md) — phpunit.xml must point at arti_lms_testing, not the dev DB; RefreshDatabase wiped dev data once
- [Course layout responsive](memories/course-layout-responsive.md) — off-canvas sidebar + sections/chapters manifest grouping; clear the manifest cache when re-verifying; headless Chrome/Edge work directly; mobile drawer QA still open
- [Branding: login page](memories/branding-login.md) — login redesign, `/` redirects to login/dashboard, primary blue from the logo dot (#2563eb)
- [Feedback: check memory before env probes](memories/feedback-check-memory-before-env-probes.md) — don't re-run discovery for facts memory already records
- [Content authoring workflow](memories/content-authoring-workflow.md) — rules for drafting arti-framework subchapters, plus the Markdown rendering pipeline (GFM tables, wiki-link resolution, per-subchapter `<style>`)
- [Content authoring progress](memories/content-authoring-progress.md) — narrative status per chapter; the checklist itself lives in todo-list.md
- [ARTi App architecture](memories/arti-app-architecture.md) — this repo grows into the ARTi App; licensing stays on its Cloudflare Worker; phase-1 public routes built, risks and next steps
- [Landing page info](memories/landing-page-info.md) — sources, assets, proof, contradictions and open decisions for the landing page
- [Landing page concept](memories/landing-page-concept.md) — positioning, copy formula, outline, offer rules and the build record, including media placed (explainer, dashboard, e-course screenshots)
- [Landing page keywords](memories/landing-page-keywords.md) — YouTube keyword run on pain wording; pain words have little demand, so the page must teach the problem
- [Landing page copy](memories/landing-page-copy.md) — draft v1 of the Indonesian copy, section by section, with a fact-check list
- [ARTi PDF app facts](memories/artipdf-landing-facts.md) — standalone PDF GUI for the free section: release state (v1.0.6 public, 1.0.7 unreleased), features, unsigned installer, claims to avoid
- [Landing: SciPeek and ARTi PDF pages](memories/landing-scipeek-artipdf.md) — concept for /scipeek and /artipdf, keyword evidence, decisions, prerequisites; both pages built
- [Landing finalisation prompt](memories/landing-finalisation-prompt.md) — ready-to-paste prompt for the next session: brainstorm first, then landing copywriting evaluated with the reptilian-brain (six stimuli) concept
- [Redis setup](memories/redis-setup.md) — Redis (Laragon, Predis, DBs 10/11, own prefix) for cache, session, queue and the course content cache; opcache benchmark; 2026-10-18 back on in dev; production still open
- [Page speed](memories/page-speed.md) — what was measured and optimised on the public site, the `public/hot` measuring pitfall, results, and what is still unverified
- [Contact and WhatsApp](memories/contact-whatsapp.md) — floating button opens the contact panel (dialog), /kontak as fallback, number editable in Filament over .env, gotchas
- [License admin page](memories/license-admin.md) — Filament license issuing (A+: Worker enforces, Laravel keeps the record + encrypted key), what is verified, MFA/backup prerequisites, path to automatic purchases
- [License Worker setup runbook](memories/license-worker-setup.md) — Worker deployed at lisensi.arti.ai.id and smoke-tested; progress, the Python User-Agent gotcha, what is still open (Laravel production, admin IP restriction, D1 backup, MFA)
- [Domain migration to arti.ai.id](memories/domain-migration-arti-ai-id.md) — hostnames moved 2026-10-16, shared-VPS setup, gotchas (Brevo on ai.id, admin role, trusted proxy), what is open
- [License admin roadmap](memories/license-admin-roadmap.md) — agreed next stage: admin/support roles and permissions, payment proof and review queue, tier removed from the UI, compact issue modal, phases A-D (not built)
- [R2 storage](memories/r2-storage.md) — Cloudflare R2 buckets (arti public, arti-member, arti-private), what goes where, how the app reaches R2, gotchas; proofs must use arti-private
- [Legal review notes](memories/legal-review-notes.md) — what /syarat, /privasi, /kontak say, what needs a lawyer, proposed wording (not live)
- [List building](memories/list-building.md) — double opt-in mailing list; ARTi PDF download via R2 with thank-you dialog; sign-up cards on every free-chapter page; plus Filament "Daftar email" admin (filters, CSV, widgets, buyer marking), the 4-email welcome series (off by default), broadcasts, one-click unsubscribe; dev Redis -MISCONF gotcha
- [Automatic purchase prompt](memories/automatic-purchase-prompt.md) — the ready-to-paste prompt for the next session: payment gateway, learner account, license key, email and WhatsApp delivery, decisions to discuss first, hard rules
- [Automatic purchase plan](memories/automatic-purchase-plan.md) — what was built 2026-10-18 (Midtrans/Xendit gateway driver, orders, webhook, FulfillOrder, set-password link, forgot-password, quota, refund, reconcile, Filament pricing/promo), garansi mention list, decisions taken
- [Worker changes spec](memories/worker-changes-spec.md) — hand-over for the Worker repo: silent resend, months 1000, optional idempotent extend, client "Lifetime" label
- [Purchase production checklist](memories/purchase-production-checklist.md) — env variable names (mail scheme/From gotchas), migrations, cron, Midtrans dashboard (Settings, Payment), Brevo, sandbox steps and the 2026-10-18 ngrok run (5b)
- [Next-session prompt](memories/next-session-prompt.md) — purchase brainstorm prompt, deferred behind the landing copywriting session (sandbox result, WhatsApp provider, legal review, product side, production)
- [Duitku fallback](memories/duitku-fallback.md) — backup gateway if Midtrans rejects the account or category: individual requirements (no NIB, NPWP yes), fees, API quirks (status-check rate limit), support questions; not built
- [Mayar fallback](memories/mayar-fallback.md) — Mayar.id vs Midtrans: fees (about 3x on QRIS), NPWP optional for individuals, API gaps (no redirect/order id/webhook signature documented), questions for support; saved, not pursued
- [Learner app UX](memories/learner-app-ux.md) — 2026-10-18 night: empty-dashboard bug and cause, avatar menu, spinner, password UX, instant mark-complete, prev/next, resume card, Redis content cache, headless Chrome checking recipe, ideas not built
- [PHP binary and shell notes](memories/php-binary-and-shell.md) — php path for artisan/tests, Bash heredoc and CRLF gotchas
- [Landing explainer storyboard](memories/landing-explainer-storyboard.md) — the 40 s explainer: 7 scenes, build and render recipe, installed on the landing page
