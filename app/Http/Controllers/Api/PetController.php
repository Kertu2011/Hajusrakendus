<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $limit = $validated['limit'] ?? 15;
        $offset = $validated['offset'] ?? 0;

        $pets = $this->rememberPetsCache($limit, $offset);

        return response()->json($pets);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'species' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female,unknown',
            'approximate_age' => ['nullable', 'string', 'regex:/^\d+\s(months|years)$/i'],
        ]);
        $pet = new Pet();
        $pet->fill($validatedData);
        $pet->user_id = $request->user()->id;
        $pet->save();

        // Clear cache after adding a new pet
        $this->clearPetsCache();

        return response()->json([
            'message' => 'Pet created successfully.',
            'pet' => $pet,
        ], 201);
    }

    private function rememberPetsCache(int $limit, int $offset, int $minutes = 60): Collection
    {
        $cacheKey = sprintf('pets:limit_%d:offset_%d', $limit, $offset);

        $retrievePets = function () use ($limit, $offset) {
            return Pet::query()
                ->orderBy('created_at', 'desc') // Add explicit ordering for consistency
                ->limit($limit)
                ->offset($offset)
                ->get();
        };

        if (Cache::supportsTags()) {
            return Cache::tags(['pets'])
                ->remember($cacheKey, now()->addMinutes($minutes), $retrievePets);
        }

        return Cache::remember($cacheKey, now()->addMinutes($minutes), $retrievePets);
    }

    private function clearPetsCache(): void
    {
        if (Cache::supportsTags()) {
            Cache::tags(['pets'])->flush();
        } else {
            Cache::flush();
        }
    }
}
