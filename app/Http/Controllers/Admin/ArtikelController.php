<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Untuk generate slug
use Illuminate\Validation\Rule; // Untuk validasi unik saat update

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::latest()->get();
        return view('admin.artikel.index', compact('artikels'));
    }

    public function create()
    {
        return view('admin.artikel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255|unique:artikels,judul',
            'excerpt' => 'required|string|max:500',
            'konten' => 'required|string',
            'gambar_utama' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
            'is_published' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('gambar_utama')) {
            $imagePath = $request->file('gambar_utama')->store('artikel', 'public');
        }

        Artikel::create([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul, '-'),
            'excerpt' => $request->excerpt,
            'konten' => $request->konten,
            'gambar_utama_path' => $imagePath,
            'is_published' => $request->has('is_published'),
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dibuat!');
    }

    public function edit(Artikel $artikel)
    {
        return view('admin.artikel.edit', compact('artikel'));
    }

    public function update(Request $request, Artikel $artikel)
    {
        $request->validate([
            'judul' => ['required','string','max:255', Rule::unique('artikels')->ignore($artikel->id)],
            'excerpt' => 'required|string|max:500',
            'konten' => 'required|string',
            'gambar_utama' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $imagePath = $artikel->gambar_utama_path;
        if ($request->hasFile('gambar_utama')) {
            // Hapus gambar lama jika ada
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('gambar_utama')->store('artikel', 'public');
        }

        $artikel->update([
            'judul' => $request->judul,
            'slug' => Str::slug($request->judul, '-'),
            'excerpt' => $request->excerpt,
            'konten' => $request->konten,
            'gambar_utama_path' => $imagePath,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil diperbarui!');
    }

    public function destroy(Artikel $artikel)
    {
        // Hapus gambar dari storage
        if ($artikel->gambar_utama_path) {
            Storage::disk('public')->delete($artikel->gambar_utama_path);
        }
        $artikel->delete();

        return redirect()->route('admin.artikel.index')->with('success', 'Artikel berhasil dihapus!');
    }
}