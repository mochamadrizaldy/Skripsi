<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $fillable = ['cafe_id', 'kriteria_id', 'value'];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function cafe()
    {
        return $this->belongsTo(Cafe::class);
    }

    public function sub_kriteria()
    {
        return $this->belongsTo(SubKriteria::class, 'value', 'nilai');
    }
}
