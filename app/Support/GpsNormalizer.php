<?php

namespace App\Support;

class GpsNormalizer
{
    /**
     * Normaliza y valida una coordenada ingresada como string.
     * Acepta formatos: "lat,lng" o "lat , lng".
     * Devuelve "lat,lng" con punto decimal o null si no es válido.
     */
    public static function normalize(?string $input): ?string
    {
        $input = trim((string)$input);
        if ($input === '') return null;

        // Reemplazar separadores comunes y limpiar espacios duplicados
        $input = str_replace([';', '|', '\t'], ',', $input);
        $input = preg_replace('/\s+/', ' ', $input);

        // Si viene "lat lng" con espacio, convertir a coma
        if (strpos($input, ',') === false && strpos($input, ' ') !== false) {
            $input = preg_replace('/\s+/', ',', $input);
        }

        // Partir en lat/lng
        if (strpos($input, ',') === false) return null;
        [$lat, $lng] = array_map('trim', explode(',', $input, 2));

        // Reemplazar coma decimal por punto si viniese así
        $lat = str_replace(',', '.', $lat);
        $lng = str_replace(',', '.', $lng);

        if (!is_numeric($lat) || !is_numeric($lng)) return null;

        $lat = (float)$lat;
        $lng = (float)$lng;

        // Validar rangos
        if ($lat < -90 || $lat > 90) return null;
        if ($lng < -180 || $lng > 180) return null;

        // Formato final con hasta 6 decimales
        return sprintf('%.6f,%.6f', $lat, $lng);
    }
}
