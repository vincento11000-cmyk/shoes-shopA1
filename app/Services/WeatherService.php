<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log; // Add this line

class WeatherService
{
    public function getWeatherWarning()
    {
        // Cache for 30 minutes to reduce API calls
        return Cache::remember('weather_warning', 1800, function () {
            // Manila coordinates for Philippines
            $latitude = 14.5995;
            $longitude = 120.9842;

            try {
                $response = Http::timeout(5)->get("https://api.open-meteo.com/v1/forecast", [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'current_weather' => true,   // ← External API endpoint
                    'timezone' => 'Asia/Manila',
                ]);

                if ($response->failed()) {
                    Log::warning('Weather API request failed');
                    return null;
                }

                $weather = $response->json();

                if (!isset($weather['current_weather']['weathercode'])) {
                    Log::warning('Weather API response missing weathercode');
                    return null;
                }

                $code = $weather['current_weather']['weathercode'];
                
                // WMO Weather Codes that indicate bad weather
                $badWeatherCodes = [
                    // Rain (51-67)
                    51, 52, 53, 54, 55, // Drizzle
                    56, 57,             // Freezing drizzle
                    61, 62, 63, 64, 65, // Rain
                    66, 67,             // Freezing rain
                    // Rain showers (80-82)
                    80, 81, 82,
                    // Snow (71-77, 85-86)
                    71, 72, 73, 74, 75, // Snow fall
                    77,                 // Snow grains
                    85, 86,             // Snow showers
                    // Thunderstorm (95-99)
                    95, 96, 99,
                ];

                if (in_array($code, $badWeatherCodes)) {
                    $description = $this->getWeatherDescription($code);
                    $temperature = $weather['current_weather']['temperature'] ?? null;
                    
                    Log::info('Bad weather detected', [
                        'code' => $code,
                        'description' => $description,
                        'temperature' => $temperature
                    ]);
                    
                    return [
                        'warning' => "Delivery may be delayed due to $description.",
                        'description' => $description,
                        'temperature' => round($temperature, 1),
                        'code' => $code
                    ];
                }

                Log::debug('Weather conditions normal', ['code' => $code]);
                return null;
            } catch (\Exception $e) {
                Log::error('Weather API Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    private function getWeatherDescription($code)
    {
        $descriptions = [
            0 => 'clear sky',
            1 => 'mainly clear',
            2 => 'partly cloudy',
            3 => 'overcast',
            45 => 'fog',
            48 => 'rime fog',
            51 => 'light drizzle',
            53 => 'moderate drizzle',
            55 => 'heavy drizzle',
            56 => 'light freezing drizzle',
            57 => 'heavy freezing drizzle',
            61 => 'slight rain',
            63 => 'moderate rain',
            65 => 'heavy rain',
            66 => 'light freezing rain',
            67 => 'heavy freezing rain',
            71 => 'slight snow',
            73 => 'moderate snow',
            75 => 'heavy snow',
            77 => 'snow grains',
            80 => 'slight rain showers',
            81 => 'moderate rain showers',
            82 => 'violent rain showers',
            85 => 'slight snow showers',
            86 => 'heavy snow showers',
            95 => 'thunderstorm',
            96 => 'thunderstorm with hail',
            99 => 'thunderstorm with heavy hail',
        ];

        return $descriptions[$code] ?? 'unfavorable weather conditions';
    }
    
    // Optional: Add a method to manually clear cache for testing
    public function clearCache()
    {
        Cache::forget('weather_warning');
        return true;
    }
}