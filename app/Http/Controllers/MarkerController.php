<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Inertia\Inertia; // Impordi Inertia

class MarkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Seda meetodit kasutame kaardi lehe kuvamiseks, edastades kõik markerid
        return Inertia::render('Map/Index', [
            'markers' => Marker::orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        Marker::create($validated);

        return redirect()->route('map.index')->with('success', 'Marker added successfully!');
    }

    /**
     * Display the specified resource.
     * (Võib-olla pole eraldi vaadet vaja, kui haldus toimub kaardil)
     */
    public function show(Marker $marker)
    {
        // Võid tagastada JSON kujul, kui vaja
        return response()->json($marker);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marker $marker)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string',
        ]);

        $marker->update($validated);

        return redirect()->route('map.index')->with('success', 'Marker updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marker $marker)
    {
        $marker->delete();
        return redirect()->route('map.index')->with('success', 'Marker deleted successfully!');
    }
}