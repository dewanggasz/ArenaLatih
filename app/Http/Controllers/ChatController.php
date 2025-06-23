<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Menampilkan halaman chat utama.
     */
    public function index()
    {
        // Mengambil semua pesan, diurutkan dari yang paling lama,
        // beserta data pengirimnya dan data pesan induk yang dibalas.
        $messages = ChatMessage::with(['user', 'parent.user'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', ['messages' => $messages]);
    }

       public function destroy(ChatMessage $message)
    {
        // Otorisasi: Pastikan pengguna yang terautentikasi adalah pemilik pesan.
        if ($message->user_id !== Auth::id()) {
            return response()->json(['status' => 'Unauthorized'], 403);
        }

        // Hapus pesan
        $message->delete();

        // Beri respons sukses
        return response()->json(['status' => 'Pesan berhasil dihapus!']);
    }

    /**
     * Menyimpan pesan baru ke database, termasuk balasan.
     */
    public function store(Request $request)
    {
        // Validasi input: pesan harus ada, dan parent_id (jika ada) harus valid.
        $request->validate([
            'message' => 'required_without:image|nullable|string|max:1000',
            'image' => 'required_without:message|nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'parent_id' => 'nullable|exists:chat_messages,id',
        ]);

        $type = 'text';
        $content = $request->message;

        if ($request->hasFile('image')) {
            $type = 'image';
            $path = $request->file('image')->store('chat_images', 'public');
            $content = $path;
        }

        if ($content) {
            $message = ChatMessage::create([
                'user_id' => Auth::id(),
                'parent_id' => $request->parent_id, // Simpan ID pesan induk
                'type' => $type,
                'message' => $content,
            ]);

            // Muat relasi agar bisa dikirim balik ke frontend dengan lengkap
            $message->load(['user', 'parent.user']);

            return response()->json([
                'status' => 'Pesan terkirim!',
                'message' => $message
            ]);
        }
        
        // Respons jika tidak ada konten sama sekali
        return response()->json(['status' => 'Pesan kosong.'], 422);
    }

    /**
     * Mengambil pesan baru untuk polling.
     */
    public function fetch(Request $request)
    {
        $lastMessageId = $request->query('last_id', 0);

        // Mengambil pesan baru beserta data pesan induknya.
        $newMessages = ChatMessage::with(['user', 'parent.user'])
            ->where('id', '>', $lastMessageId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        // 2. [BARU] Periksa pesan yang telah dihapus
        $deletedIds = [];
        $visibleIds = $request->input('visible_ids', []);

        if (!empty($visibleIds)) {
            // Cari tahu ID mana dari yang terlihat di layar yang masih ada di database
            $existingIds = ChatMessage::whereIn('id', $visibleIds)->pluck('id')->all();
            
            // Perbedaan antara yang dikirim frontend dan yang ada di DB adalah pesan yang dihapus
            $deletedIds = array_diff($visibleIds, $existingIds);
        }

        // 3. [BARU] Kirim respons gabungan
        return response()->json([
            'new_messages' => $newMessages,
            'deleted_ids' => array_values($deletedIds) // array_values untuk mereset key array
        ]);
    }

}
