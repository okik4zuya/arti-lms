<?php

namespace App\Http\Controllers;

use App\Models\DownloadCount;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    /**
     * Counts the download, then sends the visitor to the installer on R2. Without a configured R2 link
     * it goes to the GitHub release page, which is a web page, so the button opens it in a new tab.
     */
    public function artipdf(Request $request)
    {
        if (! $request->isMethod('HEAD')) {
            DownloadCount::bump('artipdf');
        }

        return redirect()
            ->away(config('site.artipdf_download_url') ?: config('site.artipdf_url'))
            ->header('X-Robots-Tag', 'noindex');
    }
}
