<?php

namespace App\Http\Controllers;

class ContactController extends Controller
{
    /** Support is by email only; the address comes from config('site.contact_email'). */
    public function show()
    {
        return view('site.kontak');
    }
}
