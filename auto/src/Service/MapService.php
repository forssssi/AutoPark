<?php

namespace App\Service;

class MapService
{
    /**
     * Get a simple route between two points given as "lat,lon" strings.
     * Returns ['distance' => kilometers]
     */
    public function getRoute(string $from, string $to): array
    {
        [$lat1, $lon1] = array_map('floatval', explode(',', $from));
        [$lat2, $lon2] = array_map('floatval', explode(',', $to));

        $distanceKm = $this->haversine($lat1, $lon1, $lat2, $lon2);

        return [
            'distance' => $distanceKm,
            'from' => $from,
            'to' => $to,
        ];
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371.0; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }
}
