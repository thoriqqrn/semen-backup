<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftar;
use Illuminate\Http\Request;

class PesertaHajiController extends Controller
{
    /**
     * Menampilkan daftar peserta haji yang statusnya "Diterima".
     */
    public function index()
    {
        // Ambil data dari database HANYA yang statusnya 'Diterima'
        $pendaftars = Pendaftar::where('status_pendaftaran', 'Diterima')->latest()->get();
        
        // Arahkan ke view khusus peserta haji
        return view('admin.pendaftar.peserta-haji', compact('pendaftars'));
    }
}