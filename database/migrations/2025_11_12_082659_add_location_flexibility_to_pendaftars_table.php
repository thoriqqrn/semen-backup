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
            // Ubah kelurahan_id jadi nullable (bisa kosong untuk pendaftar luar Gresik)
            $table->foreignId('kelurahan_id')->nullable()->change();
            
            // Tambah kolom kota/kabupaten (wajib diisi)
            $table->string('kabupaten_kota')->after('alamat')->default('Gresik');
            
            // Tambah kolom untuk input manual (untuk pendaftar luar Gresik)
            $table->string('kecamatan_manual')->nullable()->after('kabupaten_kota');
            $table->string('kelurahan_manual')->nullable()->after('kecamatan_manual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftars', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['kabupaten_kota', 'kecamatan_manual', 'kelurahan_manual']);
            
            // Kembalikan kelurahan_id jadi not nullable
            // Note: ini mungkin gagal jika ada data dengan kelurahan_id NULL
            // $table->foreignId('kelurahan_id')->nullable(false)->change();
        });
    }
};
