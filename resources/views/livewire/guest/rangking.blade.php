<?php

use App\Models\Rangking;
use App\Models\Cafe;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.beranda')] #[Title('Beranda')] class extends Component {
    use WithPagination, Toast;

    public int $perPage = 6;
    public bool $detailModal = false;
    public ?Cafe $selectedCafe = null;
    // public bool $myModal2 = false;

    public function updated($property): void
    {
        if (!is_array($property)) {
            $this->resetPage();
        }
    }

    public function getRankingsProperty()
    {
        return Rangking::with('cafe.alternatifs.kriteria.sub_kriteria')->orderBy('peringkat', 'asc')->paginate($this->perPage);
    }

    public function showDetail($id): void
    {
        $this->selectedCafe = Cafe::with('alternatifs.kriteria.sub_kriteria')->find($id);

        if ($this->selectedCafe) {
            $this->detailModal = true;
        }
    }

    public function with(): array
    {
        return [
            'rankings' => $this->rankings,
        ];
    }
};
?>

<div class="min-h-screen w-full max-w-6xl px-4 py-6 mx-auto">
    <h2 class="text-2xl font-bold mb-6 text-center text-black">Daftar Ranking Cafe</h2>

    <!-- Box dummy -->
    <div class="flex justify-center mb-10">
        <div class="w-56 h-32 bg-gray-200"></div>
    </div>

    <div class="bg-white rounded-xl shadow-md px-6 py-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @foreach ($rankings as $ranking)
                @php $cafe = $ranking->cafe; @endphp
                <div class="bg-[#FCF9F4] rounded-lg shadow p-4 cursor-pointer transition hover:scale-105"
                    wire:click="showDetail({{ $cafe->id }})">
                    <img src="{{ asset($cafe->gambar) }}" alt="{{ $cafe->name }}"
                        class="w-full h-32 object-cover rounded mb-3">
                    <h3 class="text-lg font-bold">{{ $cafe->name }}</h3>
                    <p class="text-sm">Peringkat: {{ $ranking->peringkat }}</p>
                    <p class="text-sm">Sosmed: {{ $cafe->sosmed }}</p>
                    <p class="text-sm">Lat: {{ $cafe->latitude }}</p>
                    <p class="text-sm">Long: {{ $cafe->longitude }}</p>
                </div>
            @endforeach
        </div>

        <!-- Custom Pagination -->
        <div class="flex justify-center mt-10">
            <nav class="inline-flex space-x-2">
                @if ($rankings->onFirstPage())
                    <span class="px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded">Prev</span>
                @else
                    <button wire:click="previousPage"
                        class="px-4 py-2 text-sm bg-white border rounded hover:bg-gray-50">Prev</button>
                @endif

                <span class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded">
                    Page {{ $rankings->currentPage() }} of {{ $rankings->lastPage() }}
                </span>

                @if ($rankings->hasMorePages())
                    <button wire:click="nextPage"
                        class="px-4 py-2 text-sm bg-white border rounded hover:bg-gray-50">Next</button>
                @else
                    <span class="px-4 py-2 text-sm text-gray-400 bg-gray-100 rounded">Next</span>
                @endif
            </nav>
        </div>
    </div>

    @if ($detailModal)
        <!-- Modal Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center">
            <!-- Modal Container -->
            <div
                class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-hidden max-h-[90vh] flex flex-col relative animate-fade-in">
                <!-- Close Button -->
                <button wire:click="$set('detailModal', false)"
                    class="absolute top-3 right-3 text-gray-500 hover:text-red-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Detail Cafe</h3>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4 overflow-y-auto flex-1">
                    @if ($selectedCafe)
                        <img src="{{ asset($selectedCafe->gambar) }}"
                            class="h-48 w-full object-cover rounded-lg mb-4" />
                        <p><strong>Nama:</strong> {{ $selectedCafe->name }}</p>
                        <p><strong>Sosmed:</strong> {{ $selectedCafe->sosmed }}</p>
                        <p><strong>Latitude:</strong> {{ $selectedCafe->latitude }}</p>
                        <p><strong>Longitude:</strong> {{ $selectedCafe->longitude }}</p>

                        <hr class="my-2">

                        <h4 class="font-semibold">Detail Penilaian</h4>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($selectedCafe->alternatifs as $alt)
                                <li>
                                    <strong>{{ $alt->kriteria->name }}:</strong>
                                    @php
                                        $sub = $alt->kriteria->sub_kriteria->firstWhere('nilai', $alt->value);
                                    @endphp
                                    <span>{{ $sub?->name ?? 'Tidak ada data' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 border-t flex justify-end">
                    <button wire:click="$set('detailModal', false)"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
