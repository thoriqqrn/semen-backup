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
        Schema::table('dokumen_pendaftarans', function (Blueprint $table) {
            // Buat kolom opsional jadi nullable
            $table->string('file_paspor_path')->nullable()->change();
            $table->string('file_booster1_path')->nullable()->change();
            $table->string('file_booster2_path')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_pendaftarans', function (Blueprint $table) {
            $table->string('file_paspor_path')->nullable(false)->change();
            $table->string('file_booster1_path')->nullable(false)->change();
            $table->string('file_booster2_path')->nullable(false)->change();
        });
    }
};
