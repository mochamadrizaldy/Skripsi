<?php

use App\Models\Alternatif;
use App\Models\Cafe;
use App\Models\Kriteria;
use App\Models\Rangking;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlternatifExport;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];
    public int $perPage = 10;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filter direset.', position: 'toast-top');
    }

    public function delete($cafe_id): void
    {
        $cafe = Cafe::find($cafe_id);
        if (!$cafe) {
            $this->error("Cafe tidak ditemukan.", position: 'toast-top');
            return;
        }
        Alternatif::where('cafe_id', $cafe_id)->delete();
        $this->warning("Alternatif untuk Cafe {$cafe->name} dihapus.", position: 'toast-top');
    }

    public function calculateSAW()
    {
        Rangking::truncate();

        $kriterias = Kriteria::all();
        $cafes = Cafe::all();

        // Hitung total bobot untuk normalisasi WP
        $totalBobot = $kriterias->sum('bobot');

        // Hitung bobot normalisasi WP untuk semua kriteria
        $normalizedWeights = [];
        foreach ($kriterias as $kriteria) {
            $normalizedWeights[$kriteria->id] = $kriteria->bobot / $totalBobot;
        }

        // Ambil nilai maksimum dan minimum tiap kriteria
        $maxValues = [];
        $minValues = [];

        foreach ($kriterias as $kriteria) {
            $maxValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->max('value') ?: 1;
            $minValues[$kriteria->id] = Alternatif::where('kriteria_id', $kriteria->id)->min('value') ?: 1;
        }

        $results = [];

        foreach ($cafes as $cafe) {
            $scoreSAW = 0;

            foreach ($kriterias as $kriteria) {
                $alt = Alternatif::where('cafe_id', $cafe->id)
                    ->where('kriteria_id', $kriteria->id)
                    ->first();

                $nilai = $alt ? $alt->value : 0;

                // --- Normalisasi SAW ---
                if ($kriteria->kategori == 'Benefit') {
                    $normalSAW = $nilai / $maxValues[$kriteria->id];
                } elseif ($kriteria->kategori == 'Cost') {
                    $normalSAW = $nilai != 0 ? $minValues[$kriteria->id] / $nilai : 0;
                } else {
                    $normalSAW = 0;
                }

                // Gunakan bobot yang sudah dinormalisasi dengan WP
                $bobotNormalWP = $normalizedWeights[$kriteria->id];
                $scoreSAW += $normalSAW * $bobotNormalWP;
            }

            $results[] = [
                'cafe_id' => $cafe->id,
                'score_saw' => round($scoreSAW, 4),
            ];
        }

        // Urutkan berdasarkan skor SAW untuk peringkat
        usort($results, fn($a, $b) => $b['score_saw'] <=> $a['score_saw']);

        $peringkat = 1;
        foreach ($results as $result) {
            Rangking::create([
                'cafe_id' => $result['cafe_id'],
                'score' => $result['score_saw'], // hasil akhir menggunakan SAW
                'peringkat' => $peringkat++,
            ]);
        }

        $this->success('Perhitungan SAW dengan bobot WP selesai dan disimpan.', position: 'toast-top');
    }

    public function headers(): array
    {
        $headers = [
            ['key' => 'name', 'label' => 'Cafe'],
        ];

        foreach (Kriteria::all() as $kriteria) {
            $headers[] = [
                'key' => "kriteria_{$kriteria->id}",
                'label' => $kriteria->name,
                'class' => 'text-center',
            ];
        }

        return $headers;
    }

    public function alternatif(): LengthAwarePaginator
    {
        $cafes = Cafe::query()
            ->with(['alternatifs.kriteria'])
            ->when(
                $this->search,
                fn($q) => $q->where('name', 'like', "%{$this->search}%")
            )
            ->paginate($this->perPage);

        return $cafes->through(function ($cafe) {
            $data = [
                'id' => $cafe->id,
                'cafe_id' => $cafe->id,
                'name' => $cafe->name,
                'cafe_name' => $cafe->name,
            ];

            foreach ($cafe->alternatifs as $alt) {
                $data["kriteria_{$alt->kriteria_id}"] = $alt->value;
            }

            return (object) $data;
        });
    }

    public function canCreate(): bool
    {
        $totalCafes = Cafe::count();
        // Hitung cafe yang punya alternatif unik (cafe_id)
        $cafesWithAlternatif = Alternatif::distinct('cafe_id')->count('cafe_id');

        return $cafesWithAlternatif < $totalCafes;
    }

    public function with(): array
    {
        return [
            'alternatif' => $this->alternatif(),
            'headers' => $this->headers(),
            'cafes' => Cafe::all(),
            'kriterias' => Kriteria::all(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
            'canCreate' => $this->canCreate(),
        ];
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property !== '') {
            $this->resetPage();
        }
    }
    
    public function export()
    {
        return Excel::download(new AlternatifExport, 'alternatif.xlsx');
    }

};

?>
<div>
    <x-header title="Data Alternatif" separator progress-indicator>
        <x-slot:actions>
            @if($canCreate)
                <x-button label="Create" link="/alternatif/create" icon="o-plus" class="btn-primary" />
            @endif
            <x-button label="Hitung" @click="$wire.calculateSAW()" icon="o-plus" class="btn-secondary" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 mb-4 items-end">
        <div class="md:col-span-1">
            <x-select label="Show" :options="$pages" wire:model.live="perPage" class="w-15" />
        </div>
        <div class="md:col-span-7">
            <x-input placeholder="Search Cafe..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </div>
    </div>

    <x-card>
        <x-table :headers="$headers" :rows="$alternatif" :sort-by="$sortBy" with-pagination
            link="alternatif/{cafe_id}/edit">
            @scope('actions', $alternatif)
            <x-button icon="o-trash" wire:click.stop="delete({{ $alternatif->cafe_id }})"
                wire:confirm="Yakin ingin menghapus {{ $alternatif->cafe_name }}?" spinner
                class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    {{-- Drawer Filter --}}
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Tutup" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>