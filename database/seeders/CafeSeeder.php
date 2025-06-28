<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CafeSeeder extends Seeder
{
    public function run()
    {
        $cafes = [
            ['name' => 'Kopi Studio 24, JL.soekarno hatta', 'sosmed' => null],
            ['name' => 'piskip', 'sosmed' => null],
            ['name' => 'swara malang', 'sosmed' => null],
            ['name' => 'Warunk WOW KWB - Malang', 'sosmed' => null],
            ['name' => 'Fore Coffee - Jl. Soekarno Hatta, Malang', 'sosmed' => null],
            ['name' => 'Semusim Café', 'sosmed' => null],
            ['name' => 'Nakoa Cafe dinoyo', 'sosmed' => null],
            ['name' => 'Rilo Coffee and Space', 'sosmed' => null],
            ['name' => 'Dialoogi Space & Coffee', 'sosmed' => null],
            ['name' => 'Nakoa Cafe Suhat - Puncak Borobudur', 'sosmed' => null],
            ['name' => 'Kopi Calf Signature Soehat Malang', 'sosmed' => null],
            ['name' => 'Teras Kota Suhat Malang', 'sosmed' => null],
            ['name' => 'Pesenkopi Plus Dinoyo', 'sosmed' => null],
            ['name' => 'KAF Café', 'sosmed' => null],
            ['name' => 'Kopi Kenangan - Ruko Soekarno Hatta', 'sosmed' => null],
            ['name' => '7 Seven Chicken', 'sosmed' => null],
            ['name' => 'Tomoro Coffee - Suhat', 'sosmed' => null],
            ['name' => 'CW COFFEE & EATERY Kendal Sari', 'sosmed' => null],
            ['name' => 'Kopi Handall', 'sosmed' => null],
            ['name' => 'Distrik Coffee Roaster', 'sosmed' => null],
            ['name' => 'Roketto Coffee & Co', 'sosmed' => null],
        ];


        foreach ($cafes as &$cafe) {
            $cafe['created_at'] = Carbon::now()->subDays(rand(0, 30));
            $cafe['updated_at'] = Carbon::now()->subDays(rand(0, 30));
        }

        DB::table('cafes')->insert($cafes);
    }
}
