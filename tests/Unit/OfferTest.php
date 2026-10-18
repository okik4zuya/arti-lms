<?php

namespace Tests\Unit;

use App\Support\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_launch_price_is_active_while_quota_remains(): void
    {
        config(['site.offer.launch_sold' => null]);
        $offer = new Offer;
        $this->assertSame(100, $offer->remaining());
        $this->assertTrue($offer->launchActive());
        $this->assertSame(99000, $offer->currentPrice());

        config(['site.offer.launch_sold' => '37']);
        $this->assertSame(63, (new Offer)->remaining());
    }

    public function test_normal_price_applies_once_the_quota_is_used_up(): void
    {
        config(['site.offer.launch_sold' => 100]);
        $offer = new Offer;

        $this->assertSame(0, $offer->remaining());
        $this->assertFalse($offer->launchActive());
        $this->assertSame(199000, $offer->currentPrice());

        config(['site.offer.launch_sold' => 140]);
        $this->assertSame(0, (new Offer)->remaining());
    }

    public function test_money_format_and_monthly_figure(): void
    {
        $offer = new Offer;

        $this->assertSame('Rp99.000', $offer->format(99000));
        $this->assertSame(16600, $offer->perMonthNormal());
    }

    public function test_buy_url_uses_whatsapp_when_configured_else_the_contact_page(): void
    {
        config(['site.contact_whatsapp' => null]);
        $this->assertSame(route('kontak'), (new Offer)->buyUrl());

        config(['site.contact_whatsapp' => '+62 812-3456-7890']);
        $url = (new Offer)->buyUrl();
        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $url);
        $this->assertStringContainsString('paket%20perintis', $url);
    }
}
