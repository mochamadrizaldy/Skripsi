<?php

use App\Models\Barang;
use App\Models\Satuan;
use App\Models\User;
use App\Models\JenisBarang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Cafe;
use App\Models\Kriteria;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component {
    use Toast;
    use WithPagination; // Menyimpan ID barang yang dipilih

    public function mount()
    {

    }

    public function with()
    {
        return [
            'kriteriaTotal' => Kriteria::count(),
            'cafeTotal' => Cafe::count(),
            'userTotal' => User::count(),
        ];
    }
};
?>

<div>
    <x-header title="Dashboard" separator progress-indicator />

    <!-- Grid untuk Kartu Data -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mt-6">
        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.box-open" class="text-purple-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Total Kriteria</p>
                    <p class="text-2xl font-bold">{{ $kriteriaTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.balance-scale" class="text-blue-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Total Cafe</p>
                    <p class="text-2xl font-bold">{{ $cafeTotal }}</p>
                </div>
            </div>
        </x-card>

        <x-card class="rounded-lg shadow p-6">
            <div class="flex items-center gap-4">
                <x-icon name="fas.tags" class="text-green-600 w-12 h-12" />
                <div>
                    <p class="text-gray-600">Total User</p>
                    <p class="text-2xl font-bold">{{ $userTotal }}</p>
                </div>
            </div>
        </x-card>
    </div>
</div>
