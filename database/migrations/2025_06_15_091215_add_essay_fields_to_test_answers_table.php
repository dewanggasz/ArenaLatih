<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            // Buat choice_id bisa kosong (untuk soal esai)
            $table->foreignId('choice_id')->nullable()->change();
            // Tambahkan kolom untuk menyimpan jawaban teks esai
            $table->text('essay_answer')->nullable()->after('choice_id');
            // Tambahkan kolom untuk menyimpan feedback dari AI
            $table->text('ai_feedback')->nullable()->after('essay_answer');
            // Tambahkan kolom untuk menyimpan skor dari AI per soal
            $table->unsignedTinyInteger('ai_score')->nullable()->after('ai_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_answers', function (Blueprint $table) {
            //
        });
    }
};
