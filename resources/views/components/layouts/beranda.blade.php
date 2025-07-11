<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

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
                <div x-data="{ sidebarOpen: false }" class="w-full">
                    {{-- Navbar Mobile Only --}}
                    <div class="flex items-center justify-between bg-[#0B0B15] text-white h-14 px-4 md:hidden">
                        <a href="/" class="inline-block">
                            <img src="{{ asset('template/img/CARI CAFFE2.PNG') }}" alt="" class="h-8">
                        </a>


                        <button @click="sidebarOpen = !sidebarOpen" class="focus:outline-none">
                            <!-- Hamburger Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>


                    {{-- Desktop Navbar --}}
                    <div class="hidden md:flex w-full flex-row items-center justify-center mb-8 px-4">
                        <div
                            class="bg-[#0B0B15] rounded-full flex w-full max-w-4xl h-10 items-center justify-between px-2 sm:px-4 md:px-6">
                            <a href="/" class="inline-block">
                                <img src="{{ asset('template/img/CARI CAFFE2.PNG') }}" alt="Logo" class="h-10">
                            </a>

                            <div class="flex items-center space-x-2">
                                <a href="/" class="text-sm text-white px-7 py-1">Home Page</a>
                                @if (auth()->check())
                                    <a href="/rankings" class="text-sm text-white px-7 py-1">Ranking</a>
                                    <a href="/rekomendasis" class="text-sm text-white px-7 py-1">Rekomendasi</a>
                                @endif
                            </div>

                            @if (auth()->check())
                                <div x-data="{ open: false }" class="relative ml-4">
                                    <button @click="open = !open"
                                        class="flex items-center justify-center w-10 h-10 rounded-full bg-[#0B0B15] text-white shadow hover:bg-[#23233a] transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5.121 17.804A4 4 0 017 17h10a4 4 0 011.879.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg z-50 text-sm text-gray-800 overflow-hidden">
                                        <a href="/editProfile" class="block px-4 py-2 hover:bg-gray-100 transition">Edit
                                            Profil</a>
                                        <a href="/logout"
                                            class="block px-4 py-2 hover:bg-gray-100 transition">Logout</a>
                                    </div>
                                </div>
                            @else
                                <a href="/login"
                                    class="ml-4 bg-[#0B0B15] text-white text-sm font-semibold px-4 py-2 rounded-full shadow hover:bg-[#23233a] transition whitespace-nowrap">
                                    Login
                                </a>
                            @endif
                        </div>
                    </div>




                    {{-- Mobile Sidebar --}}
                    <div x-show="sidebarOpen" @click.away="sidebarOpen = false"
                        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex md:hidden">
                        <div class="w-64 bg-white p-6 space-y-4 text-gray-800">
                            <div class="flex justify-between items-center mb-4">
                                <span class="font-bold text-lg">Menu</span>
                                <button @click="sidebarOpen = false">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <a href="/" class="block px-2 py-1 hover:bg-gray-100 rounded">Home Page</a>
                            @if (auth()->check())
                                <a href="/rankings" class="block px-2 py-1 hover:bg-gray-100 rounded">Ranking</a>
                                <a href="/rekomendasis"
                                    class="block px-2 py-1 hover:bg-gray-100 rounded">Rekomendasi</a>
                                <a href="/editProfile" class="block px-2 py-1 hover:bg-gray-100 rounded">Edit Profil</a>
                                <a href="/logout" class="block px-2 py-1 hover:bg-gray-100 rounded">Logout</a>
                            @else
                                <a href="/login" class="block px-2 py-1 hover:bg-gray-100 rounded">Login</a>
                            @endif
                        </div>
                    </div>
                </div>

                <script>
                    window.initMap = function() {
                        const event = new CustomEvent('google-maps-ready');
                        window.dispatchEvent(event);
                    };
                </script>
                {{-- Konten halaman --}}
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>
    {{-- TOAST area --}}
    <x-toast />

    @livewireScripts
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_RaFzN-2uez7swhZmlBPHKZmtKofRBWM&callback=initMap"></script>
</body>

</html>
