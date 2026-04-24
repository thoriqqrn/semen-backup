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
            $table->string('jenis_porsi')->after('nomor_hp'); // berangkat atau penggabungan, tanpa default
            $table->string('nomor_porsi_penggabungan')->nullable()->after('jenis_porsi'); // Nomor porsi yang digabungkan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            $table->dropColumn(['jenis_porsi', 'nomor_porsi_penggabungan']);
        });
    }
};
