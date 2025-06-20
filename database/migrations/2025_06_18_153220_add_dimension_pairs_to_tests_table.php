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
            // Kolom ini akan menyimpan string seperti "E,I S,N T,F J,P"
            // Boleh kosong (nullable) untuk tes berbasis skor.
            $table->string('dimension_pairs')->nullable()->after('result_type');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('dimension_pairs');
        });
    }
};
