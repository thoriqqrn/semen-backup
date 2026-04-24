<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_galeris_table.php

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
        Schema::create('galeris', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('tipe')->default('foto'); // 'foto' atau 'video'
            $table->string('file_path');
            $table->string('thumbnail_path')->nullable(); // Optional for video thumbnails or smaller image versions
            $table->boolean('is_published')->default(true);
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Relasi ke tabel users (Admin)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeris');
    }
};
