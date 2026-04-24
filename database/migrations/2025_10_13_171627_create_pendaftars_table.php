<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_pendaftars_table.php

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
        Schema::create('pendaftars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pendaftaran')->unique(); // Unique code for checking status
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('nomor_hp'); // WhatsApp number
            $table->string('nomor_porsi_haji')->unique(); // Nomor Porsi Haji should be unique
            $table->string('email')->nullable(); // Optional email, not in form but good to have
            $table->string('status_pendaftaran')->default('Menunggu Verifikasi'); // Default status
            $table->text('catatan_admin')->nullable(); // For admin notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftars');
    }
};