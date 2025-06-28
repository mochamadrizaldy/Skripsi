@extends('components.layouts.beranda')

@section('content')
    <div class="min-h-screen bg-[#FCF9F4] flex flex-col items-center py-6">
        <!-- Navbar + Login Button Row -->

        <!-- HomePage Title -->
        <h2 class="text-xl font-bold mb-8 text-center">HomePage</h2>

        <!-- Content Row -->
        <div class="flex flex-col md:flex-row justify-center items-center w-full max-w-4xl mb-16 gap-8 md:gap-0">
            <!-- Left: Description -->
            <div class="flex-1 text-xs text-black px-4 mb-6 md:mb-0 text-center md:text-left">
                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the
                    industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and
                    scrambled it to make a type specimen book.
                </p>
            </div>
            <!-- Right: Gray Box -->
            <div class="flex-1 flex justify-center">
                <div class="w-40 h-32 sm:w-56 sm:h-48 bg-gray-200"></div>
            </div>
        </div>

        @php
            use App\Models\Kriteria;
            use App\Models\Cafe;

            $totalKriteria = Kriteria::count();
            $totalCafe = Cafe::count();
        @endphp

        <!-- Jumlah Data Section -->
        <div class="w-full flex flex-col items-center">
            <h3 class="text-lg font-bold mb-8 text-center">Jumlah Data</h3>
            <div class="flex flex-col sm:flex-row gap-8 sm:gap-16 items-center">
                <!-- Card: Total Kriteria -->
                <div
                    class="w-40 h-44 bg-white rounded-lg shadow-md flex flex-col items-center justify-center text-center px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-6h13M9 6h13M4 6h.01M4 12h.01M4 18h.01" />
                    </svg>
                    <p class="text-sm font-semibold">Total Kriteria</p>
                    <p class="text-xl font-bold">{{ $totalKriteria }}</p>
                </div>

                <!-- Card: Total Cafe -->
                <div
                    class="w-40 h-44 bg-white rounded-lg shadow-md flex flex-col items-center justify-center text-center px-4 py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M8 21h8M12 17v4M7 10v6a5 5 0 0010 0v-6" />
                    </svg>
                    <p class="text-sm font-semibold">Total Cafe</p>
                    <p class="text-xl font-bold">{{ $totalCafe }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
