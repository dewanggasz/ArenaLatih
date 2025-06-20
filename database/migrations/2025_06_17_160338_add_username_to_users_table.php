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
        Schema::table('users', function (Blueprint $table) {
            // PERUBAHAN DI SINI: Menambahkan nullable()
            // Ini akan mengizinkan nilai kosong dan mencegah error pada data lama.
            $table->string('username')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Instruksi untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('username');
        });
    }
};
