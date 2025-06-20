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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            // foreignId untuk menghubungkan saran ini ke seorang pengguna
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Kolom untuk subjek/judul saran
            $table->string('subject');
            // Kolom untuk isi pesan saran yang lebih panjang
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};
