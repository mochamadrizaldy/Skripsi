<?php

use App\Models\Cafe;
use App\Models\Kriteria;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public int $filter = 0;

    public array $filters = []; // filter sub_kriteria per kriteria

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];
    public int $perPage = 10;

    public function clear(): void
    {
        $this->reset(['search', 'filters']);
        $this->resetPage();
        $this->success('Filter direset.', position: 'toast-top');
    }

    public function headers(): array
    {
        $headers = [['key' => 'name', 'label' => 'Cafe']];

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
        $query = Cafe::query()
            ->with(['alternatifs.kriteria.sub_kriteria'])
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"));

        $paginator = $query->paginate($this->perPage);

        $krit = Kriteria::with('sub_kriteria')->get();

        // Manipulasi data: tampilkan cafe hanya jika sesuai semua filter
        $paginator->getCollection()->transform(function ($cafe) use ($krit) {
            $data = [
                'id' => $cafe->id,
                'cafe_id' => $cafe->id,
                'name' => $cafe->name,
                'cafe_name' => $cafe->name,
            ];

            $shouldDisplay = true;

            foreach ($krit as $kriteria) {
                $alt = $cafe->alternatifs->firstWhere('kriteria_id', $kriteria->id);
                if (!$alt) {
                    // Jika tidak ada alternatif untuk kriteria ini, asumsikan "-"
                    $data["kriteria_{$kriteria->id}"] = '-';
                    continue;
                }

                $sub = $kriteria->sub_kriteria->firstWhere('nilai', $alt->value);

                // Filter check: jika filter ada dan tidak cocok, skip cafe ini
                if (isset($this->filters[$kriteria->id]) && $this->filters[$kriteria->id] != ($sub->id ?? null)) {
                    $shouldDisplay = false;
                    break;
                }

                $data["kriteria_{$kriteria->id}"] = $sub->name ?? '-';
            }

            return $shouldDisplay ? (object) $data : null;
        });

        // Hapus cafe yang tidak lolos filter (null)
        $filtered = $paginator->getCollection()->filter();

        // Ganti collection hasil paginasi dengan yang sudah difilter
        $paginator->setCollection($filtered->values());

        return $paginator;
    }

    public function with(): array
    {
        return [
            'alternatif' => $this->alternatif(),
            'headers' => $this->headers(),
            'cafes' => Cafe::all(),
            'kriterias' => Kriteria::with('sub_kriteria')->get(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property !== '') {
            $this->resetPage();
        }
    }
};
?>
<div>
    <x-header title="Data Cafe" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer=true" icon="o-funnel" badge="{{ $filter }}"
                class="" />
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
        <x-table :headers="$headers" :rows="$alternatif" :sort-by="$sortBy" with-pagination>
        </x-table>
    </x-card>

    {{-- Drawer Filter --}}
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            @foreach ($kriterias as $kriteria)
                @php
                    $options = $kriteria->sub_kriteria
                        ->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'name' => $sub->name,
                            ];
                        })
                        ->toArray();
                @endphp

                <x-select label="{{ $kriteria->name }}" wire:model.live="filters.{{ $kriteria->id }}" :options="$options" clearable />
            @endforeach
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Tutup" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>
