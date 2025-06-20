<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    /**
     * Menampilkan halaman papan peringkat.
     */
    public function index()
    {
        // --- PERUBAHAN DI SINI ---
        // 1. Ambil semua paket latihan yang:
        //    a. Memiliki hasil ujian (whereHas 'results')
        //    b. Ditandai untuk tampil di peringkat (where 'show_on_leaderboard' is true)
        $tests = Test::where('show_on_leaderboard', true) // <-- KONDISI BARU DITAMBAHKAN
                     ->whereHas('results')
                     ->with(['results' => function ($query) {
                         $query->orderBy('score', 'desc');
                     }])
                     ->get();

        // 2. Olah data agar setiap pengguna hanya muncul sekali per latihan (dengan skor tertingginya).
        $leaderboards = $tests->mapWithKeys(function ($test) {
            $uniqueUserResults = $test->results->unique('user_id');
            $topTen = $uniqueUserResults->take(10);
            return [$test->title => $topTen];
        });

        return view('leaderboard.index', ['leaderboards' => $leaderboards]);
    }
}
