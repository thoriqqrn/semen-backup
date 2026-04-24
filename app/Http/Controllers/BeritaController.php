<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artikel;

class BeritaController extends Controller
{
    public function index()
    {
        // Ambil semua artikel yang sudah dipublish, urutkan dari yang terbaru,
        // dan tampilkan 9 artikel per halaman.
        $articles = Artikel::where('is_published', true)
                            ->latest() // Ini singkatan dari orderBy('created_at', 'desc')
                            ->paginate(9); // Ubah angka 9 jika ingin jumlah artikel per halaman berbeda

        return view('berita', compact('articles'));
    }
    public function show(Artikel $artikel)
    {
        // Berkat Route Model Binding, Laravel sudah otomatis
        // memberikan kita data artikel yang benar ($artikel).
        // Kita tinggal menampilkannya di view.
        return view('berita-detail', compact('artikel'));
    }
}