<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

interface DailyForecastItem {
    dt: number;
    date_str: string;
    temp_min: number | null;
    temp_max: number | null;
    sunrise: number | null;
    sunset: number | null;
    weather: { 
        id: number;
        main: string;
        description: string;
        icon: string;
    } | null;
}

interface WeatherData {
    main: {
        temp: number | null;
        feels_like: number | null;
        temp_min: number | null; 
        temp_max: number | null; 
        pressure: number | null;
        humidity: number | null;
    };
    weather: Array<{ 
        id: number;
        main: string;
        description: string;
        icon: string;
    }>; 
    base: string | null;
    visibility: number | null;
    wind: {
        speed: number | null;
        deg: number | null;
        gust?: number | null; 
    };
    clouds: {
        all: number | null;
    };
    dt: number; 
    sys: {
        type: number | null;
        id: number | null;
        country: string | null; // e.g., Timezone Abbreviation like GMT, CEST
        sunrise: number | null; // Today's sunrise
        sunset: number | null; // Today's sunset
    };
    timezone: number; // Offset in seconds
    id: number | null; // Often null from Open-Meteo
    name: string; // Location name
    cod: number; // Status code placeholder

    // Added daily forecast array
    daily_forecast?: DailyForecastItem[]; // Make optional for safety
}

// --- Rest of the script setup ---

const form = useForm({
    location: '',
    days: '1', // Default to 1 day
});

// Update the ref type
const weatherData = ref<WeatherData | null>(null);
const loading = ref(false);
const error = ref<string | null>(null);

const fetchWeather = async () => {
    weatherData.value = null;
    error.value = null;
    loading.value = true;

    try {
        const response = await axios.get('/api/weather', {
            params: {
                location: form.location,
                days: form.days,
            },
        });
        // Assuming response.data matches the updated WeatherData interface
        weatherData.value = response.data;
    } catch (err: any) {
        if (err.response && err.response.data && err.response.data.error) {
            error.value = err.response.data.error;
        } else if (err.message) {
             error.value = `Network Error: ${err.message}`;
        }
        else {
            error.value = 'An error occurred while fetching weather data.';
        }
        console.error("Weather fetch error:", err);
    } finally {
        loading.value = false;
    }
};

