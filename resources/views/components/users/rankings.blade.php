@extends('components.layouts.beranda')

@section('content')
    <div class="min-h-screen bg-[#FCF9F4] flex flex-col items-center py-6">
        <h2 class="text-xl font-bold mb-6 text-center">Daftar Ranking Cafe</h2>

        <!-- Box dummy -->
        <div class="flex justify-center mb-10">
            <div class="w-56 h-32 bg-gray-200"></div>
        </div>

        <!-- Query langsung di blade -->
        @php
            use App\Models\Rangking;
            $rankings = Rangking::with('cafe.alternatifs.kriteria.sub_kriteria')
                ->orderBy('peringkat', 'asc')
                ->paginate(6);
        @endphp

        <!-- Card Grid -->
        <div class="w-full max-w-5xl flex justify-center">
            <div class="bg-[#EDE1D4] rounded-xl w-full px-4 py-10 flex flex-col items-center">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 mb-6">
                    @foreach ($rankings as $ranking)
                        @php $cafe = $ranking->cafe; @endphp
                        <div class="w-60 bg-[#FCF9F4] rounded-lg shadow-md p-4 cursor-pointer"
                            onclick="openPopup({{ $cafe->id }})">
                            <img src="{{ asset($cafe->gambar) }}" alt="{{ $cafe->name }}"
                                class="w-full h-32 object-cover rounded-md mb-2">
                            <h3 class="font-bold text-lg">{{ $cafe->name }}</h3>
                            <p class="text-sm">Peringkat: {{ $ranking->peringkat }}</p>
                            <p class="text-sm">Sosmed: {{ $cafe->sosmed }}</p>
                            <p class="text-sm">Lat: {{ $cafe->latitude }}</p>
                            <p class="text-sm">Long: {{ $cafe->longitude }}</p>
                        </div>

                        <!-- Popup Detail -->
                        <div id="popup-{{ $cafe->id }}"
                            class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg w-[90%] max-w-2xl overflow-y-auto max-h-[90vh]">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-bold">Detail Cafe</h2>
                                    <button onclick="closePopup({{ $cafe->id }})"
                                        class="text-red-500 font-bold">X</button>
                                </div>
                                <img src="{{ asset($cafe->gambar) }}" alt="{{ $cafe->name }}"
                                    class="w-full h-48 object-cover rounded mb-4">
                                <p><strong>Nama:</strong> {{ $cafe->name }}</p>
                                <p><strong>Sosmed:</strong> {{ $cafe->sosmed }}</p>
                                <p><strong>Latitude:</strong> {{ $cafe->latitude }}</p>
                                <p><strong>Longitude:</strong> {{ $cafe->longitude }}</p>
                                <hr class="my-3">
                                <h4 class="text-lg font-semibold mb-2">Detail Penilaian</h4>
                                <ul class="list-disc pl-5">
                                    @foreach ($cafe->alternatifs as $alt)
                                        <li>
                                            <strong>{{ $alt->kriteria->name }}:</strong>
                                            <ul class="list-disc pl-5">
                                                @foreach ($alt->kriteria->sub_kriteria as $sub)
                                                    @if ($sub->nilai === $alt->value)
                                                        <li>{{ $sub->name }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 flex justify-center">
                    <div class="bg-white px-4 py-2 rounded shadow-md">
                        {{ $rankings->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPopup(id) {
            document.getElementById(`popup-${id}`).classList.remove('hidden');
        }

        function closePopup(id) {
            document.getElementById(`popup-${id}`).classList.add('hidden');
        }
    </script>
@endsection
