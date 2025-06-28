<?php
namespace App\Exports;

use App\Models\Rangking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankingExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Rangking::with('cafe')
            ->orderBy('peringkat', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'Cafe' => $item->cafe->name,
                    'C1' => $item->c1,
                    'C2' => $item->c2,
                    'C3' => $item->c3,
                    'C4' => $item->c4,
                    'C5' => $item->c5,
                    'C6' => $item->c6,
                    'C7' => $item->c7,
                    'C8' => $item->c8,
                    'Preferensi' => $item->score,
                    'Peringkat' => $item->peringkat,
                ];
            });
    }

    public function headings(): array
    {
        return ['Cafe', 'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'Preferensi', 'Peringkat'];
    }
}

