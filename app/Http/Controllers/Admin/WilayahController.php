<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WilayahController extends Controller
{
    public function index()
    {
        $kecamatans = Kecamatan::orderBy('nama_kecamatan')->get();
        $kelurahans = Kelurahan::with('kecamatan')->orderBy('kecamatan_id')->get();
        return view('admin.wilayah.index', compact('kelurahans', 'kecamatans'));
    }

    public function update(Request $request)
    {
        // Loop melalui semua input yang dikirim dari form
        foreach (($request->rings ?? []) as $kelurahan_id => $ring_status) {
            Kelurahan::where('id', $kelurahan_id)
                     ->update(['ring_status' => empty($ring_status) ? null : $ring_status]);
        }
        return back()->with('success', 'Pengaturan RING Wilayah berhasil diperbarui!');
    }

    public function storeKecamatan(Request $request)
    {
        $validated = $request->validate([
            'nama_kecamatan' => 'required|string|max:100|unique:kecamatans,nama_kecamatan',
        ]);

        Kecamatan::create([
            'nama_kecamatan' => $validated['nama_kecamatan'],
        ]);

        return back()->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function updateKecamatan(Request $request, Kecamatan $kecamatan)
    {
        $validated = $request->validate([
            'nama_kecamatan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('kecamatans', 'nama_kecamatan')->ignore($kecamatan->id),
            ],
        ]);

        $kecamatan->nama_kecamatan = $validated['nama_kecamatan'];
        $kecamatan->save();

        return back()->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroyKecamatan(Kecamatan $kecamatan)
    {
        $kecamatan->delete();

        return back()->with('success', 'Kecamatan berhasil dihapus.');
    }

    public function storeKelurahan(Request $request)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'nama_kelurahan' => 'required|string|max:100',
            'ring_status' => 'nullable|integer|min:1',
        ]);

        Kelurahan::create([
            'kecamatan_id' => $validated['kecamatan_id'],
            'nama_kelurahan' => $validated['nama_kelurahan'],
            'ring_status' => $validated['ring_status'] ?? null,
        ]);

        return back()->with('success', 'Kelurahan/Desa berhasil ditambahkan.');
    }

    public function updateKelurahan(Request $request, Kelurahan $kelurahan)
    {
        $validated = $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'nama_kelurahan' => 'required|string|max:100',
            'ring_status' => 'nullable|integer|min:1',
        ]);

        $kelurahan->kecamatan_id = $validated['kecamatan_id'];
        $kelurahan->nama_kelurahan = $validated['nama_kelurahan'];
        $kelurahan->ring_status = $validated['ring_status'] ?? null;
        $kelurahan->save();

        return back()->with('success', 'Kelurahan/Desa berhasil diperbarui.');
    }

    public function destroyKelurahan(Kelurahan $kelurahan)
    {
        $kelurahan->delete();

        return back()->with('success', 'Kelurahan/Desa berhasil dihapus.');
    }
}