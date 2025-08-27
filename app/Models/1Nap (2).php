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

    protected $appends = ['lat','lng'];

    public function olt()
    {
        return $this->belongsTo(\App\Models\Olt::class);
    }

    /** 
     * Accesores para lat/lng derivados de gps si no hay columnas explícitas.
     * Acepta formatos: "-23.12,-64.32", "(-23.12, -64.32)", "lat:-23.12 lng:-64.32", con espacios/; etc.
     */
    public function getLatAttribute()
    {
        // Si existe columna 'lat', úsala
        if (array_key_exists('lat', $this->attributes) && is_numeric($this->attributes['lat'])) {
            return (float) $this->attributes['lat'];
        }
        [$lat, $lng] = $this->parseGps($this->attributes['gps'] ?? null);
        return $lat;
    }

    public function getLngAttribute()
    {
        if (array_key_exists('lng', $this->attributes) && is_numeric($this->attributes['lng'])) {
            return (float) $this->attributes['lng'];
        }
        [$lat, $lng] = $this->parseGps($this->attributes['gps'] ?? null);
        return $lng;
    }

    protected function parseGps($gps): array
    {
        if (!$gps) return [null, null];
        if (preg_match_all('/-?\d+(?:\.\d+)?/', (string) $gps, $m)) {
            if (count($m[0]) >= 2) {
                return [floatval($m[0][0]), floatval($m[0][1])];
            }
        }
        return [null, null];
    }
}
