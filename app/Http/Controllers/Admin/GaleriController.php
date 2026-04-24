<?php

// app/Http/Controllers/Admin/GaleriController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;
use Illuminate\Support\Facades\Storage; // Untuk menghapus file
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan admin_id

class GaleriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $galeris = Galeri::orderBy('tahun_kegiatan', 'desc')->orderBy('created_at', 'desc')->get();
        return view('admin.galeri.index', compact('galeris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.galeri.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_kegiatan' => 'required|integer|min:1900|max:' . (date('Y') + 5), // Tahun saat ini + 5
            'tipe' => 'required|in:foto,video',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // Max 20MB (20480 KB)
            'is_published' => 'boolean',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('galeri', 'public'); // Simpan di storage/app/public/galeri
        }

        Galeri::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_kegiatan' => $request->tahun_kegiatan,
            'tipe' => $request->tipe,
            'file_path' => $filePath,
            'is_published' => $request->has('is_published'), // checkbox
            'admin_id' => Auth::id(), // Ambil ID admin yang sedang login
        ]);

        return redirect()->route('admin.galeri.index')->with('success', 'Item galeri berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Galeri $galeri)
    {
        return view('admin.galeri.edit', compact('galeri'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Galeri $galeri)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tahun_kegiatan' => 'required|integer|min:1900|max:' . (date('Y') + 5),
            'tipe' => 'required|in:foto,video',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:20480', // file opsional saat update
            'is_published' => 'boolean',
        ]);

        $filePath = $galeri->file_path; // Ambil path lama
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($galeri->file_path) {
                Storage::disk('public')->delete($galeri->file_path);
            }
            $file = $request->file('file');
            $filePath = $file->store('galeri', 'public');
        }

        $galeri->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tahun_kegiatan' => $request->tahun_kegiatan,
            'tipe' => $request->tipe,
            'file_path' => $filePath,
            'is_published' => $request->has('is_published'),
            // admin_id tidak diubah saat update
        ]);

        return redirect()->route('admin.galeri.index')->with('success', 'Item galeri berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Galeri $galeri)
    {
        // Hapus file dari storage
        if ($galeri->file_path) {
            Storage::disk('public')->delete($galeri->file_path);
        }
        $galeri->delete();

        return redirect()->route('admin.galeri.index')->with('success', 'Item galeri berhasil dihapus!');
    }
}