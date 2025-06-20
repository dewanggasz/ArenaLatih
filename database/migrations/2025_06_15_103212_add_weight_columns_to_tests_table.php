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
            // Menambahkan kolom untuk bobot PG (default 70%)
            $table->unsignedTinyInteger('pg_weight')->default(70)->after('duration_minutes');
            // Menambahkan kolom untuk bobot Esai (default 30%)
            $table->unsignedTinyInteger('essay_weight')->default(30)->after('pg_weight');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn(['pg_weight', 'essay_weight']);
        });
    }
};
