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
        Schema::table('test_results', function (Blueprint $table) {
            // Kolom untuk status: 'in_progress' atau 'completed'
            $table->string('status')->default('completed')->after('score');
            // Kolom untuk mencatat kapan tes dimulai
            $table->timestamp('started_at')->nullable()->after('status');
            // Kolom untuk menyimpan sisa waktu dalam detik
            $table->unsignedInteger('time_remaining')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at', 'time_remaining']);
        });
    }
};
