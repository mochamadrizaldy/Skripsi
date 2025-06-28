<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rangking extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_id',
        'score',
        'peringkat',
    ];

    public function cafe() {
        return $this->belongsTo(Cafe::class );
    }
}
