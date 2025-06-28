<?php

use App\Models\Cafe;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use App\Models\Alternatif;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;

new class extends Component {
    use Toast;

    #[Rule('required')]
    public ?int $cafe_id = null;

    public array $selectedSub = []; // [kriteria_id => sub_kriteria_id]

    public function with(): array
    {
        // Ambil semua cafe_id dari alternatif yang sudah ada
        $existingCafeIds = Alternatif::distinct('cafe_id')->pluck('cafe_id')->toArray();

        // Ambil cafe yang belum ada di alternatif
        $availableCafes = Cafe::whereNotIn('id', $existingCafeIds)->get();

        return [
            'cafes' => $availableCafes,
            'kriterias' => Kriteria::with('sub_kriteria')->get(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->selectedSub as $kriteriaId => $subKriteriaId) {
            $sub = SubKriteria::find($subKriteriaId);
            if ($sub) {
                Alternatif::create([
                    'cafe_id' => $this->cafe_id,
                    'kriteria_id' => $kriteriaId,
                    'value' => $sub->nilai,
                ]);
            }
        }

        $this->success('Alternatif berhasil disimpan!', redirectTo: '/alternatif');
    }
};
?>
<div>
    <x-header title="Create" separator />

    <x-form wire:submit="save">
        {{-- Basic section --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from cafe" size="text-2xl" />
            </div>

            <div class="col-span-3 grid gap-3">
                <x-select label="Cafe" wire:model="cafe_id" :options="$cafes->map(fn($cafe) => ['id' => $cafe->id, 'name' => $cafe->name])->toArray()" placeholder="-- Cafe --" />
            </div>
        </div>

        {{-- Details section --}}
        <hr class="my-5" />

        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Details" subtitle="More about the alternatif" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                @foreach($kriterias as $kriteria)
                    <x-select label="{{ $kriteria->name }}" wire:model="selectedSub.{{ $kriteria->id }}"
                        :options="$kriteria->sub_kriteria->map(fn($sub) => ['id' => $sub->id, 'name' => $sub->name])->toArray()" placeholder="-- Sub Kriteria {{ $kriteria->id }} --" />
                @endforeach
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/alternatif" />
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>

    </x-form>
</div>