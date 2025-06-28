<?php

use App\Models\Cafe;
use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\Rangking;
use Livewire\Volt\Component;

new class extends Component {
    public function with()
    {
        $cafes = Cafe::all();
        $kriterias = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');

        $maxValues = [];
        $minValues = [];

        foreach ($kriterias as $kriteria) {
            $maxValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->max('value') ?: 1;
            $minValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->min('value') ?: 1;
        }

        $data = [];

        foreach ($cafes as $cafe) {
            $sawTotal = 0;
            $rincian = [];

            foreach ($kriterias as $kriteria) {
                $alt = Alternatif::where('cafe_id', $cafe->id)->where('kriteria_id', $kriteria->id)->first();

                $nilai = $alt?->value ?? 0;
                $bobot = $kriteria->bobot;
                $bobotWP = $totalBobot ? round($bobot / $totalBobot, 4) : 0;

                $max = $maxValues[$kriteria->id];
                $min = $minValues[$kriteria->id];

                // SAW normalisasi
                if ($kriteria->kategori === 'Benefit') {
                    $sawNorm = $nilai / ($max ?: 1);
                } else {
                    $sawNorm = $nilai ? $min / $nilai : 0;
                }

                $sawTerbobot = $sawNorm * $bobotWP;
                $sawTotal += $sawTerbobot;

                $rincian[] = [
                    'kriteria' => $kriteria->name,
                    'kategori' => $kriteria->kategori,
                    'bobot' => $bobot,
                    'bobot_wp' => $bobotWP,
                    'nilai' => $nilai,
                    'max' => $max,
                    'min' => $min,
                    'saw_normal' => round($sawNorm, 4),
                    'saw_terbobot' => round($sawTerbobot, 4),
                ];
            }

            $peringkat = Rangking::where('cafe_id', $cafe->id)->first()?->peringkat ?? '-';

            $data[] = [
                'cafe' => $cafe->name,
                'rincian' => $rincian,
                'saw_total' => round($sawTotal, 4),
                'peringkat' => $peringkat,
                'total_bobot' => $totalBobot,
            ];
        }

        return ['hasil' => $data];
    }
};
?>
<div>
    <x-header title="Detail Perhitungan SAW" separator />

    @foreach ($hasil as $data)
        <x-card class="mb-6">
            <h2 class="text-lg font-bold text-indigo-700 mb-2">Cafe: {{ $data['cafe'] }}</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg text-sm text-left">
                    <thead class="bg-gray-100 text-gray-800">
                        <tr>
                            <th class="px-4 py-2">Kriteria</th>
                            <th class="px-4 py-2">Kategori</th>
                            <th class="px-4 py-2 text-center">Bobot</th>
                            <th class="px-4 py-2 text-center">Total Bobot</th>
                            <th class="px-4 py-2 text-center">Bobot WP</th>
                            <th class="px-4 py-2 text-center">Nilai</th>
                            <th class="px-4 py-2 text-center">Max</th>
                            <th class="px-4 py-2 text-center">Min</th>
                            <th class="px-4 py-2 text-center">SAW Norm</th>
                            <th class="px-4 py-2 text-center">SAW × Bobot WP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($data['rincian'] as $r)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $r['kriteria'] }}</td>
                                <td class="px-4 py-2">{{ $r['kategori'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $r['bobot'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $data['total_bobot'] }}</td>
                                <td class="px-4 py-2 text-center text-blue-700">{{ $r['bobot_wp'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $r['nilai'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $r['max'] }}</td>
                                <td class="px-4 py-2 text-center">{{ $r['min'] }}</td>
                                <td class="px-4 py-2 text-center text-green-700">{{ $r['saw_normal'] }}</td>
                                <td class="px-4 py-2 text-center text-indigo-700">{{ $r['saw_terbobot'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <p><strong>Total SAW:</strong> {{ $data['saw_total'] }}</p>
                <p><strong>Peringkat:</strong> {{ $data['peringkat'] }}</p>
            </div>
        </x-card>
    @endforeach
</div>
