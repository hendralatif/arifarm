<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goat;
use App\Models\GoatWeighing;
use Illuminate\Http\Request;

class AdminWeighingController extends Controller
{
    public function index(Request $request)
    {
        $query = GoatWeighing::with('goat.category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('goat', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        $weighings = $query->latest('weighed_at')->latest('id')->paginate(15)->withQueryString();

        // Active goats list for dropdown
        $goats = Goat::where('status', 'available')->orderBy('name')->get();

        // Calculate summary stats
        $avgWeight = Goat::where('status', 'available')->avg('weight_kg') ?? 0;
        $maxWeight = Goat::where('status', 'available')->max('weight_kg') ?? 0;
        $totalLogs = GoatWeighing::count();

        return view('admin.weighings.index', compact('weighings', 'goats', 'avgWeight', 'maxWeight', 'totalLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'goat_id' => 'required|exists:goats,id',
            'weight_kg' => 'required|numeric|min:0.1',
            'weighed_at' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        // Create weighing log
        GoatWeighing::create([
            'goat_id' => $request->goat_id,
            'weight_kg' => $request->weight_kg,
            'weighed_at' => $request->weighed_at,
            'notes' => $request->notes,
        ]);

        // Update current weight in goats table
        $goat = Goat::findOrFail($request->goat_id);
        $goat->weight_kg = $request->weight_kg;
        $goat->save();

        return redirect()->route('admin.weighings.index')->with('success', 'Catatan penimbangan berhasil ditambahkan dan bobot kambing telah diperbarui.');
    }

    public function destroy($id)
    {
        $weighing = GoatWeighing::findOrFail($id);
        $weighing->delete();

        return redirect()->route('admin.weighings.index')->with('success', 'Catatan penimbangan berhasil dihapus.');
    }
}
