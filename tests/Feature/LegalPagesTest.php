<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_terms_describe_a_one_time_lifetime_purchase_and_no_longer_promise_seven_days(): void
    {
        config(['site.offer.price_normal' => 199000, 'site.offer.price_launch' => 99000]);

        $this->get('/syarat')->assertOk()
            ->assertSee('Anda membayar sekali, tanpa biaya tahunan')
            ->assertSee('Rp199.000')
            ->assertSee('Rp99.000')
            ->assertSee('Lisensi tidak punya tanggal kedaluwarsa')
            ->assertSee('penyedia pembayaran berlisensi')
            ->assertSee('tidak pernah mengirim atau meminta password')
            ->assertSee('id="pengembalian"', false)
            ->assertSee('Pengembalian dana')
            ->assertSee('tidak berafiliasi dengan Anthropic')
            ->assertDontSee('7 hari sejak tanggal pembelian')
            ->assertDontSee('Garansi')
            ->assertDontSee('untuk satu tahun')
            ->assertDontSee('Perpanjangan mengikuti')
            ->assertDontSee('per tahun, sekitar');
    }

    public function test_the_refund_section_never_says_money_is_not_returned(): void
    {
        $text = strip_tags($this->get('/syarat')->getContent());

        // A standard clause refusing refunds is void under the consumer protection law; the page must list when money
        // is returned and keep the statutory rights instead.
        $this->assertStringNotContainsStringIgnoringCase('tidak dapat dikembalikan', $text);
        $this->assertStringNotContainsStringIgnoringCase('tidak ada pengembalian', $text);
        $this->assertStringContainsString('UU Perlindungan Konsumen', $text);
        $this->assertStringContainsString('Pembayaran terjadi dua kali', $text);
    }

    public function test_the_terms_have_balanced_markup(): void
    {
        $html = $this->get('/syarat')->getContent();

        $this->assertSame(1, substr_count($html, '<article'));
        $this->assertSame(1, substr_count($html, '</article>'));
    }

    public function test_the_contact_page_points_to_the_refund_terms_and_shows_the_seller_only_when_known(): void
    {
        config(['site.seller.name' => null, 'site.seller.city' => null]);
        $this->get('/kontak')->assertOk()
            ->assertSee('pengajuan pengembalian dana')
            ->assertSee('/syarat#pengembalian', false)
            ->assertDontSee('garansi')
            ->assertDontSee('Penyelenggara:');

        config(['site.seller.name' => 'Budi Peneliti', 'site.seller.city' => 'Bandung']);
        $this->get('/kontak')->assertSee('Penyelenggara:')->assertSee('Budi Peneliti, Bandung');
    }

    public function test_the_privacy_page_names_the_payment_and_email_processors_and_the_password_rule(): void
    {
        $this->get('/privasi')->assertOk()
            ->assertSee('penyedia pembayaran berlisensi')
            ->assertSee('Brevo')
            ->assertSee('tidak menerima dan tidak menyimpan nomor kartu atau rekening')
            ->assertSee('Password tidak pernah dikirim, disimpan dalam bentuk asli, atau dicatat di log')
            ->assertSee('Pesanan yang tidak pernah dibayar')
            ->assertDontSee('Pembelian berlangsung lewat WhatsApp, jadi');
    }

    public function test_no_marketing_or_legal_page_still_promises_a_guarantee(): void
    {
        // /syarat itself says, once, that there is no no-questions-asked guarantee; that is tested above.
        foreach (['/', '/fitur', '/kontak', '/privasi', '/scipeek', '/artipdf'] as $path) {
            $text = strip_tags($this->get($path)->getContent());
            $this->assertStringNotContainsStringIgnoringCase('garansi', $text, $path);
            $this->assertStringNotContainsStringIgnoringCase('uang kembali', $text, $path);
        }
    }

    public function test_unpaid_orders_older_than_a_year_are_deleted_and_nothing_else(): void
    {
        $make = fn (string $id, string $status, int $ageDays, bool $paid = false) => tap(Order::create([
            'public_id' => $id, 'name' => 'X', 'email' => "{$id}@example.test", 'amount' => 99000, 'price_tier' => 'normal', 'status' => $status,
        ]), fn (Order $o) => $o->forceFill(['created_at' => now()->subDays($ageDays), 'paid_at' => $paid ? now()->subDays($ageDays) : null])->save());

        $oldExpired = $make('arti-old-expired', Order::EXPIRED, 400);
        $oldFailed = $make('arti-old-failed', Order::FAILED, 400);
        $recentExpired = $make('arti-recent-expired', Order::EXPIRED, 30);
        $oldPaid = $make('arti-old-paid', Order::FULFILLED, 900, true);
        $oldRefunded = $make('arti-old-refunded', Order::REFUNDED, 900, true);
        $oldPending = $make('arti-old-pending', Order::PENDING, 400);

        $this->artisan('orders:prune')->expectsOutputToContain('Deleted 2')->assertSuccessful();

        $this->assertNull(Order::find($oldExpired->id));
        $this->assertNull(Order::find($oldFailed->id));
        foreach ([$recentExpired, $oldPaid, $oldRefunded, $oldPending] as $kept) {
            $this->assertNotNull(Order::find($kept->id), $kept->public_id);
        }
    }

    public function test_the_prune_job_is_scheduled(): void
    {
        $this->artisan('schedule:list')->expectsOutputToContain('orders:prune')->assertSuccessful();
    }
}
