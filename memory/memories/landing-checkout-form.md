---
name: landing-checkout-form
description: The buy form on the landing page (#checkout), the two payment-method cards mapped to Midtrans Snap, mobile buy bar, WhatsApp removed from the UI, support email hi@arti.ai.id; what was built, decisions, what is unverified
metadata:
  type: project
  created: 2026-10-19 06:34
  updated: 2026-10-19 08:15
---

Built 2026-10-19 after the researcher showed a competitor's (Scalev, course.dosen.my.id) checkout section. Builds on [[automatic-purchase-plan]] (gateway, orders, webhook) and replaces the WhatsApp contact UI in [[contact-whatsapp]]. All uncommitted.

## Decisions (researcher, 2026-10-19)
- Level 2 of three: landing form plus a method choice before paying (not a Snap popup/embed, not Core API).
- WhatsApp number stays **optional**, with a note: it is used for the product-delivery notification, same content as the email.
- Layout copies the competitor's: big price, "Sekali pembayaran • tanpa biaya bulanan", product chip, "Data Pembeli", WhatsApp and email side by side, two method cards, yellow CTA "Beli & Akses Sekarang →", sticky bar on mobile.
- Form lives on the landing page, not on `/beli`.
- Later the same day: floating WhatsApp button and panel removed from the UI, "Beli lewat WhatsApp" removed everywhere ("mulai sekarang full otomatis"), support email **hi@arti.ai.id** in the footer.

## How it works
- Partial `resources/views/site/partials/checkout.blade.php` (id `#checkout`) inside the `#harga` panel of `home.blade.php`; all "Beli sekarang" buttons on the landing are `#checkout`. `GET /beli` redirects to `/#checkout` (old links, mails, other pages); `POST /beli` validates with `Validator::make` and sends errors and old input back to `/#checkout`. `beli.blade.php` is deleted.
- Field `metode` is `va` or `qris` (required, `va` preselected). `CreateCheckout::handle(..., $method)` stores it in the new column `orders.method_choice` (migration `2026_10_19_100000`) and passes it to `PaymentGateway::createInvoice(..., ?string $method)`. An open pending order is reused only for the same method; a changed method expires the old order (slots released, event `method_changed`), so no second launch place is used.
- `MidtransClient` sets Snap `enabled_payments` from `config('services.midtrans.methods')`: `va` = bca_va, bni_va, bri_va, permata_va, echannel, gopay, shopeepay; `qris` = other_qris. Env overrides `MIDTRANS_METHODS_VA`, `MIDTRANS_METHODS_QRIS` (comma separated). Xendit ignores the method.
- Card label is "Virtual Account / GoPay / ShopeePay" (the competitor says DANA; DANA is **not** confirmed for the researcher's Midtrans account, so it is not claimed).
- Mobile buy bar: `[data-buy-bar]` at the end of `home.blade.php`, script `resources/js/sticky-buy.js` (own vite entry), shows after 200 px scroll and hides while `#checkout` is visible; CSS hides it from 768 px. Yellow `.btn--cta` and the `.checkout`/`.method` styles are in `site.css`.
- Without a configured gateway the form is replaced by a note with the support email (no WhatsApp fallback).
- Form label is "Nama" (was "Nama lengkap"), placeholder "Masukkan nama Anda" (2026-10-19).
- Support email: `config('site.contact_email')`, default `hi@arti.ai.id` (`SITE_CONTACT_EMAIL` overrides); in the footer of every page, on `/kontak` (now email only; the WhatsApp form, `POST /kontak`, `contact-form` partial and `contact-panel.js` are deleted) and on the payment status page. `Offer::buyUrl()` is deleted. `/syarat` and `/privasi` lost their "buy via WhatsApp" sentences (the researcher should re-read them).

## Not touched on purpose
- WhatsApp delivery stage and the optional number in the form (driver `none` still, so the form note promises something not live yet).
- Filament "Pengaturan situs" still has the WhatsApp contact number and default messages; they are now unused by the public UI.
- Manual licence flow (`LicenseMessage`) still builds wa.me links for the admin.

## Verified / not verified
- Tests green: CheckoutTest (23), MidtransGatewayTest (22), ContactFormTest (5), PublicSiteTest, LegalPagesTest, FeaturePageTest, LandingPageTest, OfferTest, PublicPageQualityTest, EmailSeriesTest, ArtiPdfPageTest, SciPeekPageTest, PerformanceTest. The full suite was not run.
- **Not seen in a browser or on a phone**; no real Snap sandbox call with `enabled_payments` yet (`other_qris` and `shopeepay` must be active in the Midtrans dashboard or Snap may refuse).
- `php artisan migrate` (new column) and `npm run build` (new JS entry) are the researcher's to run.

## Gotchas met
- A `sed` insert of `use App\Services\Payments\PaymentGateway;` lost its backslashes and 500'd `/` ("Class App\Http\Controllers\PaymentGateway does not exist"); see [[php-binary-and-shell]]: use Edit or a script file for lines with backslashes.
- A Bash heredoc holding Blade/Python with apostrophes failed with "unexpected EOF" and wrote nothing; use the Write tool for files.
- Tests that asserted the old `/beli` page or WhatsApp links (CheckoutTest, MidtransGatewayTest, ContactFormTest, LandingPageTest, FeaturePageTest, PublicSiteTest, OfferTest) were rewritten; copy changes in the form label ("Harga perintis. Tersisa N dari 100") are asserted by LandingPageTest.

## Change log
- 2026-10-19 08:15 — label "Nama".
- 2026-10-19 06:34 — created.
