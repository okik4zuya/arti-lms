---
name: contact-whatsapp
description: How the WhatsApp number, the floating button's contact panel (form in a dialog), the /kontak fallback page work, where the number is set (Filament "Pengaturan situs" over .env), and the gotchas
metadata:
  type: project
  created: 2026-10-15 09:54
  updated: 2026-10-15 10:25
---

Built 2026-10-15 after the finalisation pass ([[landing-page-concept]], [[legal-review-notes]]). Code is in the working tree or in whatever commits the researcher kept; they reset the commits made in this session, so check `git log` before assuming.

## Number
- Source order: a value saved in the admin (`site_settings` table, key `contact_whatsapp`) wins over `SITE_CONTACT_WHATSAPP` in `.env`; blank falls back to `.env`. `AppServiceProvider::boot()` copies the stored value into `config('site.contact_whatsapp')` (cached in `SiteSetting::values()`, cleared on save; a missing table is ignored so a fresh deploy does not break). Run `php artisan migrate` in production for the table.
- Admin page: Filament `/jamrud/site-settings` ("Pengaturan situs", `App\Filament\Pages\SiteSettings`), admin only. Fields: number (regex check), "Pesan default pembelian" (`whatsapp_message`, max 300; panel pre-fill and the Beli links' no-JS message) and "Pesan default chat umum" (`chat_message`; floating button's no-JS link). Each filled value overrides its config entry via `SiteSetting::CONFIG_KEYS` (applied in `AppServiceProvider`); blank keeps the `config/site.php` default. The panel's general-topic message is deliberately empty, so `chat_message` only affects the direct link.
- `Offer::whatsappNumber()` normalises digits and turns a leading 0 into 62 (a bare `wa.me/0812...` link does not work); `Offer::whatsappUrl($message)` builds the link or returns null without a number. Every buy button uses it via `Offer::buyUrl()`.

## One entry point: the panel (2026-10-15, later)
The floating button opens a `<dialog id="wa-panel">` holding the contact form; every trigger goes through it. Every trigger keeps a real href, so without JS nothing breaks.
- Triggers (`resources/js/contact-panel.js`, loaded by the layout only when a number exists and not on `/kontak`): `.wa-float`, any link to `/kontak` (footer, menu, `?topik=` honoured), `[data-kontak="pembelian"]` on the 7 "Beli lewat WhatsApp" buttons (href stays `Offer::buyUrl()`), and `#kontak` in the URL (load and `hashchange`).
- Buy flow is one tap: topic Pembelian preselected, message pre-filled from `ContactController::templates()` (the "Halo, " dropped because the composed text already opens with "Halo"), name optional. Template swaps only while the message is untouched.
- Desktop: non-modal `show()` card above the button, focus to the first empty field. Phone (max 767px): `showModal()` bottom sheet, no auto-focus so the keyboard does not cover it. Esc, the – button, backdrop and Back (`pushState('#kontak')`) all close it through the `close` event; focus returns to the trigger.
- Submit: JS builds the text (same format as `ContactController::send()`, keep the two identical) and clicks a temporary `<a target=_blank rel=noopener>` on wa.me, so popup blockers allow it. Draft (nama, topik, pesan) in `sessionStorage`; pesan cleared after sending. Pulse stops after the first open (`is-seen`).

## `/kontak` (fallback)
- `ContactController`: GET shows the form (partial `site/partials/contact-form.blade.php`, shared with the panel; `idPrefix` keeps ids unique) when a number exists; POST (`throttle:10,1`) validates nama (optional now), topik and pesan (max 1000), redirects to wa.me. Nothing is stored. No number: no form and the old "belum dibuka" text (404 on POST). No panel or button on this page.

## Floating button
- In `layouts/site.blade.php`, WhatsApp's own `#25d366` with a white icon, the original look, kept on purpose. The white icon is only about 2:1 on that green (below WCAG 3:1); the researcher tried `#128c7e` (too dark) and a dark icon on a white disc (looked odd) and chose the original, so do not change it again without asking. The panel header uses the same `#25d366` with white text and icon, to match the button. Pulse ring paused on hover/focus and off under `prefers-reduced-motion`. Shows on every public page except `/kontak`, only with a number; link message from `config('site.offer.chat_message')`. Not on the login and error layouts.

## Gotchas met
- Writing PHP namespaces with backslashes through `node -e` or `sed` in Bash lost the backslashes (`AppSupportOffer`) and 500'd every public page; use the Edit tool for lines with backslashes.
- A Bash `node -e` string replacement silently does nothing when the pattern misses; verify with grep.

- The vite dev pointer `public/hot` (http://localhost:5600) is present locally; to check built assets, rename it away temporarily (restored afterwards) and run the site with `SITE_CONTACT_WHATSAPP` set, because `.env` has no number.
- Headless Chrome driven over CDP from Node 22 (built-in WebSocket) works for clicking and reading state; stubbing `HTMLAnchorElement.prototype.click` globally also breaks the footer link, so only stub the `target=_blank` one.
- A same-page link to `/#kontak` changes only the hash; without a `hashchange` listener the panel did not open.

## Tests
`ContactFormTest` (no number, redirect text, optional name, validation, `?topik=` preselect, panel in the layout only with a number and not on `/kontak`, `data-kontak` on the buy buttons, admin override, Filament page, floating button) and `OfferTest` (0 becomes 62). Suite 108 green. Checked in headless Chrome at 1100 and 375 px: open/close, Esc, Back, footer link, `#kontak`, send text, empty-message error. Not checked: a real phone opening the WhatsApp app from the new tab.

## Change log
- 2026-10-15 09:54 — created.
- 2026-10-15 10:08 — panel replaces direct links; /kontak kept as fallback; contrast fixed.
- 2026-10-15 10:25 — default messages editable in Filament (CONFIG_KEYS); suite 110 green.
