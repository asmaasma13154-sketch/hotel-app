<?php
namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::withCount('rooms');

        if ($request->city) {
            $query->inCity($request->city);
        }
        if ($request->stars) {
            $query->where('stars', $request->stars);
        }

        $hotels = $query->paginate(9);
        $cities = Hotel::distinct()->pluck('city');

        return view('hotels.index', compact('hotels', 'cities'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load('rooms');
        $rooms = $hotel->rooms()->available()->get();
        return view('hotels.show', compact('hotel', 'rooms'));
    }

    public function create()
    {
        return view('admin.hotels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'address'     => 'required|string',
            'description' => 'nullable|string',
            'stars'       => 'required|integer|between:1,5',
            'phone'       => 'nullable|string',
            'email'       => 'nullable|email',
        ]);

        Hotel::create($validated);
        return redirect()->route('hotels.index')->with('success', 'Hôtel créé avec succès.');
    }

    public function edit(Hotel $hotel)
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'city'        => 'required|string|max:100',
            'address'     => 'required|string',
            'stars'       => 'required|integer|between:1,5',
        ]);

        $hotel->update($validated);
        return redirect()->route('hotels.index')->with('success', 'Hôtel modifié.');
    }

    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return redirect()->route('hotels.index')->with('success', 'Hôtel supprimé.');
    }
}