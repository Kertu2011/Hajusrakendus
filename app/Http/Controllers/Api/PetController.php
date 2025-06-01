<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'limit' => 'integer|min:1|max:100',
            'offset' => 'integer|min:0',
        ]);

        $pets = Pet::query()
            ->when($request->has('limit'), function ($query) use ($validated) {
                return $query->limit($validated['limit']);
            })
            ->when($request->has('offset'), function ($query) use ($validated) {
                return $query->offset($validated['offset']);
            })
            ->get();

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

        return response()->json([
            'message' => 'Pet created successfully.',
            'pet' => $pet,
        ], 201);
    }
}
