<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_dokumen_pendaftarans_table.php

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
        Schema::create('dokumen_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftar_id')->constrained('pendaftars')->onDelete('cascade'); // Foreign Key to Pendaftar

            $table->string('file_ktp_path');
            $table->string('file_kk_path');
            $table->string('file_akta_path');
            $table->string('file_nikah_path')->nullable(); // Optional
            $table->string('file_ijazah_path');
            $table->string('file_bpih_path');
            $table->string('file_spph_path');
            $table->string('file_foto_path');
            $table->string('file_paspor_path');
            $table->string('file_booster1_path');
            $table->string('file_booster2_path');
            $table->timestamps();

            // Tambahan: Tambahkan constraint unique jika setiap pendaftar hanya punya 1 dokumen_pendaftaran
            // $table->unique('pendaftar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_pendaftarans');
    }
};