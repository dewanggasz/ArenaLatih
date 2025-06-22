<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Test; // <-- Import model Test

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // 1. Tambahkan halaman-halaman statis utama
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY));
        $sitemap->add(Url::create('/login')->setPriority(0.8));
        $sitemap->add(Url::create('/kebijakan-privasi')->setPriority(0.5));

        // 2. Tambahkan semua halaman "Mulai Latihan" secara dinamis
        Test::all()->each(function (Test $test) use ($sitemap) {
            $sitemap->add(Url::create("/test/{$test->id}/start")
                ->setPriority(0.9)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));
        });

        return $sitemap;
    }
}