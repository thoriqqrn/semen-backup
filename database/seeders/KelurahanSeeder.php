<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelurahan;
use Illuminate\Support\Facades\Schema; // <-- PENTING: Tambahkan ini

class KelurahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key untuk sementara
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel dengan aman
        Kelurahan::truncate();

        // 3. Nyalakan kembali pengecekan Foreign Key
        Schema::enableForeignKeyConstraints();

        // 4. Isi data RING 1 sesuai daftar dari client
        // KECAMATAN GRESIK (ID: 8)
        Kelurahan::create(['kecamatan_id' => 8, 'nama_kelurahan' => 'Kramat Inggil', 'ring_status' => 1]);
        Kelurahan::create(['kecamatan_id' => 8, 'nama_kelurahan' => 'Sidomoro', 'ring_status' => 1]);
        
        // KECAMATAN KEBOMAS (ID: 9)
        Kelurahan::create(['kecamatan_id' => 9, 'nama_kelurahan' => 'Gending', 'ring_status' => 1]);
        Kelurahan::create(['kecamatan_id' => 9, 'nama_kelurahan' => 'Kawisanyar', 'ring_status' => 1]);
        Kelurahan::create(['kecamatan_id' => 9, 'nama_kelurahan' => 'Kembangan', 'ring_status' => 1]);
        Kelurahan::create(['kecamatan_id' => 9, 'nama_kelurahan' => 'Singosari', 'ring_status' => 1]);

        // KECAMATAN MANYAR (ID: 11)
        Kelurahan::create(['kecamatan_id' => 11, 'nama_kelurahan' => 'Segoro Madu', 'ring_status' => 1]);
        Kelurahan::create(['kecamatan_id' => 11, 'nama_kelurahan' => 'Sidorukun', 'ring_status' => 1]);

        // KECAMATAN MENGANTI (ID: 12)
        Kelurahan::create(['kecamatan_id' => 12, 'nama_kelurahan' => 'Suci', 'ring_status' => 1]);
        
        // 5. Isi data lain (non-ring)
        // KECAMATAN GRESIK (ID: 8)
        Kelurahan::create(['kecamatan_id' => 8, 'nama_kelurahan' => 'Bedilan']);
        Kelurahan::create(['kecamatan_id' => 8, 'nama_kelurahan' => 'Gapurosukolilo']);
        Kelurahan::create(['kecamatan_id' => 8, 'nama_kelurahan' => 'Karangpoh']);

        // KECAMATAN KEBOMAS (ID: 9)
        Kelurahan::create(['kecamatan_id' => 9, 'nama_kelurahan' => 'Indro']);

        // KECAMATAN MANYAR (ID: 11)
        Kelurahan::create(['kecamatan_id' => 11, 'nama_kelurahan' => 'Manyarejo']);
        Kelurahan::create(['kecamatan_id' => 11, 'nama_kelurahan' => 'Manyarsidorukun']);
        Kelurahan::create(['kecamatan_id' => 11, 'nama_kelurahan' => 'Peganden']);

        // Tambahkan data kelurahan lain yang belum masuk jika perlu...
    }
}