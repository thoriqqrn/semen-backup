<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Kecamatan;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            'Balongpanggang', 'Benjeng', 'Bungah', 'Cerme', 'Driyorejo', 'Duduk Sampeyan',
            'Dukun', 'Gresik', 'Kebomas', 'Kedamean', 'Manyar', 'Menganti',
            'Panceng', 'Sangkapura', 'Sidayu', 'Tambak', 'Ujung Pangkah', 'Wringinanom'
        ];
        foreach ($kecamatans as $kecamatan) {
            Kecamatan::create(['nama_kecamatan' => $kecamatan]);
        }
    }
}