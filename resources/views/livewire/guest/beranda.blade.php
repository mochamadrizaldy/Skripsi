<?php

use App\Models\Kriteria;
use App\Models\Cafe;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Layout('components.layouts.beranda')] #[Title('Beranda')] class extends Component {
    public int $totalKriteria;
    public int $totalCafe;

    public function mount()
    {
        $this->totalKriteria = Kriteria::count();
        $this->totalCafe = Cafe::count();
    }
};

?>

<div class="min-h-screen bg-[#FCF9F4] flex flex-col items-center py-10">
    <h2 class="text-2xl font-bold mb-8 text-center text-neutral-800">Selamat Datang! Yuk, Cari Cafe Terbaik untuk Kamu
    </h2>

    <!-- Content Row -->
    <div class="flex flex-col md:flex-row justify-center items-center w-full max-w-4xl mb-16 gap-8 md:gap-0">
        <!-- Left: Description -->
        <div class="flex-1 text-xs text-black px-4 mb-6 md:mb-0 text-center md:text-left">
            <p>
                Sistem rekomendasi ini dirancang untuk membantu pengguna menemukan cafe terbaik untuk mengerjakan tugas.
                Berdasarkan preferensi yang dipilih pengguna, sistem akan menampilkan daftar alternatif cafe yang
                paling sesuai dan relevan.
            </p>
        </div>
        <!-- Right: Gray Box -->
        <div class="flex-1 flex justify-center">
            <div class="w-40 h-32 sm:w-56 sm:h-48 overflow-hidden rounded">
                <img src="{{ asset('template/img/CARI CAFFE2.PNG') }}" alt="Cari Cafe"
                    class="w-full h-full object-contain" />
            </div>
        </div>

    </div>

    <!-- Section: Jumlah Data -->
    <div class="w-full max-w-4xl">
        <h3 class="text-lg font-bold mb-6 text-center text-black">Jumlah Data</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 justify-center items-center px-4">
            <!-- Card: Total Kriteria -->
            <div class="bg-[#55372B] rounded-xl shadow-md p-6 flex flex-col items-center justify-center">
                {{-- <x-icon name="clipboard-document" class="w-8 h-8 text-blue-500 mb-2" /> --}}
                <p class="text-sm font-medium text-white">Total Kriteria</p>
                <p class="text-2xl font-bold text-neutral-700 text-white">{{ $totalKriteria }}</p>
            </div>

            <!-- Card: Total Cafe -->
            <div class="bg-[#55372B] rounded-xl shadow-md p-6 flex flex-col items-center justify-center">
                {{-- <x-icon name="building-storefront" class="w-8 h-8 text-green-600 mb-2" /> --}}
                <p class="text-sm font-medium text-white">Total Cafe</p>
                <p class="text-2xl font-bold text-neutral-700 text-white">{{ $totalCafe }}</p>
            </div>
        </div>
    </div>
</div>
