<?php

namespace App\Http\Controllers\Admin;

// Tambahkan use statement ini di atas
use App\Exports\PendaftarExport;
use App\Http\Controllers\Controller;
use App\Models\Pendaftar;
use App\Services\OcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PDF; // <-- INI UNTUK 'Str::contains'

class PendaftarController extends Controller
{
    /**
     * Menampilkan daftar semua pendaftar.
     */
    public function index()
    {
        // Urutkan berdasarkan waktu pendaftaran (siapa duluan daftar)
        $pendaftars = Pendaftar::with('kelurahan')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.pendaftar.index', compact('pendaftars'));
    }

    /**
     * Menampilkan detail satu pendaftar beserta dokumennya.
     */
    // HAPUS OcrService dari sini, dan hapus semua logika OCR
    public function show(Pendaftar $pendaftar)
    {
        $pendaftar->load('dokumen');
        // Ambil data OCR yang sudah di-decode dari database
        $ocrResult = $pendaftar->ocr_result;

        return view('admin.pendaftar.show', compact('pendaftar', 'ocrResult'));
    }

    /**
     * Mengupdate status dan catatan admin untuk seorang pendaftar.
     */
    public function update(Request $request, Pendaftar $pendaftar)
    {
        $request->validate([
            'status_pendaftaran' => 'required|string', // Validasi simpel
            'catatan_admin' => 'nullable|string',
        ]);
        $pendaftar->update($request->only(['status_pendaftaran', 'catatan_admin']));

        return back()->with('success', 'Catatan admin berhasil disimpan!');
    }

    /** Menampilkan pendaftar yang statusnya 'Diterima' */
    public function pesertaHaji()
    {
        $pendaftars = Pendaftar::where('status_pendaftaran', 'Diterima')->latest()->get();

        // Pastikan view ini ada: resources/views/admin/pendaftar/peserta-haji.blade.php
        return view('admin.pendaftar.peserta-haji', compact('pendaftars'));
    }

    /** Ekspor data ke Excel */
    public function exportExcel()
    {
        return Excel::download(new PendaftarExport, 'daftar-pendaftar-haji.xlsx');
    }

    /** Ekspor data ke PDF */
    public function exportPdf()
    {
        $pendaftars = Pendaftar::all();
        $pdf = PDF::loadView('pdf.pendaftar-list', compact('pendaftars'));

        return $pdf->download('daftar-pendaftar-haji.pdf');
    }

    /**
     * Verifikasi: TERIMA pendaftar (dengan catatan opsional)
     */
    public function verifikasiTerima(Request $request, Pendaftar $pendaftar)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $pendaftar->update([
            'status_pendaftaran' => 'diterima',
            'alasan_penolakan' => null, // Clear alasan jika sebelumnya ditolak
            'catatan_admin' => $request->catatan_admin
        ]);

        return back()->with('success', 'Pendaftar berhasil DITERIMA! Kartu pendaftaran dapat dicetak.');
    }

    /**
     * Verifikasi: TOLAK pendaftar
     */
    public function verifikasiTolak(Request $request, Pendaftar $pendaftar)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|min:10',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 10 karakter.',
        ]);

        $pendaftar->update([
            'status_pendaftaran' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        return back()->with('success', 'Pendaftar berhasil DITOLAK dengan alasan: ' . $request->alasan_penolakan);
    }

    /**
     * Halaman khusus untuk list Jamaah Haji yang sudah DITERIMA
     */
    public function jamaahDiterima()
    {
        $pendaftars = Pendaftar::with('kelurahan')
            ->where('status_pendaftaran', 'diterima')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.pendaftar.jamaah-diterima', compact('pendaftars'));
    }

    /**
     * Ubah status jamaah (untuk fleksibilitas jika ada kesalahan)
     */
    public function ubahStatus(Request $request, Pendaftar $pendaftar)
    {
        $request->validate([
            'status_baru' => 'required|in:menunggu,ditolak',
            'alasan' => 'required_if:status_baru,ditolak|nullable|string|min:10',
        ], [
            'status_baru.required' => 'Status baru wajib dipilih.',
            'alasan.required_if' => 'Alasan wajib diisi jika status diubah ke Ditolak.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        $updateData = [
            'status_pendaftaran' => $request->status_baru,
        ];

        if ($request->status_baru === 'ditolak') {
            $updateData['alasan_penolakan'] = $request->alasan;
        } else {
            // Jika diubah ke menunggu, clear alasan penolakan
            $updateData['alasan_penolakan'] = null;
        }

        $pendaftar->update($updateData);

        $statusLabel = $request->status_baru === 'menunggu' ? 'Menunggu Verifikasi' : 'Ditolak';
        return back()->with('success', "Status pendaftar berhasil diubah menjadi: {$statusLabel}");
    }
}
