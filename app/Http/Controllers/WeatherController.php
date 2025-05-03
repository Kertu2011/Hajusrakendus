<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Client\RequestException; // Import RequestException
use DateTime;
use DateTimeZone;

class WeatherController extends Controller
{
    // Default coordinates for a generic fallback (Tallinn, Estonia)
    private const DEFAULT_LAT = 59.436962;
    private const DEFAULT_LON = 24.753574;
    private const DEFAULT_LOCATION_NAME = 'Tallinn, Estonia';

    // Renders the Vue component
    public function show(Request $request)
    {
        return Inertia::render('Weather');
    }

    /**
     * Handles the frontend's request to fetch weather data.
     * /api/weather?location=...&days=...
     */
    public function getWeather(Request $request): JsonResponse
    {
        $locationInput = $request->query('location', self::DEFAULT_LOCATION_NAME);
        $days = max(1, min(14, (int) $request->query('days', 1)));

        // --- 1. Geocoding (Now calls internal helper using API) ---
        $coordinates = $this->getCoordinatesForLocation($locationInput);

        // Handle geocoding failure explicitly
        if ($coordinates['lat'] === null || $coordinates['lon'] === null) {
            Log::warning('Geocoding failed or returned no results for location', ['location' => $locationInput]);
            // Return a specific error message if the name wasn't resolved
            $errorMsg = $coordinates['name'] // Check if name is still the input or a default fallback indicator
                ? 'Could not find coordinates for the specified location: ' . $coordinates['name']
                : 'Could not determine coordinates for the location.';
            return response()->json(['error' => $errorMsg], 404); // Not Found is appropriate here
        }

        $latitude = $coordinates['lat'];
        $longitude = $coordinates['lon'];
        $locationName = $coordinates['name']; // Use the resolved name from geocoding

        // --- 2. Fetch Weather from Open-Meteo Forecast API ---
        $apiUrl = "https://api.open-meteo.com/v1/forecast";
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'daily' => 'temperature_2m_max,temperature_2m_min,sunrise,sunset,weather_code',
            'hourly' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation_probability,precipitation,weather_code,surface_pressure,cloud_cover,visibility,wind_speed_10m,wind_direction_10m,wind_gusts_10m,is_day',
            'timezone' => 'auto',
            'forecast_days' => $days
        ];

        // Cache forecast results
        $cacheKeyForecast = 'weather_forecast_' . md5(json_encode($params));
        $cacheDurationForecast = now()->addMinutes(15); // Cache forecast for 15 minutes

