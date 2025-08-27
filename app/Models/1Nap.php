<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nap extends Model
{
    use HasFactory;

    protected $fillable = [
        'olt_id',
        'name',
        'ubicacion',
        'gps',
        'puertos',
        'detalles',
    ];

    public function olt()
    {
        return $this->belongsTo(\App\Models\Olt::class);
    }
}
