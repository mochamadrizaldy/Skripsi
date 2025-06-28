<?php

use App\Models\Cafe;
use App\Models\Kriteria;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.beranda')] #[Title('Rekomendasi')] class extends Component {
    use Toast;

    public string $search = '';
    public array $filters = [];

    public function clear(): void
    {
        $this->reset(['filters', 'search']);
        $this->success('Filter direset.', position: 'toast-top');
    }

    public function filteredCafes()
    {
        $query = Cafe::with(['alternatifs.kriteria'])->with('alternatifs');
        $kriterias = Kriteria::with('sub_kriteria')->get();

        return $query->get()->filter(function ($cafe) use ($kriterias) {
            foreach ($this->filters as $kriteriaId => $subId) {
                $alt = $cafe->alternatifs->firstWhere('kriteria_id', $kriteriaId);
                $kriteria = $kriterias->firstWhere('id', $kriteriaId);
                $sub = $kriteria?->sub_kriteria->firstWhere('nilai', $alt->value ?? null);

                if (!$sub || $sub->id != $subId) {
                    return false;
                }
            }
            return true;
        });
    }

    public function with(): array
    {
        return [
            'cafes' => $this->filteredCafes(),
            'kriterias' => Kriteria::with('sub_kriteria')->get(),
        ];
    }
};
?>

<div class="min-h-screen bg-white rounded-xl flex flex-col items-center py-6 px-4">
    <h2 class="text-xl font-bold mb-4 text-center text-black">Filter Cafe Berdasarkan Sub Kriteria</h2>

    <!-- Filter Panel dan Ilustrasi -->
    <div class="flex flex-col md:flex-row justify-center items-start w-full max-w-4xl mb-8 gap-8">
        <!-- Filter -->
        <div class="flex-1 space-y-4">
            @foreach ($kriterias as $kriteria)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $kriteria->name }}</label>
                    <select wire:model.live="filters.{{ $kriteria->id }}"
                        class="w-full border border-gray-300 bg-gray-100 rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:outline-none px-3 py-2 text-sm">
                        <option value="">Pilih {{ $kriteria->name }}</option>
                        @foreach ($kriteria->sub_kriteria as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach

            <div class="flex gap-4 mt-2">
                <button wire:click="clear"
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-4 py-2 rounded">
                    Reset
                </button>
            </div>
        </div>

        <!-- Gambar Placeholder -->
        <div class="flex-1 flex justify-center">
            <div class="w-72 h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-600 text-sm">
                Gambar / Ilustrasi
            </div>
        </div>
    </div>

    <!-- Daftar Cafe -->
    <div class="w-full max-w-5xl mx-auto">
        <div class="w-full bg-[#EDE1D4] rounded-xl px-4 py-10 shadow">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 justify-items-center">
                @forelse ($cafes as $cafe)
                    <div class="w-64 h-60 bg-[#FCF9F4] rounded-lg shadow-md flex flex-col items-center p-3">
                        <img src="{{ asset($cafe->gambar) }}" alt="{{ $cafe->name }}"
                            class="w-full h-32 object-cover rounded mb-2">
                        <p class="text-sm font-bold text-center">{{ $cafe->name }}</p>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Tidak ada cafe yang cocok.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
