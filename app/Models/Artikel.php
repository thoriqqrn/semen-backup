<?php

// app/Models/Artikel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'excerpt', // <-- Tambahkan ini
        'konten',
        'gambar_utama_path',
        'is_published',
        'admin_id',
    ];

    // Relasi BelongsTo dengan Admin (User)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
