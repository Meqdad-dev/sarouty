<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GeoService
{
    protected string $nominatimUrl = 'https://nominatim.openstreetmap.org';
    protected string $userAgent;

    public function __construct()
    {
        $this->userAgent = config('services.nominatim.user_agent', 'Sarouty/1.0 (contact@sarouty.ma)');
    }

    /**
     * Géocode une adresse → coordonnées lat/lng via OpenStreetMap Nominatim.
     */
    public function geocode(string $address, string $city = '', string $country = 'Maroc'): ?array
    {
        $query    = trim("{$address} {$city} {$country}");
        $cacheKey = 'geo_' . md5($query);

        return Cache::remember($cacheKey, 86400, function () use ($query) {
            try {
                // Respect du rate limit Nominatim (1 req/s)
                sleep(1);

                $response = Http::withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Accept'     => 'application/json',
                ])->get("{$this->nominatimUrl}/search", [
                    'q'              => $query,
                    'format'         => 'json',
                    'limit'          => 1,
                    'countrycodes'   => 'ma',
                    'addressdetails' => 1,
                ]);

                if ($response->successful() && !empty($response->json())) {
                    $result = $response->json()[0];
                    return [
                        'lat'         => (float) $result['lat'],
                        'lng'         => (float) $result['lon'],
                        'display'     => $result['display_name'],
                        'type'        => $result['type'] ?? null,
                        'importance'  => $result['importance'] ?? null,
                    ];
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Geocode error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Géocodage inverse : coordonnées → adresse.
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $cacheKey = "revgeo_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 86400, function () use ($lat, $lng) {
            try {
                sleep(1);

                $response = Http::withHeaders(['User-Agent' => $this->userAgent])
                    ->get("{$this->nominatimUrl}/reverse", [
                        'lat'            => $lat,
                        'lon'            => $lng,
                        'format'         => 'json',
                        'addressdetails' => 1,
                    ]);

                if ($response->successful()) {
                    $data    = $response->json();
                    $address = $data['address'] ?? [];
                    return [
                        'display'  => $data['display_name'] ?? '',
                        'city'     => $address['city'] ?? $address['town'] ?? $address['village'] ?? '',
                        'suburb'   => $address['suburb'] ?? $address['neighbourhood'] ?? '',
                        'postcode' => $address['postcode'] ?? '',
                        'country'  => $address['country'] ?? 'Maroc',
                    ];
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Reverse geocode error: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Retourne les coordonnées d'une ville marocaine (fallback statique).
     */
    public function getCityCoordinates(string $city): array
    {
        $cities = [
            'Casablanca' => ['lat' => 33.5731, 'lng' => -7.5898],
            'Rabat'      => ['lat' => 34.0209, 'lng' => -6.8416],
            'Marrakech'  => ['lat' => 31.6295, 'lng' => -7.9811],
            'Fès'        => ['lat' => 34.0181, 'lng' => -5.0078],
            'Tanger'     => ['lat' => 35.7595, 'lng' => -5.8340],
            'Agadir'     => ['lat' => 30.4278, 'lng' => -9.5981],
            'Meknès'     => ['lat' => 33.8931, 'lng' => -5.5473],
            'Oujda'      => ['lat' => 34.6814, 'lng' => -1.9086],
            'Kénitra'    => ['lat' => 34.2610, 'lng' => -6.5802],
            'Tétouan'    => ['lat' => 35.5785, 'lng' => -5.3684],
            'Salé'       => ['lat' => 34.0365, 'lng' => -6.8225],
            'Essaouira'  => ['lat' => 31.5085, 'lng' => -9.7595],
            'El Jadida'  => ['lat' => 33.2549, 'lng' => -8.5000],
            'Ifrane'     => ['lat' => 33.5228, 'lng' => -5.1100],
        ];

        return $cities[$city] ?? ['lat' => 31.7917, 'lng' => -7.0926]; // Centre du Maroc
    }

    /**
     * Génère les tiles OpenStreetMap pour Leaflet.
     */
    public function getTileUrl(): string
    {
        return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    }

    public function getTileAttribution(): string
    {
        return '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
    }
}
