<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\FreeChapterController;
use App\Http\Controllers\LicenseProofController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
| Main domain: public site, articles, auth, learner app. When APP_DOMAIN_MAIN is unset
| (local dev, tests) the routes answer on any host.
|
| The shop (config('app.domains.toko')) gets its own routes and `customer` guard in a later
| phase; nothing is registered for it yet.
*/
Route::group(array_filter(['domain' => config('app.domains.main')]), function () {

    // ---- Public site --------------------------------------------------------
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::view('fitur', 'site.fitur')->name('fitur');
    Route::view('scipeek', 'site.scipeek')->name('scipeek');
    Route::view('artipdf', 'site.artipdf')->name('artipdf');
    Route::get('artipdf/unduh', [DownloadController::class, 'artipdf'])->middleware('throttle:download')->name('artipdf.unduh');

    // ---- Mailing list (double opt-in; the links in the email are signed) -------------
    Route::post('daftar', [SubscriptionController::class, 'store'])->middleware('throttle:subscribe')->name('subscribe.store');
    Route::get('daftar/konfirmasi/{subscriber}', [SubscriptionController::class, 'confirm'])->middleware('signed')->name('subscribe.confirm');
    Route::get('berhenti/{subscriber}', [SubscriptionController::class, 'unsubscribe'])->middleware('signed')->name('subscribe.unsubscribe');
    // Pricing lives in the landing page's #harga section; the downloads page is gone (tools are in the LMS).
    Route::redirect('harga', '/#harga', 301);
    Route::redirect('unduh', '/', 301);
    Route::get('kontak', [ContactController::class, 'show'])->name('kontak');
    Route::post('kontak', [ContactController::class, 'send'])->middleware('throttle:10,1')->name('kontak.send');
    Route::view('privasi', 'site.privasi')->name('privasi');
    Route::view('syarat', 'site.syarat')->name('syarat');

    // ---- Free chapter 1 (no account) -------------------------------------------
    Route::prefix('baca')->group(function () {
        Route::get('/', [FreeChapterController::class, 'start'])->name('free.start');
        Route::get('{slug}', [FreeChapterController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('free.show');
        Route::get('{slug}/images/{file}', [FreeChapterController::class, 'image'])->where('slug', '[a-z0-9-]+')->name('free.image');
    });

    // ---- Articles (flat-file Markdown) ----------------------------------------
    // The tag route comes first: "tag" is also a valid {slug} shape.
    Route::prefix('artikel')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('tag/{tag}', [ArticleController::class, 'tag'])->where('tag', '[a-z0-9-]+')->name('articles.tag');
        Route::get('{slug}', [ArticleController::class, 'show'])->where('slug', '[a-z0-9-]+')->name('articles.show');
        Route::get('{slug}/images/{file}', [ArticleController::class, 'image'])->where('slug', '[a-z0-9-]+')->name('articles.image');
    });

    Route::get('feed.xml', [ArticleController::class, 'feed'])->name('feed');
    Route::get('sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
    Route::get('robots.txt', [SeoController::class, 'robots'])->name('robots');

    // ---- Auth -----------------------------------------------------------------
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'create'])->name('login');
        Route::post('login', [AuthController::class, 'store']);
    });

    Route::post('logout', [AuthController::class, 'destroy'])
        ->middleware('auth')->name('logout');

    // ---- Payment proofs: signed, short-lived link, opened from the admin panel -------
    Route::get('bukti-pembayaran/{issuance}', LicenseProofController::class)
        ->middleware(['auth', 'signed'])->name('license.proof');

    // ---- Learner app ------------------------------------------------------------
    Route::get('app', [DashboardController::class, 'index'])
        ->middleware('auth')->name('dashboard');

    Route::redirect('dashboard', '/app', 301);

    Route::middleware(['auth', 'course.access'])->prefix('learn/{course}')->group(function () {
        Route::get('{slug}', [ContentController::class, 'show'])->name('content.show');
        Route::get('{slug}/images/{file}', [ContentController::class, 'image'])->name('content.image');
        Route::post('{slug}/progress', [ProgressController::class, 'store'])->name('progress.store');
    });
});
