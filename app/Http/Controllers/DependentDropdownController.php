<?php
namespace App\Http\Controllers;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;

class DependentDropdownController extends Controller
{
    public function getKecamatan() {
        $kecamatans = Kecamatan::all();
        return response()->json($kecamatans);
    }

    public function getKelurahan(Request $request) {
        $kelurahans = Kelurahan::where('kecamatan_id', $request->kecamatan_id)->get();
        return response()->json($kelurahans);
    }
}