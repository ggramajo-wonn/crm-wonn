<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nap extends Model
{
    use HasFactory;

    // Ajustá si tu tabla o fillable son distintos
    protected $fillable = [
        'name', 'gps', 'puertos', 'ubicacion', 'olt_id', 'detalles'
    ];

    protected $casts = [
        'puertos' => 'integer',
    ];

    // Exponer lat/lng derivados del string 'gps' ("-23.12,-64.32")
    protected $appends = ['lat', 'lng'];

    /**
     * Devuelve [lat, lng] a partir del string gps.
     */
    protected function parseGps(): array
    {
        $gps = (string) ($this->gps ?? '');
        $p = array_map('trim', explode(',', $gps));
        $lat = isset($p[0]) && is_numeric($p[0]) ? (float) $p[0] : null;
        $lng = isset($p[1]) && is_numeric($p[1]) ? (float) $p[1] : null;
        return [$lat, $lng];
    }

    public function getLatAttribute(): ?float
    {
        return $this->parseGps()[0];
    }

    public function getLngAttribute(): ?float
    {
        return $this->parseGps()[1];
    }
}
