<?php

use Livewire\Volt\Component;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Illuminate\Support\Collection;

new class extends Component {
    use Toast;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|in:Benefit,Cost')]
    public string $kategori = '';

    #[Rule('required|numeric|min:1|max:100')]
    public int $bobot;

    #[Rule('nullable')]
    public ?string $keterangan = null;

    public array $subkriterias = [
        ['name' => '', 'nilai' => 1]
    ];

    public function addSubkriteria(): void
    {
        $this->subkriterias[] = ['name' => '', 'nilai' => 1];
    }

    public function removeSubkriteria($index): void
    {
        unset($this->subkriterias[$index]);
        $this->subkriterias = array_values($this->subkriterias); // reset index
    }

    public function save(): void
    {
        $this->validate();

        $this->validate([
            'subkriterias.*.name' => 'required|string',
            'subkriterias.*.nilai' => 'required|integer|min:1|max:5',
        ]);

        $kriteria = Kriteria::create([
            'name' => $this->name,
            'kategori' => $this->kategori,
            'bobot' => $this->bobot,
            'keterangan' => $this->keterangan,
        ]);

        foreach ($this->subkriterias as $sub) {
            SubKriteria::create([
                'name' => $sub['name'],
                'nilai' => $sub['nilai'],
                'kriteria_id' => $kriteria->id,
            ]);
        }

        $this->success('Kriteria dan subkriteria berhasil ditambahkan!', redirectTo: '/kriteria');
    }
};
?>
<div>
    <x-header title="Create" separator />

    <x-form wire:submit="save">
        {{-- Basic section --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from kriteria" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Nama Kriteria" wire:model="name" />
                <x-select label="Kategori" :options="[['id' => 'Benefit', 'name' => 'Benefit'], ['id' => 'Cost', 'name' => 'Cost']]" wire:model="kategori" placeholder="-- Kategori --"/>
                <x-input label="Bobot (%)" wire:model="bobot" type="number" />
                <x-textarea label="Keterangan" wire:model="keterangan" />
            </div>
        </div>

        {{-- Details section --}}
        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the sub kriteria" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                @foreach ($subkriterias as $index => $sub)
                    <div class="grid grid-cols-12 gap-2 mb-3">
                        <div class="col-span-8">
                            <x-input label="Nama Subkriteria" wire:model="subkriterias.{{ $index }}.name" />
                        </div>
                        <div class="col-span-3">
                            <x-input label="Nilai" wire:model="subkriterias.{{ $index }}.nilai" type="number" min="1"
                                max="5" />
                        </div>
                        <div class="col-span-1 flex items-end">
                            <x-button icon="fas.trash" color="red" wire:click="removeSubkriteria({{ $index }})" spinner/>
                        </div>
                    </div>
                @endforeach

                <x-button label="Tambah Subkriteria" icon="fas.plus" wire:click="addSubkriteria" class="mt-2" spinner/>
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/kriteria" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>