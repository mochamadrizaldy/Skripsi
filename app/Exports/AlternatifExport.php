<?php

namespace App\Exports;

use App\Models\Cafe;
use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\Rangking;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlternatifExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $cafes = Cafe::all();
        $kriterias = Kriteria::all();

        // Total bobot untuk normalisasi WP
        $totalBobot = $kriterias->sum('bobot');

        // Cari max dan min tiap kriteria untuk normalisasi SAW
        $maxValues = [];
        $minValues = [];

        foreach ($kriterias as $kriteria) {
            $maxValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->max('value') ?: 1;
            $minValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->min('value') ?: 1;
        }

        $rows = collect();

        foreach ($cafes as $cafe) {
            $row = [
                'Cafe' => $cafe->name,
            ];

            $totalScore = 0;

            foreach ($kriterias as $kriteria) {
                $alt = Alternatif::where('cafe_id', $cafe->id)
                    ->where('kriteria_id', $kriteria->id)
                    ->first();

                $nilai = $alt ? $alt->value : 0;

                // Bobot WP (normalisasi)
                $bobotWP = $totalBobot ? round($kriteria->bobot / $totalBobot, 4) : 0;

                // Normalisasi SAW
                if ($kriteria->kategori === 'Benefit') {
                    $normal = $maxValues[$kriteria->id] ? $nilai / $maxValues[$kriteria->id] : 0;
                } else { // Cost
                    $normal = $nilai ? $minValues[$kriteria->id] / $nilai : 0;
                }

                $terbobot = $normal * $bobotWP;
                $totalScore += $terbobot;

                // Masukkan nilai terbobot ke kolom kriteria (rounded 4 decimal)
                $row[$kriteria->name] = round($terbobot, 4);
            }

            // Preferensi = total skor SAW
            $row['Preferensi'] = round($totalScore, 4);

            // Peringkat dari tabel Rangking
            $ranking = Rangking::where('cafe_id', $cafe->id)->first();
            $row['Peringkat'] = $ranking ? $ranking->peringkat : '-';

            $rows->push($row);
        }

        return $rows;
    }

    public function headings(): array
    {
        $kriterias = Kriteria::pluck('name')->toArray();

        return array_merge(['Cafe'], $kriterias, ['Preferensi', 'Peringkat']);
    }
}
