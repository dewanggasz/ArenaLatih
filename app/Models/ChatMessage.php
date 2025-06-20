<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'type',
        'parent_id',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Sistem', // Nama default untuk pesan sistem
        ]);
    }

    /**
     * Relasi untuk mengambil pesan induk (jika ini adalah balasan).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'parent_id');
    }

    /**
     * Relasi untuk mengambil semua balasan dari sebuah pesan.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'parent_id');
    }
}
