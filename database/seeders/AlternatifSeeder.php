<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AlternatifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Format kriteria berdasarkan urutan kolom
        $kriteriaIds = DB::table('kriterias')->pluck('id')->toArray();

        // Pastikan ID cafe sesuai urutan data yang diinput
        $cafes = [
            [4, 5, 4, 3, 5, 3, 5, 5],
            [3, 5, 4, 3, 3, 5, 4, 5],
            [3, 5, 4, 3, 5, 4, 4, 5],
            [5, 5, 3, 4, 5, 4, 3, 5],
            [5, 5, 2, 4, 3, 5, 4, 5],
            [5, 5, 4, 4, 4, 4, 4, 5],
            [3, 5, 3, 3, 3, 4, 5, 4],
            [3, 5, 4, 4, 2, 3, 5, 4],
            [1, 5, 3, 5, 3, 5, 4, 5],
            [2, 5, 3, 4, 3, 5, 3, 4],
            [3, 5, 3, 4, 4, 4, 4, 5],
            [2, 4, 3, 3, 3, 4, 4, 5],
            [5, 5, 3, 4, 5, 4, 3, 4],
            [5, 5, 3, 5, 4, 5, 3, 5],
            [3, 4, 3, 3, 5, 4, 5, 5],
            [3, 5, 4, 4, 5, 3, 3, 4],
            [4, 5, 3, 4, 3, 5, 4, 5],
            [5, 5, 3, 4, 5, 4, 4, 5],
            [2, 5, 2, 5, 3, 5, 4, 5],
            [4, 5, 3, 5, 3, 5, 3, 5],
            [5, 4, 3, 4, 5, 3, 4, 5],
        ];

        $cafeIds = DB::table('cafes')->pluck('id')->toArray();

        foreach ($cafeIds as $index => $cafeId) {
            foreach ($kriteriaIds as $kriteriaIndex => $kriteriaId) {
                DB::table('alternatifs')->insert([
                    'cafe_id' => $cafeId,
                    'kriteria_id' => $kriteriaId,
                    'value' => $cafes[$index][$kriteriaIndex],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
