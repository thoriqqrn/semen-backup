<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    // Field yang boleh diisi mass assignment untuk CRUD wilayah
    protected $fillable = [
        'kecamatan_id',
        'nama_kelurahan',
        'ring_status',
    ];

    /**
     * Mendefinisikan relasi bahwa setiap Kelurahan "milik" satu Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}