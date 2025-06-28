<?php

use App\Models\Cafe;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast, WithPagination, WithFileUploads;

    public string $search = '';
    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'asc'];
    public int $filter = 0;

    public array $page = [['id' => 10, 'name' => '10'], ['id' => 25, 'name' => '25'], ['id' => 50, 'name' => '50'], ['id' => 100, 'name' => '100']];
    public int $perPage = 10;

    public bool $editModal = false;
    public bool $createModal = false;

    public ?Cafe $editingcafe = null;

    // Create form
    public string $newcafeName = '';
    public $newcafeGambar;
    public string $newcafeSosmed = '';
    public string $newcafeLatitude = '';
    public string $newcafeLongitude = '';

    // Edit form
    public string $editingName = '';
    public $editingGambar;
    public ?string $editingSosmed = null;
    public ?string $editingLatitude = null;
    public ?string $editingLongitude = null;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filter telah direset.', position: 'toast-top');
    }

    public function delete($id): void
    {
        $cafe = Cafe::findOrFail($id);
        $cafe->delete();
        $this->warning("Cafe {$cafe->name} dihapus", position: 'toast-top');
    }

    public function create(): void
    {
        $this->reset(['newcafeName', 'newcafeGambar', 'newcafeSosmed', 'newcafeLatitude', 'newcafeLongitude']);
        $this->createModal = true;
    }

    public function saveCreate(): void
    {
        $this->validate([
            'newcafeName' => 'required|string|max:255',
            'newcafeGambar' => 'nullable|image|max:2048',
            'newcafeSosmed' => 'nullable|string',
            'newcafeLatitude' => 'nullable|numeric|between:-90,90',
            'newcafeLongitude' => 'nullable|numeric|between:-180,180',
        ]);

        $gambarPath = $this->newcafeGambar ? $this->newcafeGambar->store('cafe', 'public') : null;

        Cafe::create([
            'name' => $this->newcafeName,
            'gambar' => $gambarPath ? "/storage/{$gambarPath}" : null,
            'sosmed' => $this->newcafeSosmed,
            'latitude' => $this->newcafeLatitude,
            'longitude' => $this->newcafeLongitude,
        ]);

        $this->createModal = false;
        $this->success('Cafe berhasil ditambahkan.', position: 'toast-top');
    }

    public function edit($id): void
    {
        $this->editingcafe = Cafe::findOrFail($id);

        $this->editingName = $this->editingcafe->name;
        $this->editingGambar = null;
        $this->editingSosmed = $this->editingcafe->sosmed;
        $this->editingLatitude = $this->editingcafe->latitude;
        $this->editingLongitude = $this->editingcafe->longitude;
        $this->editModal = true;
    }

    public function saveEdit(): void
    {
        if (!$this->editingcafe) {
            return;
        }

        $this->validate([
            'editingName' => 'required|string|max:255',
            'editingGambar' => 'nullable|image|max:2048',
            'editingSosmed' => 'nullable|string',
            'editingLatitude' => 'nullable|numeric|between:-90,90',
            'editingLongitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($this->editingGambar) {
            if ($this->editingcafe->gambar && file_exists(public_path($this->editingcafe->gambar))) {
                unlink(public_path($this->editingcafe->gambar));
            }

            $gambarPath = $this->editingGambar->store('cafe', 'public');
            $this->editingcafe->gambar = "/storage/{$gambarPath}";
        }

        $this->editingcafe->update([
            'name' => $this->editingName,
            'sosmed' => $this->editingSosmed,
            'latitude' => $this->editingLatitude,
            'longitude' => $this->editingLongitude,
            'gambar' => $this->editingcafe->gambar,
        ]);

        $this->editModal = false;
        $this->success('Cafe berhasil diperbarui.', position: 'toast-top');
    }

    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#'], ['key' => 'gambar', 'label' => 'Gambar'], ['key' => 'name', 'label' => 'Nama Cafe'], ['key' => 'sosmed', 'label' => 'Social Media'], ['key' => 'latitude', 'label' => 'Latitude'], ['key' => 'longitude', 'label' => 'Longitude'], ['key' => 'created_at', 'label' => 'Tanggal dibuat']];
    }

    public function cafes(): LengthAwarePaginator
    {
        return Cafe::query()->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%{$this->search}%"))->orderBy(...array_values($this->sortBy))->paginate($this->perPage);
    }

    public function with(): array
    {
        return [
            'cafes' => $this->cafes(),
            'headers' => $this->headers(),
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
    <!-- HEADER -->
    <x-header title="Cafe" separator progress-indicator>
        <x-slot:actions>
            <x-button label="Create" @click="$wire.create()" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- FILTER -->
    <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-end mb-4">
        <div class="md:col-span-1">
            <x-select label="Show entries" :options="$pages" wire:model.live="perPage" class="w-full" />
        </div>
        <div class="md:col-span-7">
            <x-input placeholder="Cari Cafe..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>
    </div>

    <!-- TABEL -->
    <x-card>
        <x-table :headers="$headers" :rows="$cafes" :sort-by="$sortBy" with-pagination
            @row-click="$wire.edit($event.detail.id)">
            @scope('cell_gambar', $cafes)
                <x-avatar image="{{ $cafes['gambar'] ?? '/default.jpg' }}" class="!w-10" />
            @endscope
            @scope('actions', $cafes)
                <x-button icon="o-trash" wire:click="delete({{ $cafes['id'] }})"
                    wire:confirm="Yakin ingin menghapus cafe {{ $cafes['name'] }}?" spinner
                    class="btn-ghost btn-sm text-red-500" />
            @endscope
        </x-table>
    </x-card>

    <!-- MODAL CREATE -->
    <x-modal wire:model="createModal" title="Tambah Cafe">
        <div class="grid gap-4">
            <x-file label="Gambr Cafe" wire:model="newcafeGambar" accept="image/png, image/jpeg" crop-after-change>
                <img src="{{ $cafe->gambar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
            </x-file>
            <x-input label="Nama Cafe" wire:model.live="newcafeName" />
            <x-input label="Social Media" wire:model.live="newcafeSosmed" />
            <x-input label="Latitude" wire:model.live="newcafeLatitude" />
            <x-input label="Longitude" wire:model.live="newcafeLongitude" />
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.createModal = false" />
            <x-button label="Simpan" class="btn-primary" wire:click="saveCreate" />
        </x-slot:actions>
    </x-modal>

    <!-- MODAL EDIT -->
    <x-modal wire:model="editModal" title="Edit Cafe">
        <div class="grid gap-4">
            <x-file label="Gambr Cafe" wire:model="editingGambar" accept="image/png, image/jpeg" crop-after-change>
                <img src="{{ $cafe->gambar ?? '/empty-user.jpg' }}" class="h-40 rounded-lg" />
            </x-file>
            <x-input label="Nama Cafe" wire:model="editingName" />
            <x-input label="Social Media" wire:model="editingSosmed" />
            <x-input label="Latitude" wire:model="editingLatitude" />
            <x-input label="Longitude" wire:model="editingLongitude" />
        </div>

        <x-slot:actions>
            <x-button label="Batal" @click="$wire.editModal = false" />
            <x-button label="Simpan Perubahan" class="btn-primary" wire:click="saveEdit" />
        </x-slot:actions>
    </x-modal>

    <!-- DRAWER FILTER -->
    <x-drawer wire:model="drawer" title="Filter" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Cari Cafe..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Terapkan" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</div>
