<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'outcome_code',
        'title',
        'description',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}