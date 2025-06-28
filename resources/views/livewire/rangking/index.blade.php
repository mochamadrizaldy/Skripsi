<?php

use App\Models\Rangking;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlternatifExport;

new class extends Component {
    use Toast;
    use WithPagination;
    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'peringkat', 'direction' => 'asc'];

    // Create a public property.
    // public int $country_id = 0;

    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];

    public int $perPage = 10; // Default jumlah data per halaman

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'peringkat', 'label' => 'Peringkat'],
            ['key' => 'cafe.name', 'label' => 'Cafe'],
            ['key' => 'score', 'label' => 'Score'],
        ];
    }

    public function rangkings(): LengthAwarePaginator
    {
        return Rangking::query()
            ->select('rangkings.*')
            ->join('cafes', 'cafes.id', '=', 'rangkings.cafe_id')
            ->when($this->search, function (Builder $query) {
                $query->where('cafes.name', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy['column'] === 'cafe.name' ? 'cafes.name' : $this->sortBy['column'], $this->sortBy['direction'])
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 2) {
            if (!$this->search == null) {
                $this->filter = 1;
            } else {
                $this->filter = 0;
            }
        }
        return [
            'rangkings' => $this->rangkings(),
            'headers' => $this->headers(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    // Reset pagination when any component property changes
    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }

    public function export()
    {
        return Excel::download(new AlternatifExport, 'detail_perhitungan.xlsx');
    }
};

?>

<div>
    <!-- HEADER -->
    <x-header title="Rangkings" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Lihat Perhitungan" link="rangking/perhitungan" icon="o-presentation-chart-bar"
                class="btn-secondary" />
            <x-button label="Export Excel" wire:click="export" icon="fas.download" class="btn-success" />
        </x-slot:actions>
    </x-header>

    <!-- FILTERS -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4  items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" class="w-15" />
        </div>
        <div class="md:col-span-7">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"
                class="" />
        </div>
        <!-- Dropdown untuk jumlah data per halaman -->
    </div>

    <!-- TABLE wire:poll.5s="users"  -->
    <x-card>
        <x-table :headers="$headers" :rows="$rangkings" :sort-by="$sortBy" with-pagination>
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
            {{-- <x-select placeholder="Country" wire:model.live="country_id" :options="$countries" icon="o-flag"
                placeholder-value="0" /> --}}
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>