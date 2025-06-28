<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cafe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gambar',
        'sosmed',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
    ];

    public function alternatifs(): HasMany
    {
        return $this->hasMany(Alternatif::class);
    }

    public function rangking(): HasMany {
        return $this->hasMany(Rangking::class);
    }
}
