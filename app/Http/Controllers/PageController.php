<?php

namespace App\Http\Controllers;

use App\Support\ArticleRepository;

class PageController extends Controller
{
    public function home(ArticleRepository $articles)
    {
        return view('site.home', [
            'latest' => $articles->all()->take(3),
        ]);
    }
}
