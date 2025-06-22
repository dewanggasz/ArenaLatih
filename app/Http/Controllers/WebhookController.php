<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donation;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Menangani sinyal webhook yang masuk dari Saweria.
     */
    public function handleSaweria(Request $request)
    {
        // Untuk keamanan, kita bisa menambahkan validasi token atau signature
        // Namun, untuk saat ini kita akan langsung memproses datanya.

        try {
            $data = $request->all();

            // Saweria mengirim data donasi di dalam 'data' -> 'donator'
            // Kita akan mengambil informasi yang relevan
            $donatorData = $data['data'];

            Donation::create([
                'donator_name' => $donatorData['donator_name'] ?? 'Donatur Anonim',
                'email'        => $donatorData['donator_email'] ?? null,
                'amount'       => $donatorData['amount_raw'] ?? 0,
                'message'      => $donatorData['message'] ?? null,
            ]);
            
            // Beri tahu server Saweria bahwa sinyal sudah diterima dengan baik
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            // Jika terjadi error, catat di log untuk diinvestigasi nanti
            Log::error('Error saat menangani webhook Saweria: ' . $e->getMessage());
            // Beri tahu server Saweria bahwa ada masalah
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }
}
