<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_tahun_kegiatan_to_galeris_table.php

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
        Schema::table('galeris', function (Blueprint $table) {
            // Tambahkan kolom 'tahun_kegiatan' setelah 'deskripsi'
            $table->year('tahun_kegiatan')->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galeris', function (Blueprint $table) {
            // Hapus kolom 'tahun_kegiatan' jika migration di-rollback
            $table->dropColumn('tahun_kegiatan');
        });
    }
};