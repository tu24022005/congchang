<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function policies(): View
    {
        return view('pages.policies');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }
}