        try {
            $meteoData = Cache::remember($cacheKeyForecast, $cacheDurationForecast, function () use ($apiUrl, $params) {
                Log::info('Fetching weather forecast from Open-Meteo API', $params);
                $response = Http::timeout(10)->get($apiUrl, $params);
                if ($response->failed()) {
                     Log::error('Open-Meteo Forecast API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                    $response->throw(); // Throw exception to prevent caching failure
                }
                 Log::info('Open-Meteo Forecast API Response Status:', ['status' => $response->status()]);
                return $response->json();
            });

             if (!$meteoData) {
                Log::error('Failed to get valid JSON data from Open-Meteo forecast response/cache.');
                return response()->json(['error' => 'Received invalid data from weather provider.'], 502);
            }

            // --- 3. Transform Data ---
            $transformedData = $this->transformMeteoDataToFrontendFormat($meteoData, $locationName, $days);
            if (!$transformedData) {
                // Logged inside transformMeteoDataToFrontendFormat
                return response()->json(['error' => 'Failed to process weather data.'], 500);
            }

            // --- 4. Return Response ---
            return response()->json($transformedData);

        } catch (RequestException $e) {
            Log::error('HTTP Error fetching weather forecast: ' . $e->getMessage(), ['code' => $e->getCode()]);
            $statusCode = $e->response ? $e->response->status() : 503;
            $errorMsg = 'Failed to fetch weather data from provider.';
             if ($statusCode >= 400 && $statusCode < 500) $errorMsg = 'Invalid request to weather provider.';
            return response()->json(['error' => $errorMsg], $statusCode > 500 ? 502 : $statusCode);
        } catch (\Exception $e) {
            Log::error('General Error in getWeather: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['error' => 'An internal server error occurred.'], 500);
        }
    }

    // REMOVED: public geocode method is no longer needed

    // --- Private Helper Methods ---

    /**
     * Internal helper to get coordinates for a location string using Open-Meteo Geocoding API.
     * Caches the results. Returns null lat/lon on failure.
     *
     * @param string $locationInput The location name entered by the user.
     * @return array{lat: float|null, lon: float|null, name: string}
     */
    private function getCoordinatesForLocation(string $locationInput): array
    {
        $trimmedLocation = trim($locationInput);

        // Handle empty input or explicit default request
        if (empty($trimmedLocation) || strcasecmp($trimmedLocation, self::DEFAULT_LOCATION_NAME) === 0) {
            Log::info('Using default location.', ['input' => $locationInput]);
            return ['lat' => self::DEFAULT_LAT, 'lon' => self::DEFAULT_LON, 'name' => self::DEFAULT_LOCATION_NAME];
        }

        // Cache geocoding results to avoid repeated API calls for the same location name
        $cacheKeyGeo = 'geocode_' . md5(strtolower($trimmedLocation));
        // Cache for longer duration, e.g., 24 hours, as geocoding data changes less often
        $cacheDurationGeo = now()->addHours(24);

        try {
            $result = Cache::remember($cacheKeyGeo, $cacheDurationGeo, function () use ($trimmedLocation) {
                $apiUrl = 'https://geocoding-api.open-meteo.com/v1/search';
                $params = [
                    'name' => $trimmedLocation,
                    'count' => 1, // We only need the best match
                    'language' => 'en',
                    'format' => 'json',
                ];

                Log::info('Fetching geocoding data from Open-Meteo API', $params);
                $response = Http::timeout(5)->get($apiUrl, $params); // Shorter timeout for geocoding

                if ($response->failed()) {
                     Log::error('Open-Meteo Geocoding API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                     // Throwing an exception prevents caching the failure, which is good.
                    $response->throw();
                }

                $data = $response->json();

                // Check if results are present and valid
                if (isset($data['results']) && !empty($data['results']) && is_array($data['results'])) {
                    $firstResult = $data['results'][0];

                    // Construct a user-friendly name
                    $nameParts = array_filter([
                        $firstResult['name'] ?? null,
                        $firstResult['admin1'] ?? null, // e.g., State or Region
                        $firstResult['country'] ?? null,
                    ]);
                    $resolvedName = implode(', ', $nameParts);

                    Log::info('Geocoding successful', ['location' => $trimmedLocation, 'resolved' => $resolvedName]);
                    return [
                        'lat' => (float) ($firstResult['latitude'] ?? null),
                        'lon' => (float) ($firstResult['longitude'] ?? null),
                        'name' => $resolvedName ?: $trimmedLocation, // Fallback name if parts were missing
                    ];
                } else {
                     Log::warning('Geocoding API returned no results for location.', ['location' => $trimmedLocation, 'response' => $data]);
                     // Return nulls to indicate failure, keep original input as name for context
                     return ['lat' => null, 'lon' => null, 'name' => $trimmedLocation];
                }
            });

            // If cache returns null/false (shouldn't happen with throw() but good practice)
            if (!$result) {
                 Log::error('Geocoding cache returned invalid result.', ['key' => $cacheKeyGeo]);
                 return ['lat' => null, 'lon' => null, 'name' => $trimmedLocation]; // Indicate failure
            }

             // Ensure lat/lon are not null before returning if we got a result from cache/API
             if ($result['lat'] === null || $result['lon'] === null) {
                 Log::warning('Geocoding result missing coordinates.', ['result' => $result]);
                 // Ensure name reflects the input if coords are missing
                 return ['lat' => null, 'lon' => null, 'name' => $trimmedLocation];
             }


            return $result;

        } catch (RequestException $e) {
             Log::error('HTTP Error during geocoding: ' . $e->getMessage(), ['location' => $trimmedLocation, 'code' => $e->getCode()]);
             return ['lat' => null, 'lon' => null, 'name' => $trimmedLocation]; // Indicate failure
        } catch (\Exception $e) {
             Log::error('General Error during geocoding: ' . $e->getMessage(), ['location' => $trimmedLocation, 'exception' => $e]);
             return ['lat' => null, 'lon' => null, 'name' => $trimmedLocation]; // Indicate failure
        }
    }


    /**
     * Transforms the Open-Meteo Forecast API response into the format
     * expected by the Vue frontend.
     * (Implementation remains the same as before)
     * @param array $meteoData The raw data from Open-Meteo Forecast API.
     * @param string $locationName The resolved location name from geocoding.
     * @param int $requestedDays The number of forecast days requested.
     * @return array|null The transformed data structure or null on error.
     */
    private function transformMeteoDataToFrontendFormat(array $meteoData, string $locationName, int $requestedDays): ?array
    {
        // ... (Keep the existing implementation from the previous step) ...
        // This function finds the current hour, maps WMO codes, populates
        // the main weather details and the daily_forecast array.
        // No changes needed here based on the geocoding update.

         try {
            // Find the current hour's index (or use the first available hour)
            $now = time();
            $currentHourIndex = 0;
            if (isset($meteoData['hourly']['time'])) {
                 $hourlyTimes = $meteoData['hourly']['time'];
                foreach ($hourlyTimes as $index => $isoTime) {
                     // Handle potential invalid time strings gracefully
                     $timestamp = strtotime($isoTime);
                     if ($timestamp === false) continue; // Skip invalid time format

                    if ($timestamp >= $now) {
                        $currentHourIndex = $index;
                        break;
                    }
                    // If loop finishes without finding a future time, use the last index
                     if ($index === count($hourlyTimes) - 1) {
                         $currentHourIndex = $index;
                     }
                }
            } else {
                 Log::error("Open-Meteo hourly time data missing.");
                 return null;
            }


             // Basic safety checks for essential current data points
             if (!isset($meteoData['hourly']['temperature_2m'][$currentHourIndex]) ||
                 !isset($meteoData['daily']['temperature_2m_max'][0]) ||
                 !isset($meteoData['daily']['temperature_2m_min'][0]) ||
                 !isset($meteoData['hourly']['weather_code'][$currentHourIndex])
                ) {
                 Log::error('Open-Meteo data missing expected fields for current display.', [
                     'hourly_keys' => isset($meteoData['hourly']) ? array_keys($meteoData['hourly']) : 'missing',
                     'daily_keys' => isset($meteoData['daily']) ? array_keys($meteoData['daily']) : 'missing',
                     'currentHourIndex' => $currentHourIndex,
                     'hourly_time_count' => isset($meteoData['hourly']['time']) ? count($meteoData['hourly']['time']) : 0
                     ]);
                 return null; // Indicate failure
             }

            // Map current WMO code to description and OWM icon code
            $currentWmoCode = $meteoData['hourly']['weather_code'][$currentHourIndex] ?? 0;
             $isDay = $meteoData['hourly']['is_day'][$currentHourIndex] ?? 1; // Default to day if missing
            list($currentWeatherDesc, $currentWeatherIcon) = $this->mapWmoToOwm($currentWmoCode, $isDay);

            // --- Build the main structure resembling the OLD interface (for current conditions) ---
            $transformed = [
                'main' => [
                    'temp' => $meteoData['hourly']['temperature_2m'][$currentHourIndex] ?? null,
                    'feels_like' => $meteoData['hourly']['apparent_temperature'][$currentHourIndex] ?? null,
                    'temp_min' => $meteoData['daily']['temperature_2m_min'][0] ?? null, // Today's min
                    'temp_max' => $meteoData['daily']['temperature_2m_max'][0] ?? null, // Today's max
                    'pressure' => $meteoData['hourly']['surface_pressure'][$currentHourIndex] ?? null, // hPa
                    'humidity' => $meteoData['hourly']['relative_humidity_2m'][$currentHourIndex] ?? null, // %
                ],
                'weather' => [ // Array expected by frontend
                    [
                        'id' => $currentWmoCode, // Use WMO code
                        'main' => $currentWeatherDesc, // Use mapped description as main category
                        'description' => $currentWeatherDesc, // Use mapped description
                        'icon' => $currentWeatherIcon, // Use mapped icon
                    ]
                ],
                'base' => 'stations', // Placeholder value
                'visibility' => $meteoData['hourly']['visibility'][$currentHourIndex] ?? null, // meters
                'wind' => [
                    'speed' => $meteoData['hourly']['wind_speed_10m'][$currentHourIndex] ?? null, // m/s
                    'deg' => $meteoData['hourly']['wind_direction_10m'][$currentHourIndex] ?? null, // degrees
                    'gust' => $meteoData['hourly']['wind_gusts_10m'][$currentHourIndex] ?? null, // m/s
                ],
                'clouds' => [
                    'all' => $meteoData['hourly']['cloud_cover'][$currentHourIndex] ?? null, // %
                ],
                'dt' => (new DateTime($meteoData['hourly']['time'][$currentHourIndex], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'), // Unix timestamp
                'sys' => [
                    'timezone_abbreviation' => $meteoData['timezone_abbreviation'], // e.g., GMT, CEST
                    'sunrise' => (new DateTime($meteoData['daily']['sunrise'][0], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'), // Today's sunrise
                    'sunset' => (new DateTime($meteoData['daily']['sunset'][0], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'),   // Today's sunset
                ],
                'timezone' => $meteoData['utc_offset_seconds'] ?? 0, // Timezone offset in seconds
                'name' => $locationName, // Use the name from geocoding
                'cod' => 200, // HTTP status placeholder

                // --- ADDITION: Include daily forecast data ---
                'daily_forecast' => [] // Add a new key for the forecast array
            ];

            // --- Populate the daily forecast ---
            if (isset($meteoData['daily']['time'])) {
                 $dayCount = min(count($meteoData['daily']['time']), $requestedDays); // Ensure we don't exceed available data or request
                 for ($i = 0; $i < $dayCount; $i++) {
                     // Check if data exists for this index before accessing
                     if (!isset($meteoData['daily']['time'][$i])) continue;

                    // Need weather code for the day - use the code from the daily results
                    $dailyWmoCode = $meteoData['daily']['weather_code'][$i] ?? 0;
                    // For daily, assume 'day' icon unless API provides better info
                    list($dailyDesc, $dailyIcon) = $this->mapWmoToOwm($dailyWmoCode, 1); // Force day icon for daily summary

                    $transformed['daily_forecast'][] = [
                        'dt' => (new DateTime($meteoData['daily']['time'][$i], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'),
                        'date_str' => $meteoData['daily']['time'][$i], // Keep ISO string too
                        'temp_min' => $meteoData['daily']['temperature_2m_min'][$i] ?? null,
                        'temp_max' => $meteoData['daily']['temperature_2m_max'][$i] ?? null,
                        'sunrise' => (new DateTime($meteoData['daily']['sunrise'][$i], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'),
                        'sunset' => (new DateTime($meteoData['daily']['sunset'][$i], new DateTimeZone($meteoData['timezone_abbreviation'])))->format('U'),
                        'weather' => [ // Keep similar structure for consistency
                             'id' => $dailyWmoCode,
                             'main' => $dailyDesc,
                             'description' => $dailyDesc,
                             'icon' => $dailyIcon,
                        ]
                        // Add other daily fields if needed (e.g., precipitation sum)
                    ];
                }
            }


            return $transformed;

        } catch (\Exception $e) {
            Log::error('Error transforming Open-Meteo data: ' . $e->getMessage(), [
                'exception' => $e,
                'meteo_data_keys' => array_keys($meteoData) // Log keys to help debug structure issues
                ]);
            return null;
        }
    }


    /**
     * Maps WMO Weather Interpretation Codes (WW) to description and OWM icon code.
     * (Implementation remains the same as before)
      * @param int $wmoCode The WMO code.
      * @param int|null $isDay Flag indicating if it's daytime (1) or nighttime (0). Affects icon choice.
      * @return array [string $description, string $iconCode]
      */
    private function mapWmoToOwm(int $wmoCode, ?int $isDay = 1): array
    {
        // ... (Keep the existing implementation from the previous step) ...
         $description = 'Unknown';
        $iconSuffix = ($isDay === 0) ? 'n' : 'd'; // Use 'n' for night, 'd' for day
        $icon = '01'; // Default: clear

        // Determine base icon and description based on WMO code
        if ($wmoCode == 0) { $description = 'Clear sky'; $icon = '01'; }
        elseif ($wmoCode == 1) { $description = 'Mainly clear'; $icon = '01'; } // Could also use 02?
        elseif ($wmoCode == 2) { $description = 'Partly cloudy'; $icon = '02'; }
        elseif ($wmoCode == 3) { $description = 'Overcast'; $icon = '04'; } // 04 icon is same day/night
        elseif ($wmoCode == 45) { $description = 'Fog'; $icon = '50'; } // 50 icon is same day/night
        elseif ($wmoCode == 48) { $description = 'Depositing rime fog'; $icon = '50'; }
        elseif ($wmoCode >= 51 && $wmoCode <= 55) { // Drizzle
             $description = ($wmoCode == 51) ? 'Light drizzle' : (($wmoCode == 53) ? 'Moderate drizzle' : 'Dense drizzle');
             $icon = '09'; // shower rain icon
        } elseif ($wmoCode == 56 || $wmoCode == 57) { // Freezing Drizzle
            $description = ($wmoCode == 56) ? 'Light freezing drizzle' : 'Dense freezing drizzle';
            $icon = '09'; // Use shower rain icon + freezing context implied by temp? OWM has no specific icon. Could use '13' (snow). Let's use '09'.
        } elseif ($wmoCode >= 61 && $wmoCode <= 65) { // Rain
            $description = ($wmoCode == 61) ? 'Slight rain' : (($wmoCode == 63) ? 'Moderate rain' : 'Heavy rain');
            $icon = '10'; // rain icon
        } elseif ($wmoCode == 66 || $wmoCode == 67) { // Freezing Rain
            $description = ($wmoCode == 66) ? 'Light freezing rain' : 'Heavy freezing rain';
            $icon = '10'; // Use rain icon. Could use '13' (snow).
        } elseif ($wmoCode >= 71 && $wmoCode <= 75) { // Snow fall
            $description = ($wmoCode == 71) ? 'Slight snow fall' : (($wmoCode == 73) ? 'Moderate snow fall' : 'Heavy snow fall');
            $icon = '13'; // snow icon
        } elseif ($wmoCode == 77) { $description = 'Snow grains'; $icon = '13'; }
        elseif ($wmoCode >= 80 && $wmoCode <= 82) { // Rain showers
            $description = ($wmoCode == 80) ? 'Slight rain showers' : (($wmoCode == 81) ? 'Moderate rain showers' : 'Violent rain showers');
            $icon = '09'; // shower rain icon
        } elseif ($wmoCode == 85 || $wmoCode == 86) { // Snow showers
            $description = ($wmoCode == 85) ? 'Slight snow showers' : 'Heavy snow showers';
            $icon = '13'; // snow icon
        } elseif ($wmoCode == 95) { // Thunderstorm slight/moderate
             $description = 'Thunderstorm';
             $icon = '11'; // thunderstorm icon
        } elseif ($wmoCode == 96 || $wmoCode == 99) { // Thunderstorm with hail
             $description = ($wmoCode == 96) ? 'Thunderstorm with slight hail' : 'Thunderstorm with heavy hail';
             $icon = '11';
         }

        // Append day/night suffix, except for icons that don't vary (04, 50)
        if (!in_array($icon, ['04', '50'])) {
            $icon .= $iconSuffix;
        } else {
             $icon .= 'd'; // OWM uses 'd' variants for these (e.g., 04d, 50d)
        }


        return [$description, $icon];
    }
}