<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;

class StaticPagesController extends Controller
{
    public function contact()
    {
        return view('static_pages.contact');
    }

    public function about()
    {
        return view('static_pages.about');
    }

    public function terms()
    {
        return view('static_pages.terms');
    }

    public function privacy()
    {
        return view('static_pages.privacy');
    }

    public function faq()
    {
        return view('static_pages.faq');
    }

    public function services()
    {
        return view('static_pages.services');
    }

    public function sitemap()
    {
        $path = public_path('sitemap.xml');

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
