<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $kelurahans = Kelurahan::with('kecamatan')->orderBy('kecamatan_id')->get();
        return view('admin.wilayah.index', compact('kelurahans'));
    }

    public function update(Request $request)
    {
        // Loop melalui semua input yang dikirim dari form
        foreach ($request->rings as $kelurahan_id => $ring_status) {
            Kelurahan::where('id', $kelurahan_id)
                     ->update(['ring_status' => empty($ring_status) ? null : $ring_status]);
        }
        return back()->with('success', 'Pengaturan RING Wilayah berhasil diperbarui!');
    }
}