<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name'];

    /**
     * Satu Sub-Kategori dimiliki oleh satu Kategori Utama.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Satu Sub-Kategori bisa memiliki banyak Paket Latihan.
     */
    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }
}