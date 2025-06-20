<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_category_id', // <-- Tambahkan ini
        'title',
        'description',
        'duration_minutes',
        'pg_weight',
        'essay_weight',
        'show_on_leaderboard',
        'result_type',
        'dimension_pairs',
        
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * Satu Paket Latihan dimiliki oleh satu Sub-Kategori.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(TestOutcome::class);
    }
}