@extends('components.layouts.beranda')

@section('content')
    <div class="min-h-screen bg-[#FCF9F4] flex flex-col items-center py-6">
        <!-- Navbar -->
        <!-- Title -->
        <h2 class="text-xl font-bold mb-4 text-center">Lorem Ipsum</h2>

        <!-- Form & Image Row -->
        <div class="flex flex-col md:flex-row justify-center items-start w-full max-w-4xl mb-8 gap-8">
            <!-- Left: Form -->
            <div class="flex-1 flex flex-col gap-4">
                @for ($i = 0; $i < 8; $i++)
                    <input type="text" class="w-full rounded-lg bg-white shadow px-4 py-2 outline-none" disabled />
                @endfor
                <div class="flex gap-4 mt-2">
                    <button class="w-20 h-6 rounded-lg bg-gray-300"></button>
                    <button class="w-20 h-6 rounded-lg bg-red-600"></button>
                </div>
            </div>
            <!-- Right: Gray Box -->
            <div class="flex-1 flex justify-center">
                <div class="w-72 h-40 bg-gray-200"></div>
            </div>
        </div>

        <!-- Card Grid -->
        <div class="w-full max-w-5xl flex justify-center">
            <div class="bg-[#EDE1D4] rounded-xl w-full px-4 py-10 flex flex-col items-center">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="w-36 h-44 bg-[#FCF9F4] rounded-lg shadow-md"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
@endsection
