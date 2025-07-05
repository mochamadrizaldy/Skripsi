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
    public float $userLat = -7.94666; // default Malang
    public float $userLng = 112.6145;
    public bool $gpsUsed = false;

    public function clear(): void
    {
        $this->reset(['filters', 'search']);
        $this->success('Filter direset.', position: 'toast-top');
    }
    public function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function filteredCafes()
    {
        $query = Cafe::with(['alternatifs.kriteria'])->with('alternatifs');
        $kriterias = Kriteria::with('sub_kriteria')->get();
        // dd($query->get()->toArray(), $kriterias->toArray());
        return $query
            ->get()
            ->filter(function ($cafe) use ($kriterias) {
                // hitung jarak cafe
                $distance = $this->haversineDistance($this->userLat, $this->userLng, $cafe->latitude, $cafe->longitude);
                $cafe->distance = $distance;

                // cek apakah ada filter
                foreach ($this->filters as $kriteriaId => $subId) {
                    $alt = $cafe->alternatifs->firstWhere('kriteria_id', $kriteriaId);
                    $kriteria = $kriterias->firstWhere('id', $kriteriaId);

                    if ($kriteria->name == 'Jarak') {
                        // override alt berdasarkan jarak
                        $sub = $this->getJarakSubKriteria($distance, $kriteria);
                    } else {
                        $sub = $kriteria?->sub_kriteria->firstWhere('nilai', $alt->value ?? null);
                    }

                    if (!$sub || $sub->id != $subId) {
                        return false;
                    }
                }
                $this->success('Data Terfilter.', position: 'toast-top');
                return true;
            })
            ->sortBy('distance');
    }
    public function getJarakSubKriteria(float $distance, $kriteria)
    {
        foreach ($kriteria->sub_kriteria as $sub) {
            $range = $sub->name;

            if (str_contains($range, '<')) {
                preg_match('/< ?([\d.]+)/', $range, $match);
                if ($distance < floatval($match[1])) {
                    return $sub;
                }
            }

            if (str_contains($range, 's.d.')) {
                preg_match('/([\d.]+) s.d. ([\d.]+)/', $range, $match);
                if ($distance >= floatval($match[1]) && $distance <= floatval($match[2])) {
                    return $sub;
                }
            }

            if (str_contains($range, '>')) {
                preg_match('/> ?([\d.]+)/', $range, $match);
                if ($distance > floatval($match[1])) {
                    return $sub;
                }
            }
        }

        return null;
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
        <div class="flex-1 flex flex-col justify-center">

            <div class="mt-4 flex gap-3 w-full">
                <button onclick="setToMyLocation()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded shadow w-full">
                    Gunakan Lokasi Saya
                </button>

                <button onclick="resetLocation()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 text-sm rounded shadow w-full">
                    Reset Lokasi ke Default
                </button>
            </div>
            <div class="mt-4 flex gap-3 mb-4 w-full">
                <p class="text-sm text-gray-600">Jika tidak memilih lokasi maka otomatis akan menggunakan lokasi default
                </p>
            </div>

            <div wire:ignore class="w-full h-64 rounded overflow-hidden" id="map"></div>


            <script>
                let map;
                let marker;

                function createMap(lat, lng) {
                    const position = {
                        lat,
                        lng
                    };

                    map = new google.maps.Map(document.getElementById("map"), {
                        center: position,
                        zoom: 14,
                    });

                    marker = new google.maps.Marker({
                        position,
                        map,
                        draggable: true,
                    });

                    marker.addListener("dragend", function(event) {
                        @this.set('userLat', event.latLng.lat());
                        @this.set('userLng', event.latLng.lng());
                    });

                    // Coba pakai lokasi GPS
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const userPosition = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };
                                map.setCenter(userPosition);
                                marker.setPosition(userPosition);

                                @this.set('userLat', userPosition.lat);
                                @this.set('userLng', userPosition.lng);
                                @this.set('gpsUsed', true);
                            },
                            function() {
                                @this.set('gpsUsed', false);
                            }
                        );
                    }
                }
                window.initMap = function() {
                    const defaultPosition = {
                        lat: {{ $userLat }},
                        lng: {{ $userLng }}
                    };

                    map = new google.maps.Map(document.getElementById("map"), {
                        center: defaultPosition,
                        zoom: 14,
                    });

                    marker = new google.maps.Marker({
                        position: defaultPosition,
                        map: map,
                        draggable: true
                    });

                    marker.addListener("dragend", function(event) {
                        @this.set('userLat', event.latLng.lat());
                        @this.set('userLng', event.latLng.lng());
                    });

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const userPosition = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            map.setCenter(userPosition);
                            marker.setPosition(userPosition);

                            @this.set('userLat', userPosition.lat);
                            @this.set('userLng', userPosition.lng);
                            @this.set('gpsUsed', true);
                        }, function() {
                            @this.set('gpsUsed', false);
                        });
                    }
                };

                window.addEventListener('google-maps-ready', function() {
                    const defaultLat = {{ $userLat }};
                    const defaultLng = {{ $userLng }};
                    createMap(defaultLat, defaultLng);
                });

                function setToMyLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            const userPosition = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude
                            };

                            // Update posisi marker terlebih dahulu
                            if (marker) {
                                marker.setMap(null); // Hapus marker lama
                            }

                            marker = new google.maps.Marker({
                                position: userPosition,
                                map: map,
                                draggable: true,
                            });

                            // Tambahkan event dragend lagi
                            marker.addListener("dragend", function(event) {
                                @this.set('userLat', event.latLng.lat());
                                @this.set('userLng', event.latLng.lng());
                            });

                            map.setCenter(userPosition);

                            // Update ke Livewire
                            @this.set('userLat', userPosition.lat);
                            @this.set('userLng', userPosition.lng);
                        }, function() {
                            alert("Gagal mendapatkan lokasi.");
                        });
                    } else {
                        alert("Browser tidak mendukung geolokasi.");
                    }
                }

                function resetLocation() {
                    const defaultPosition = {
                        lat: -7.94666,
                        lng: 112.61828
                    };

                    if (marker) {
                        marker.setMap(null);
                    }

                    marker = new google.maps.Marker({
                        position: defaultPosition,
                        map: map,
                        draggable: true,
                    });

                    marker.addListener("dragend", function(event) {
                        @this.set('userLat', event.latLng.lat());
                        @this.set('userLng', event.latLng.lng());
                    });

                    map.setCenter(defaultPosition);

                    @this.set('userLat', defaultPosition.lat);
                    @this.set('userLng', defaultPosition.lng);
                }
            </script>




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
                        @if (isset($cafe->distance))
                            <p class="text-xs text-center text-gray-600">
                                {{ number_format($cafe->distance, 2) }} km dari posisi kamu
                            </p>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">Tidak ada cafe yang cocok.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
