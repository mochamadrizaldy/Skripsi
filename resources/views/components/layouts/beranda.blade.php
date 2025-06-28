<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body
    class="min-h-screen font-sans antialiased bg-[#FCF9F4] >
    <x-main>
        <x-slot:content>
            <div class="container
    mx-auto px-4 py-6">

    {{-- Navbar --}}
    <div class="w-full flex flex-row items-center justify-between mb-8 max-w-4xl mx-auto">
        <div class="flex-1 flex justify-center">
            <div
                class="bg-[#0B0B15] rounded-full flex w-full max-w-4xl h-10 items-center justify-between px-2 sm:px-4 md:px-6">

                <a href="/beranda" class="text-sm text-white px-2 py-1 font-italiana">CARI CAFE</a>


                <div class="flex items-center space-x-2">
                    <a href="/beranda" class="text-sm text-white px-7 py-1">Home Page</a>
                    <a href="/rankings" class="text-sm text-white px-7 py-1">Ranking</a>
                    <a href="/rekomendasi" class="text-sm text-white px-7 py-1">Rekomendasi</a>
                </div>

                <a href="/login"
                    class="ml-4 bg-[#0B0B15] text-white text-sm font-semibold px-4 py-2 rounded-full shadow hover:bg-[#23233a] transition whitespace-nowrap">
                    Login
                </a>
            </div>
        </div>



    </div>
    {{-- Konten halaman --}}
    {{ $slot }}
    </div>
    </x-slot:content>
    </x-main>

    @livewireScripts
</body>

</html>
