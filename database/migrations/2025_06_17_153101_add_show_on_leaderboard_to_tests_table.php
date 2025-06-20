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
        Schema::table('tests', function (Blueprint $table) {
            // Menambahkan kolom boolean (bisa true/false)
            // Secara default, semua tes akan ditampilkan di peringkat
            $table->boolean('show_on_leaderboard')->default(true)->after('essay_weight');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('show_on_leaderboard');
        });
    }
};
