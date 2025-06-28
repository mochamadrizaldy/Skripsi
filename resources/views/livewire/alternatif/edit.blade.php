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

    public array $selectedSub = [];

    public function mount(?int $id = null): void
    {
        if (!$id || !Cafe::find($id)) {
            $this->error('Cafe tidak ditemukan.', redirectTo: '/alternatifs');
            return;
        }

        $this->cafe_id = $id;

        // Ambil alternatif dari cafe ini
        $alternatifs = Alternatif::where('cafe_id', $id)->get();

        foreach ($alternatifs as $alt) {
            $sub = SubKriteria::where('kriteria_id', $alt->kriteria_id)
                ->where('nilai', $alt->value)
                ->first();

            if ($sub) {
                $this->selectedSub[$alt->kriteria_id] = $sub->id;
            }
        }
    }

    public function with(): array
    {
        return [
            'cafes' => Cafe::all(),
            'kriterias' => Kriteria::with('sub_kriteria')->get(),
        ];
    }

    public function save(): void
    {
        // Validasi semua kriteria wajib dipilih
        $rules = [];
        foreach (Kriteria::pluck('id') as $kriteriaId) {
            $rules["selectedSub.$kriteriaId"] = 'required|integer|exists:sub_kriterias,id';
        }
        $this->validate($rules);

        // Hapus alternatif lama
        Alternatif::where('cafe_id', $this->cafe_id)->delete();

        // Simpan alternatif baru
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

        $this->success('Alternatif berhasil diperbarui!', redirectTo: '/alternatif');
    }
};
?>
<div>
    <x-header title="Edit Alternatif" separator />

    <x-form wire:submit="save">
        {{-- Cafe --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Pilih Cafe yang akan diubah" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-select 
                    label="Cafe" 
                    wire:model="cafe_id" 
                    disabled 
                    :options="$cafes->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->toArray()" 
                    placeholder="Pilih Cafe" 
                />
            </div>
        </div>

        <hr class="my-5" />

        {{-- Sub Kriteria --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Sub Kriteria" subtitle="Edit nilai sub kriteria" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                @foreach($kriterias as $kriteria)
                    <x-select 
                        label="{{ $kriteria->name }}" 
                        wire:model="selectedSub.{{ $kriteria->id }}"
                        :options="$kriteria->sub_kriteria->map(fn($sub) => ['id' => $sub->id, 'name' => $sub->name])->toArray()" 
                        placeholder="Pilih Sub Kriteria" 
                    />
                @endforeach
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/alternatif" />
            <x-button label="Update" icon="o-check-circle" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
