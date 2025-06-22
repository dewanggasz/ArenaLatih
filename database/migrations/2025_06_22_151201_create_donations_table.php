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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->string('donator_name'); // Nama donatur
            $table->string('email')->nullable(); // Email donatur (jika ada)
            $table->unsignedBigInteger('amount'); // Jumlah donasi
            $table->text('message')->nullable(); // Pesan dari donatur
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
