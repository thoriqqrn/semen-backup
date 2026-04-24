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
        Schema::table('pendaftars', function (Blueprint $table) {
            // Kolom ini bisa nullable jika alamat di luar Gresik diizinkan
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
            $table->dropColumn('kelurahan_id');
        });
    }
};
