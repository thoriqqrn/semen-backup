<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Atur nilai default slot haji jika belum ada
        Setting::updateOrCreate(
            ['key' => 'max_slots'],
            ['value' => '50'] // Atur nilai default awal, misal 50
        );
        
        // Tambah setting untuk kuota Ring 1
        Setting::updateOrCreate(
            ['key' => 'kuota_ring1'],
            ['value' => '30'] // Default 30 kursi untuk Ring 1
        );
        
        // Tambah setting untuk kuota Umum (luar Ring 1)
        Setting::updateOrCreate(
            ['key' => 'kuota_umum'],
            ['value' => '20'] // Default 20 kursi untuk Umum
        );
        
        // Setting max_porsi tetap ada
        Setting::updateOrCreate(
            ['key' => 'max_porsi'],
            ['value' => '999999'] // Default porsi tertinggi
        );
    }
}