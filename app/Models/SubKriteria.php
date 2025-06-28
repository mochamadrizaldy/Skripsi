<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'kriteria_id', 'nilai'];

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
