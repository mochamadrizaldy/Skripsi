<div class="container mx-auto p-4">
    <h1 class="text-xl font-bold text-center mb-4">User Rankings</h1>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="table-auto w-full text-left">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2">Rank</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rankings as $index => $user)
                    <tr class="{{ $index % 2 == 0 ? 'bg-gray-100' : '' }}">
                        <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2 text-center">{{ $user->score }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    @media (max-width: 640px) {
        table {
            font-size: 0.875rem;
        }

        th,
        td {
            padding: 0.5rem;
        }
    }
</style>
