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
        Schema::table('chat_messages', function (Blueprint $table) {
            // Menambahkan kolom parent_id setelah user_id
            // Boleh kosong (nullable) karena tidak semua pesan adalah balasan
            // Terhubung ke kolom 'id' di tabel yang sama
            $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('chat_messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Instruksi untuk menghapus kolom dan constraint jika migrasi di-rollback
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
