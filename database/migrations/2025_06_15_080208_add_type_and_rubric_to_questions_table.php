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
        Schema::table('questions', function (Blueprint $table) {
            // Menambah kolom tipe soal
            $table->string('type')->default('pilihan_ganda')->after('id');
            // Menambah kolom untuk rubrik penilaian AI
            $table->text('rubric')->nullable()->after('explanation');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'rubric']);
        });
    }
};
