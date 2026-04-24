<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftar;

class CekStatusController extends Controller
{
    /**
     * Menampilkan halaman view cek status.
     */
    public function index()
    {
        return view('cek-status');
    }

    /**
     * API Endpoint: Dipanggil via AJAX dari cek-status.blade.php
     */
    public function getData(Request $request)
    {
        // Validasi input
        $request->validate(['kode_pendaftaran' => 'required|string']);

        // Cari data
        $pendaftar = Pendaftar::where('kode_pendaftaran', $request->kode_pendaftaran)->first();

        if (!$pendaftar) {
            return response()->json(['error' => 'Kode pendaftaran tidak ditemukan.'], 404);
        }

        // Return JSON agar bisa diolah JS
        return response()->json([
            'nama_lengkap' => $pendaftar->nama_lengkap,
            'kode_pendaftaran' => $pendaftar->kode_pendaftaran,
            'tempat_lahir' => $pendaftar->tempat_lahir,
            'tanggal_lahir' => \Carbon\Carbon::parse($pendaftar->tanggal_lahir)->format('d F Y'),
            'nomor_porsi_haji' => $pendaftar->nomor_porsi_haji,
            'tanggal_daftar' => $pendaftar->created_at->format('d F Y, H:i') . ' WIB',
            'status_pendaftaran' => $pendaftar->status_pendaftaran, // Diterima / Ditolak / Menunggu Verifikasi
            'alasan_penolakan' => $pendaftar->alasan_penolakan,
        ]);
    }
}