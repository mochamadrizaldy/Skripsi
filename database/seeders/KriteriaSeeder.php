<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kriterias = [
            ['name' => 'Kecepatan internet', 'kategori' => 'Benefit', 'bobot' => 31, 'keterangan' => 'C1', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Ketersediaan Colokan Listrik', 'kategori' => 'Benefit', 'bobot' => 29, 'keterangan' => 'C2', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Harga', 'kategori' => 'Cost', 'bobot' => 27, 'keterangan' => 'C3', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Suasana', 'kategori' => 'Benefit', 'bobot' => 26, 'keterangan' => 'C4', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Jam Operasional cafe', 'kategori' => 'Benefit', 'bobot' => 24, 'keterangan' => 'C5', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Pelayanan', 'kategori' => 'Benefit', 'bobot' => 17, 'keterangan' => 'C6', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Jarak', 'kategori' => 'Cost', 'bobot' => 16, 'keterangan' => 'C7', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
            ['name' => 'Variasi Menu', 'kategori' => 'Benefit', 'bobot' => 13, 'keterangan' => 'C8', 'created_at' => Carbon::now()->subDays(rand(0, 30)), 'updated_at' => Carbon::now()->subDays(rand(0, 30))],
        ];

        foreach ($kriterias as $kriteria) {
            DB::table('kriterias')->insert($kriteria);
        }
    }
}
