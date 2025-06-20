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
        Schema::create('test_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->string('outcome_code'); // Kode unik, misal: 'INTJ'
            $table->string('title'); // Judul, misal: 'Sang Arsitek'
            $table->text('description'); // Deskripsi lengkap tentang tipe ini
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_outcomes');
    }
};
