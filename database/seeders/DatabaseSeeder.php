<?php

// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class, // Panggil AdminSeeder di sini
            // ... (Seeder lainnya jika ada) ...
            SettingSeeder::class,
            KecamatanSeeder::class, // Tambahkan ini
            KelurahanSeeder::class, // Tambahkan ini
        ]);

    }
}
