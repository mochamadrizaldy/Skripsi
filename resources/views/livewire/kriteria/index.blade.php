<?php

use App\Models\Kriteria;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public int $filter = 0;

    public $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];
    public int $perPage = 10;

    public bool $editModal = false;
    public bool $createModal = false;

    public ?Kriteria $editingkriteria = null;

    public string $editingName = '';
    public string $editingKategori = '';
    public int $editingBobot;
    public string $editingKeterangan = '';

    public string $newkriteriaName = '';
    public string $newkriteriaKategori = '';
    public int $newkriteriaBobot;
    public string $newkriteriaKeterangan = '';

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-top');
    }

    public function delete($id): void
    {
        $kategori = Kriteria::findOrFail($id);
        $kategori->delete();
        $this->warning("Kriteria $kategori->name akan dihapus", position: 'toast-top');
    }

    public function create(): void
    {
        $this->newkriteriaName = '';
        $this->newkriteriaKategori = '';
        $this->newkriteriaBobot = 0;
        $this->newkriteriaKeterangan = '';
        $this->createModal = true;
    }

    public function saveCreate(): void
    {
        $this->validate([
            'newkriteriaName' => 'required|string|max:255',
            'newkriteriaKategori' => 'required',
            'newkriteriaBobot' => 'required|numeric',
            'newkriteriaKeterangan' => 'required',
        ]);

        Kriteria::create([
            'name' => $this->newkriteriaName,
            'kategori' => $this->newkriteriaKategori,
            'bobot' => $this->newkriteriaBobot,
            'keterangan' => $this->newkriteriaKeterangan,
        ]);

        $this->createModal = false;
        $this->success('Kriteria created successfully.', position: 'toast-top');
    }

    public function edit($id): void
    {
        $this->editingkriteria = Kriteria::find($id);

        if ($this->editingkriteria) {
            $this->editingName = $this->editingkriteria->name;
            $this->editingKategori = $this->editingkriteria->kategori;
            $this->editingBobot = $this->editingkriteria->bobot;
            $this->editingKeterangan = $this->editingkriteria->keterangan;
            $this->editModal = true;
        }
    }

    public function saveEdit(): void
    {
        if ($this->editingkriteria) {
            $this->editingkriteria->update([
                'name' => $this->editingName,
                'kategori' => $this->editingKategori,
                'bobot' => $this->editingBobot,
                'keterangan' => $this->editingKeterangan,
                'updated_at' => now(),
            ]);

            $this->editModal = false;
            $this->success('Kriteria updated successfully.', position: 'toast-top');
        }
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-100'],
            ['key' => 'kategori', 'label' => 'Kategori', 'class' => 'w-100'],
            ['key' => 'bobot', 'label' => 'Bobot', 'class' => 'w-100'],
            ['key' => 'keterangan', 'label' => 'Keterangan', 'class' => 'w-100'],
            ['key' => 'created_at', 'label' => 'Tanggal dibuat', 'class' => 'w-30'],
        ];
    }

    public function kriterias(): LengthAwarePaginator
    {
        return Kriteria::query()
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function with(): array
    {
        if ($this->filter >= 0 && $this->filter < 2) {
            if ($this->search !== '') {
                $this->filter = 1;
            } else {
                $this->filter = 0;
            }
        }

        return [
            'kriterias' => $this->kriterias(),
            'headers' => $this->headers(),
            'perPage' => $this->perPage,
            'pages' => $this->page,
        ];
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }
};

?>

<!-- BLADE SECTION -->
<div>
    <x-header title="Kriteria" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Create" link="/kriteria/create" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 mb-4 items-end">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" class="w-15" />
        </div>
        <div class="md:col-span-7">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
    </div>

    <x-card>
        <x-table :headers="$headers" :rows="$kriterias" :sort-by="$sortBy" with-pagination
            link="kriteria/{id}/edit?name={name}">
            @scope('actions', $kriterias)
            <x-button icon="o-trash" wire:click="delete({{ $kriterias['id'] }})"
                wire:confirm="Yakin ingin menghapus {{ $kriterias['name'] }}?" spinner
                class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- Modal Create -->
    <x-modal wire:model="createModal" title="Create Kriteria">
        <div class="grid gap-4">
            <x-input label="Nama Kriteria" wire:model.live="newkriteriaName" />
            <x-select label="Kategori" :options="[['id' => 'Benefit', 'name' => 'Benefit'], ['id' => 'Cost', 'name' => 'Cost']]" wire:model.live="newkriteriaKategori" />
            <x-input type="number" label="Bobot" wire:model.live="newkriteriaBobot" />
            <x-textarea label="Keterangan" wire:model.live="newkriteriaKeterangan" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" icon="o-x-mark" @click="$wire.createModal=false" />
            <x-button label="Save" icon="o-check" class="btn-primary" wire:click="saveCreate" spinner/>
        </x-slot:actions>
    </x-modal>

    <!-- Modal Edit -->
    <x-modal wire:model="editModal" title="Edit Kriteria">
        <div class="grid gap-4">
            <x-input label="Nama Kriteria" wire:model.live="editingName" />
            <x-select label="Kategori" :options="[['id' => 'Benefit', 'name' => 'Benefit'], ['id' => 'Cost', 'name' => 'Cost']]" wire:model.live="editingKategori" />
            <x-input type="number" label="Bobot" wire:model.live="editingBobot" />
            <x-textarea label="Keterangan" wire:model.live="editingKeterangan" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" icon="o-x-mark" @click="$wire.editModal=false" />
            <x-button label="Save" icon="o-check" class="btn-primary" wire:click="saveEdit" spinner />
        </x-slot:actions>
    </x-modal>

    <!-- Drawer Filter -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer=false" />
        </x-slot:actions>
    </x-drawer>
</div>