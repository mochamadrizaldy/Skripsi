<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Kriteria;

class SubKriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Kecepatan internet' => [
                ['name' => '≥ 50 Mbps', 'nilai' => 5],
                ['name' => '30 Mbps s.d. < 50 Mbps', 'nilai' => 4],
                ['name' => '10 Mbps s.d. < 30 Mbps', 'nilai' => 3],
                ['name' => '5 Mbps s.d. < 10 Mbps', 'nilai' => 2],
                ['name' => '< 5 Mbps', 'nilai' => 1],
            ],
            'Ketersediaan Colokan Listrik' => [
                ['name' => '≥ 10 colokan', 'nilai' => 5],
                ['name' => '7 s.d. < 10 colokan', 'nilai' => 4],
                ['name' => '4 s.d. < 7 colokan', 'nilai' => 3],
                ['name' => '2 s.d. < 4 colokan', 'nilai' => 2],
                ['name' => '< 2 colokan', 'nilai' => 1],
            ],
            'Harga' => [
                ['name' => '≤ Rp10.000', 'nilai' => 5],
                ['name' => '> Rp10.000 s.d. ≤ Rp20.000', 'nilai' => 4],
                ['name' => '> Rp20.000 s.d. ≤ Rp30.000', 'nilai' => 3],
                ['name' => '> Rp30.000 s.d. ≤ Rp40.000', 'nilai' => 2],
                ['name' => '> Rp40.000', 'nilai' => 1],
            ],
            'Suasana' => [
                ['name' => 'Sangat nyaman', 'nilai' => 5],
                ['name' => 'Nyaman', 'nilai' => 4],
                ['name' => 'Cukup', 'nilai' => 3],
                ['name' => 'Tidak nyaman', 'nilai' => 2],
                ['name' => 'Sangat tidak nyaman', 'nilai' => 1],
            ],
            'Jam Operasional Cafe' => [
                ['name' => '≥ 24jam', 'nilai' => 5],
                ['name' => '18 s.d. < 24jam', 'nilai' => 4],
                ['name' => '12 s.d. < 18jam', 'nilai' => 3],
                ['name' => '6 s.d. < 12jam', 'nilai' => 2],
                ['name' => '< 6jam', 'nilai' => 1],
            ],
            'Pelayanan' => [
                ['name' => 'Sangat ramah', 'nilai' => 5],
                ['name' => 'Ramah', 'nilai' => 4],
                ['name' => 'Cukup', 'nilai' => 3],
                ['name' => 'Tidak ramah', 'nilai' => 2],
                ['name' => 'Sangat tidak ramah', 'nilai' => 1],
            ],
            'Jarak' => [
                ['name' => '< 1 km', 'nilai' => 5],
                ['name' => '1 s.d. 2 km', 'nilai' => 4],
                ['name' => '2 s.d. 3 km', 'nilai' => 3],
                ['name' => '4 km', 'nilai' => 2],
                ['name' => '>4 km', 'nilai' => 1],
            ],
            'Variasi Menu' => [
                ['name' => '≥ 30 jenis menu', 'nilai' => 5],
                ['name' => '20 s.d. < 30 jenis menu', 'nilai' => 4],
                ['name' => '10 s.d. < 20 jenis menu', 'nilai' => 3],
                ['name' => '5 s.d. < 10 jenis menu', 'nilai' => 2],
                ['name' => '< 5 jenis menu', 'nilai' => 1],
            ],
        ];

        foreach ($data as $kriteriaName => $subKriterias) {
            $kriteria = Kriteria::where('name', $kriteriaName)->first();
            if (!$kriteria) continue;

            foreach ($subKriterias as $sub) {
                DB::table('sub_kriterias')->insert([
                    'name' => $sub['name'] . ' - ' . $kriteriaName,
                    'kriteria_id' => $kriteria->id,
                    'nilai' => $sub['nilai'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}
