<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'question_id',
        'choice_id',
        'essay_answer',
        'ai_feedback',
        'ai_score',
    ];

    /**
     * Setiap jawaban (jika PG) dimiliki oleh satu Pilihan Jawaban.
     */
    public function choice(): BelongsTo
    {
        return $this->belongsTo(Choice::class);
    }

    /**
     * Setiap jawaban dimiliki oleh satu Pertanyaan.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
