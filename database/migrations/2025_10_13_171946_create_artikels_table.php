<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_artikels_table.php

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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique(); // For clean URLs
            $table->longText('konten');
            $table->string('gambar_utama_path')->nullable();
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
        Schema::dropIfExists('artikels');
    }
};