<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Galeri;

class GaleriController extends Controller
{
    /**
     * Menampilkan halaman galeri publik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Ambil semua tahun kegiatan yang unik dari item yang sudah dipublish.
        //    Ini untuk mengisi dropdown filter.
        $availableYears = Galeri::where('is_published', true)
                                ->select('tahun_kegiatan')
                                ->distinct()
                                ->orderBy('tahun_kegiatan', 'desc')
                                ->pluck('tahun_kegiatan');

        // 2. Tentukan tahun yang sedang aktif.
        //    Ambil dari URL (parameter ?tahun=xxxx), atau jika tidak ada,
        //    gunakan tahun terbaru dari daftar yang tersedia.
        $selectedYear = $request->input('tahun', $availableYears->first());

        // 3. Siapkan variabel galeris, defaultnya adalah collection kosong.
        $galeris = collect();

        // 4. HANYA JIKA ada tahun yang dipilih, jalankan query untuk mengambil gambar.
        //    Ini mencegah error jika tidak ada data galeri sama sekali.
        if ($selectedYear) {
            $galeris = Galeri::where('tahun_kegiatan', $selectedYear)
                             ->where('is_published', true) // Filter hanya yang statusnya publish
                             ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
                             ->get();
        }

        // 5. Kirim semua data yang diperlukan ke view.
        return view('galeri', compact('galeris', 'availableYears', 'selectedYear'));
    }
}