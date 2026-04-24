<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    // Tambahkan properti ini untuk mengizinkan mass update di controller
    protected $fillable = ['ring_status'];

    /**
     * Mendefinisikan relasi bahwa setiap Kelurahan "milik" satu Kecamatan.
     */
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}