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
            // Alamat unik untuk dibagikan, bisa null
            $table->uuid('share_uuid')->unique()->nullable()->after('id');
            // Lokasi path gambar yang akan dibuat, bisa null
            $table->string('share_image_path')->nullable()->after('share_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['share_uuid', 'share_image_path']);
        });
    }
};
