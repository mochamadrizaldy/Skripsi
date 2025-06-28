<?php

use Livewire\Volt\Component;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast;

    public Kriteria $kriteria;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required|in:Benefit,Cost')]
    public string $kategori = '';

    #[Rule('required|numeric|min:1')]
    public int $bobot = 1;

    #[Rule('required')]
    public string $keterangan = '';

    public array $subkriterias = [];

    public function mount()
    {
        $this->fill($this->kriteria);

        $this->subkriterias = $this->kriteria->sub_kriteria
            ->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'name' => $sub->name,
                    'nilai' => $sub->nilai,
                ];
            })
            ->toArray();
    }

    public function addSub()
    {
        $this->subkriterias[] = ['name' => '', 'nilai' => 1];
    }

    public function removeSub($index)
    {
        unset($this->subkriterias[$index]);
        $this->subkriterias = array_values($this->subkriterias);
    }

    public function save()
    {
        $data = $this->validate();

        $this->kriteria->update($data);

        foreach ($this->subkriterias as $sub) {
            if (isset($sub['id'])) {
                SubKriteria::find($sub['id'])->update([
                    'name' => $sub['name'],
                    'nilai' => $sub['nilai'],
                ]);
            } else {
                SubKriteria::create([
                    'kriteria_id' => $this->kriteria->id,
                    'name' => $sub['name'],
                    'nilai' => $sub['nilai'],
                ]);
            }
        }

        $this->success('Kriteria berhasil diperbarui', redirectTo: '/kriteria');
    }
};
?>

<div>
    <x-header title="Edit {{ $kriteria->name }}" separator />

    <x-form wire:submit="save">
        <div class="grid grid-cols-5 gap-6">
            <div class="col-span-2">
                <x-header title="Kriteria" subtitle="Informasi utama" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-input label="Nama Kriteria" wire:model="name" />
                <x-select label="Kategori" :options="[['id' => 'Benefit', 'name' => 'Benefit'], ['id' => 'Cost', 'name' => 'Cost']]" wire:model="kategori" placeholder="-- Kategori --" />
                <x-input label="Bobot" type="number" wire:model="bobot" />
                <x-input label="Keterangan" wire:model="keterangan" />
            </div>
        </div>

        <hr class="my-6" />

        <div class="grid grid-cols-5 gap-6">
            <div class="col-span-2">
                <x-header title="Sub Kriteria" subtitle="Nilai dan nama sub-kriteria" size="text-2xl" />
            </div>

            <div class="col-span-3 space-y-4">
                @foreach ($subkriterias as $index => $sub)
                    <div class="grid grid-cols-12 gap-2 mb-3">
                        <div class="col-span-8">
                            <x-input label="Nama Subkriteria" wire:model="subkriterias.{{ $index }}.name" />
                        </div>
                        <div class="col-span-3">
                            <x-input label="Nilai" wire:model="subkriterias.{{ $index }}.nilai" type="number"
                                min="1" max="5" />
                        </div>
                        <div class="col-span-1 flex items-end">
                            <x-button icon="fas.trash" color="red" wire:click="removeSub({{ $index }})"
                                spinner />
                        </div>
                    </div>
                @endforeach

                <x-button label="Tambah Sub Kriteria" icon="fas.plus" wire:click="addSub" spinner />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Batal" link="/kriteria" />
            <x-button label="Simpan" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
