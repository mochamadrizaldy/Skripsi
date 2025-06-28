<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'kategori', 'bobot', 'keterangan', 'created_at'];

    public function alternative(): HasMany
    {
        return $this->hasMany(Alternatif::class);
    }

    public function sub_kriteria(): HasMany
    {
        return $this->hasMany(SubKriteria::class);
    }
}
