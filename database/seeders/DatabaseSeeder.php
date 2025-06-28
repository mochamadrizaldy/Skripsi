<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\JenisBarang;
use App\Models\SubKriteria;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(CountrySeeder::class);
        // $this->call(LanguageSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(KriteriaSeeder::class);
        $this->call(SubKriteriaSeeder::class);
        $this->call(CafeSeeder::class);
        $this->call(AlternatifSeeder::class);
        // $this->call(JenisBarangSeeder::class);
        // $this->call(SatuanSeeder::class);
        // $this->call(BarangSeeder::class);
        // $this->call(BarangMasukSeeder::class);
        // $this->call(BarangKeluarSeeder::class);
    }
}
