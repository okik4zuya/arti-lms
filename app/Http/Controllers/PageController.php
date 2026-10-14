<?php

namespace App\Http\Controllers;

use App\Support\Offer;

class PageController extends Controller
{
    public function home(Offer $offer)
    {
        return view('site.home', [
            'offer' => $offer,
            'freeChapterUrl' => config('site.free_chapter_url') ?: route('free.start'),
        ]);
    }
}
