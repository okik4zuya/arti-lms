<?php

namespace App\Http\Controllers;

use App\Services\Payments\PaymentGateway;
use App\Support\Offer;

class PageController extends Controller
{
    public function home(Offer $offer, PaymentGateway $gateway)
    {
        return view('site.home', [
            'offer' => $offer,
            'gateway' => $gateway,
            'freeChapterUrl' => config('site.free_chapter_url') ?: route('free.start'),
        ]);
    }
}
