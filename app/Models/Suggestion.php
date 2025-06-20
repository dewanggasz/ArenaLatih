<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'subject',
        'message',
    ];

    /**
     * Setiap saran dimiliki oleh satu pengguna (User).
     * Relasi ini memungkinkan kita untuk dengan mudah mengambil nama pengirim.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
