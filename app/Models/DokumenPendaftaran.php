<?php

// app/Models/DokumenPendaftaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPendaftaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftar_id',
        'file_ktp_path',
        'file_kk_path',
        'file_akta_path',
        'file_nikah_path',
        'file_ijazah_path',
        'file_bpih_path',
        'file_spph_path',
        'file_foto_path',
        'file_paspor_path',
        'file_booster1_path',
        'file_booster2_path',
    ];

    // Relasi BelongsTo dengan Pendaftar
    public function pendaftar()
    {
        return $this->belongsTo(Pendaftar::class);
    }
}
