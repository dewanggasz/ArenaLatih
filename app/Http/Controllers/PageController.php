<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Menampilkan halaman kebijakan privasi.
     */
    public function privacyPolicy(): View
    {
        // Kita akan membuat view 'pages.privacy-policy' di langkah berikutnya
        return view('pages.privacy-policy');
    }
}
