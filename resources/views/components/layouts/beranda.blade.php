<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Cropper.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />

    {{-- TinyMCE --}}
    <script src="https://cdn.tiny.cloud/1/zj7w29mcgsahkxloyg71v6365yxaoa4ey1ur6l45pnb63v42/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

    {{-- Currency --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js">
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Italiana&display=swap" rel="stylesheet">


    @livewireStyles
</head>

<body class="min-h-screen font-sans antialiased bg-[#FCF9F4]">
    <x-main>
        <x-slot:content>
            <div class="container mx-auto px-4 py-6">

                {{-- Navbar --}}
                <div class="w-full flex flex-row items-center justify-between mb-8 max-w-4xl mx-auto">
                    <div class="flex-1 flex justify-center">
                        <div
                            class="bg-[#0B0B15] rounded-full flex w-full max-w-4xl h-10 items-center justify-between px-2 sm:px-4 md:px-6">

                            <a href="/" class="text-sm text-white px-2 py-1 font-italiana">CARI CAFE</a>

                            <div class="flex items-center space-x-2">
                                <a href="/" class="text-sm text-white px-7 py-1">Home Page</a>
                                @if (auth()->check())
                                    <a href="/rankings" class="text-sm text-white px-7 py-1">Ranking</a>
                                    <a href="/rekomendasis" class="text-sm text-white px-7 py-1">Rekomendasi</a>
                                @endif
                            </div>

                            @if (auth()->check())
                                <a href="/editProfile"
                                    class="ml-4 bg-[#0B0B15] text-white text-sm font-semibold px-4 py-2 rounded-full shadow hover:bg-[#23233a] transition whitespace-nowrap">
                                    Edit Profile
                                </a>
                                <a href="/logout"
                                    class="ml-4 bg-[#0B0B15] text-white text-sm font-semibold px-4 py-2 rounded-full shadow hover:bg-[#23233a] transition whitespace-nowrap">
                                    Logout
                                </a>
                            @else
                                <a href="/login"
                                    class="ml-4 bg-[#0B0B15] text-white text-sm font-semibold px-4 py-2 rounded-full shadow hover:bg-[#23233a] transition whitespace-nowrap">
                                    Login
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Konten halaman --}}
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    {{-- TOAST area --}}
    <x-toast />

    @livewireScripts
</body>

</html>
