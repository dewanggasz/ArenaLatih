<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    /**
     * Menampilkan halaman formulir untuk mengirim saran.
     */
    public function create()
    {
        // Kita akan membuat view 'suggestions.create' di langkah berikutnya
        return view('suggestions.create');
    }

    /**
     * Menyimpan saran baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Buat saran baru dan hubungkan dengan pengguna yang sedang login
        Suggestion::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Kembalikan ke halaman yang sama dengan pesan sukses
        return redirect()->route('suggestions.create')
                         ->with('success', 'Terima kasih! Saran Anda telah berhasil kami terima.');
    }
}