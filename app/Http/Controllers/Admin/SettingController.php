<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $max_slots = Setting::where('key', 'max_slots')->first();
        $max_porsi = Setting::where('key', 'max_porsi')->first();
        $kuota_ring1 = Setting::where('key', 'kuota_ring1')->first();
        $kuota_umum = Setting::where('key', 'kuota_umum')->first();
        
        return view('admin.settings.index', compact('max_slots', 'max_porsi', 'kuota_ring1', 'kuota_umum'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'kuota_ring1' => 'required|integer|min:0',
            'kuota_umum' => 'required|integer|min:0',
            'max_porsi' => 'required|integer|min:1',
        ]);
        
        // Auto-calculate max_slots from ring1 + umum
        $max_slots = $request->kuota_ring1 + $request->kuota_umum;
        
        Setting::updateOrCreate(
            ['key' => 'max_slots'],
            ['value' => $max_slots]
        );
        
        Setting::updateOrCreate(
            ['key' => 'max_porsi'],
            ['value' => $request->max_porsi]
        );
        
        Setting::updateOrCreate(
            ['key' => 'kuota_ring1'],
            ['value' => $request->kuota_ring1]
        );
        
        Setting::updateOrCreate(
            ['key' => 'kuota_umum'],
            ['value' => $request->kuota_umum]
        );
        
        return back()->with('success', 'Pengaturan kuota berhasil disimpan! Total kuota: ' . $max_slots . ' kursi.');
    }
}