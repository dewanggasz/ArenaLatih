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
        // 1. Ambil semua paket latihan yang:
        //    a. Ditandai untuk tampil di peringkat.
        //    b. Memiliki setidaknya satu hasil yang sudah SELESAI.
        $tests = Test::where('show_on_leaderboard', true)
                     ->whereHas('results', function ($query) {
                         $query->where('status', 'completed'); // <-- KONDISI BARU YANG PENTING
                     })
                     ->with(['results' => function ($query) {
                         // 2. Untuk setiap latihan, ambil HANYA hasil yang sudah SELESAI,
                         //    lalu urutkan dari skor tertinggi.
                         $query->where('status', 'completed')->orderBy('score', 'desc');
                     }])
                     ->get();

        // 3. Olah data agar setiap pengguna hanya muncul sekali per latihan (dengan skor tertingginya).
        $leaderboards = $tests->mapWithKeys(function ($test) {
            // 'unique('user_id')' akan mengambil hasil pertama yang ditemui untuk setiap user.
            // Karena kita sudah urutkan berdasarkan skor tertinggi, ini akan menjadi skor terbaik mereka.
            $uniqueUserResults = $test->results->unique('user_id');
            
            // Batasi hanya menampilkan 10 peringkat teratas untuk setiap tes
            $topTen = $uniqueUserResults->take(10);

            // Kelompokkan hasilnya berdasarkan judul tes.
            return [$test->title => $topTen];
        });

        return view('leaderboard.index', ['leaderboards' => $leaderboards]);
    }
}
