<?php

// app/Models/Pendaftar.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftar extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pendaftaran',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kabupaten_kota',       // Tambahan baru
        'kecamatan_manual',     // Tambahan baru
        'kelurahan_manual',     // Tambahan baru
        'kelurahan_id',         // Sekarang bisa null
        'nomor_hp',
        'jenis_porsi',          // berangkat atau penggabungan
        'nomor_porsi_haji',
        'nomor_porsi_penggabungan', // Nomor porsi yang digabungkan
        'email',
        'status_pendaftaran',
        'alasan_penolakan',     // Kolom baru untuk alasan penolakan
        'catatan_admin',
        'ocr_result',
    ];

    // Relasi One-to-One dengan DokumenPendaftaran
    public function dokumen()
    {
        return $this->hasOne(DokumenPendaftaran::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    /**
     * Cek apakah pendaftar masuk kriteria Ring 1
     * Ring 1 = Hanya yang dari Kabupaten Gresik (punya kelurahan_id)
     */
    public function isRing1()
    {
        return !is_null($this->kelurahan_id);
    }

    /**
     * Get alamat lengkap dengan format yang rapi
     */
    public function getAlamatLengkap()
    {
        if ($this->kelurahan_id) {
            // Dari Gresik, ambil dari relasi
            $kelurahan = $this->kelurahan->nama_kelurahan ?? '-';
            $kecamatan = $this->kelurahan->kecamatan->nama_kecamatan ?? '-';
            return "{$this->alamat}, Kel. {$kelurahan}, Kec. {$kecamatan}, Kab. Gresik";
        } else {
            // Dari luar Gresik, ambil dari input manual
            return "{$this->alamat}, Kel. {$this->kelurahan_manual}, Kec. {$this->kecamatan_manual}, Kab. {$this->kabupaten_kota}";
        }
    }
}
