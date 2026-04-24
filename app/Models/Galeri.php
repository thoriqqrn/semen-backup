<?php

// app/Models/Galeri.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'tahun_kegiatan', // <--- Tambahkan ini
        'tipe',
        'file_path',
        'thumbnail_path',
        'is_published',
        'admin_id',
    ];

    // Relasi BelongsTo dengan Admin (User)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