// Icon URL function (remains the same)
const getWeatherIconUrl = (iconCode: string | undefined | null): string => {
    if (!iconCode) return ''; // Handle missing icon code
    return `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
};

// --- Formatting Helpers ---

// Format Unix timestamp to a short date string (e.g., "Wed, Apr 23")
const formatDate = (timestamp: number | undefined | null): string => {
    if (timestamp === undefined || timestamp === null) return 'N/A';
    const date = new Date(timestamp * 1000);
    return date.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
};

// Format Unix timestamp to a time string (e.g., "6:15 AM") - Optional
const formatTime = (timestamp: number | undefined | null): string => {
     if (timestamp === undefined || timestamp === null) return 'N/A';
     const date = new Date(timestamp * 1000);
     return date.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit', hour12: true });
};


// Format temperature (round and add degree symbol)
const formatTemp = (temp: number | undefined | null): string => {
    if (temp === undefined || temp === null) return 'N/A';
    return `${Math.round(temp)}°C`; // Or °F depending on unit preference
};

// Format wind speed
const formatSpeed = (speed: number | undefined | null): string => {
     if (speed === undefined || speed === null) return 'N/A';
     return `${speed.toFixed(1)} m/s`; // Adjust units/precision if needed
};

// Format pressure
const formatPressure = (pressure: number | undefined | null): string => {
     if (pressure === undefined || pressure === null) return 'N/A';
     return `${pressure.toFixed(1)} hPa`;
};

// Format visibility
const formatVisibility = (visibility: number | undefined | null): string => {
     if (visibility === undefined || visibility === null) return 'N/A';
     if (visibility > 1000) {
         return `${(visibility / 1000).toFixed(1)} km`;
     }
     return `${visibility} m`;
};
</script>

<template>
  <div class="container mx-auto p-4">
      <h1 class="text-2xl font-bold mb-4">Weather Information</h1>

      <form @submit.prevent="fetchWeather" class="mb-6 flex flex-col sm:flex-row gap-2">
          <input
              type="text"
              v-model="form.location"
              placeholder="Enter city or country (e.g., Tallinn)"
              class="border rounded py-2 px-4 flex-grow focus:ring-2 focus:ring-blue-300 focus:outline-none"
              required
          />
          <select
              v-model="form.days"
              class="border rounded py-2 px-4 w-full sm:w-auto focus:ring-2 focus:ring-blue-300 focus:outline-none"
          >
              <option value="1">Current</option>
              <option value="3">3 Days</option>
              <option value="7">7 Days</option>
              <option value="14">14 Days</option>
          </select>
          <button
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 transition duration-150 ease-in-out"
              :disabled="loading || !form.location"
          >
              <span v-if="loading">Loading...</span>
              <span v-else>Get Weather</span>
          </button>
      </form>

      <div v-if="loading" class="text-center py-10">
          <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          Loading weather data...
      </div>

      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
          <strong class="font-bold">Error!</strong>
          <span class="block sm:inline"> {{ error }}</span>
      </div>

      <div v-if="weatherData && !loading && !error" class="space-y-6">

          <div class="rounded-lg shadow-md p-6 border border-gray-200">
              <h2 class="text-xl font-semibold mb-1">
                  Current Weather in {{ weatherData.name }}
                  <span v-if="weatherData.sys?.country">({{ weatherData.sys.country }})</span>
              </h2>
               <p class="text-sm mb-4">As of {{ formatTime(weatherData.dt) }}</p>

              <div class="flex flex-col sm:flex-row items-center sm:items-start mb-4">
                  <img
                      v-if="weatherData.weather && weatherData.weather.length > 0 && weatherData.weather[0].icon"
                      :src="getWeatherIconUrl(weatherData.weather[0].icon)"
                      :alt="weatherData.weather[0].description || 'Weather icon'"
                      class="w-20 h-20 mr-0 sm:mr-4 mb-2 sm:mb-0 drop-shadow-lg"
                  />
                  <div class="text-center sm:text-left">
                      <p class="text-4xl font-bold">{{ formatTemp(weatherData.main.temp) }}</p>
                      <p v-if="weatherData.weather && weatherData.weather.length > 0" class="text-lg capitalize">
                          {{ weatherData.weather[0].description }}
                      </p>
                      <p class="text-sm">
                           Feels like {{ formatTemp(weatherData.main.feels_like) }}
                      </p>
                  </div>
              </div>

              <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-2 text-sm">
                  <div><span class="font-semibold">Humidity:</span> {{ weatherData.main.humidity ?? 'N/A' }}%</div>
                  <div><span class="font-semibold">Pressure:</span> {{ formatPressure(weatherData.main.pressure) }}</div>
                  <div><span class="font-semibold">Visibility:</span> {{ formatVisibility(weatherData.visibility) }}</div>
                  <div><span class="font-semibold">Wind:</span> {{ formatSpeed(weatherData.wind.speed) }} <span v-if="weatherData.wind.deg"> ({{ weatherData.wind.deg }}°)</span></div>
                   <div v-if="weatherData.wind.gust"><span class="font-semibold">Gusts:</span> {{ formatSpeed(weatherData.wind.gust) }}</div>
                   <div><span class="font-semibold">Clouds:</span> {{ weatherData.clouds.all ?? 'N/A' }}%</div>
                  <div><span class="font-semibold">Sunrise:</span> {{ formatTime(weatherData.sys.sunrise) }}</div>
                  <div><span class="font-semibold">Sunset:</span> {{ formatTime(weatherData.sys.sunset) }}</div>
                  <div><span class="font-semibold">Day Min:</span> {{ formatTemp(weatherData.main.temp_min) }}</div>
                  <div><span class="font-semibold">Day Max:</span> {{ formatTemp(weatherData.main.temp_max) }}</div>
              </div>
          </div>

          <div v-if="weatherData.daily_forecast && weatherData.daily_forecast.length > 0" class="rounded-lg shadow-md p-6 border border-gray-200">
              <h2 class="text-xl font-semibold mb-4">{{ weatherData.daily_forecast.length }}-Day Forecast</h2>
               <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                  <div
                      v-for="day in weatherData.daily_forecast"
                      :key="day.date_str"
                      class="rounded p-3 text-center border border-gray-100 hover:shadow-sm transition-shadow duration-150"
                   >
                       <p class="font-semibold text-sm">{{ formatDate(day.dt) }}</p>
                       <img
                           v-if="day.weather?.icon"
                           :src="getWeatherIconUrl(day.weather.icon)"
                          :alt="day.weather.description || 'Weather icon'"
                           class="w-12 h-12 mx-auto my-1 drop-shadow-sm"
                       />
                      <p v-if="day.weather?.description" class="text-xs capitalize mb-1">
                          {{ day.weather.description }}
                      </p>
                      <p class="text-sm font-medium">
                          {{ formatTemp(day.temp_max) }} <span class="">/ {{ formatTemp(day.temp_min) }}</span>
                      </p>
                       </div>
               </div>
          </div>

      </div> </div>
</template>