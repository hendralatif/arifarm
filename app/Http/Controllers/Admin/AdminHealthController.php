<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goat;
use App\Models\GoatHealthRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHealthController extends Controller
{
    public function index(Request $request)
    {
        $query = GoatHealthRecord::with(['goat', 'recorder'])->latest('check_date');

        if ($request->filled('status')) {
            $query->where('health_status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('record_type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('goat', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('diagnosis', 'like', "%{$search}%");
        }

        $records    = $query->paginate(15)->withQueryString();
        $sickCount  = GoatHealthRecord::where('health_status', 'sick')->orWhere('health_status', 'critical')->count();
        $vaccinatedThisMonth = GoatHealthRecord::where('record_type', 'vaccination')
            ->whereMonth('check_date', now()->month)->count();
        $upcomingCheckups = GoatHealthRecord::whereNotNull('next_checkup')
            ->whereDate('next_checkup', '>=', today())
            ->whereDate('next_checkup', '<=', today()->addDays(7))
            ->count();

        $goats = Goat::orderBy('name')->get(['id', 'name', 'breed', 'gender']);

        return view('admin.health.index', compact('records', 'sickCount', 'vaccinatedThisMonth', 'upcomingCheckups', 'goats'));
    }

    public function create()
    {
        $goats = Goat::orderBy('name')->get(['id', 'name', 'breed', 'gender']);
        return view('admin.health.create', compact('goats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'goat_id'       => 'required|exists:goats,id',
            'check_date'    => 'required|date',
            'record_type'   => 'required|in:checkup,vaccination,treatment,observation',
            'health_status' => 'required|in:healthy,sick,recovering,critical',
            'diagnosis'     => 'nullable|string|max:255',
            'treatment'     => 'nullable|string|max:255',
            'medicine'      => 'nullable|string|max:255',
            'medicine_dose' => 'nullable|numeric|min:0',
            'vet_name'      => 'nullable|string|max:100',
            'next_checkup'  => 'nullable|date|after:today',
            'notes'         => 'nullable|string|max:500',
        ]);

        GoatHealthRecord::create([
            ...$request->only([
                'goat_id', 'check_date', 'record_type', 'health_status',
                'diagnosis', 'treatment', 'medicine', 'medicine_dose',
                'vet_name', 'next_checkup', 'notes',
            ]),
            'recorded_by' => Auth::id(),
        ]);

        // Update goat health_status jika berubah
        $goat = Goat::find($request->goat_id);
        $statusMap = ['healthy' => 'healthy', 'sick' => 'under_observation', 'recovering' => 'under_observation', 'critical' => 'under_observation'];
        if ($goat && isset($statusMap[$request->health_status])) {
            $goat->health_status = $statusMap[$request->health_status];
            $goat->save();
        }

        return redirect()->route('admin.health.index')
            ->with('success', 'Catatan kesehatan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $record = GoatHealthRecord::with(['goat', 'recorder'])->findOrFail($id);
        return view('admin.health.show', compact('record'));
    }

    public function destroy($id)
    {
        $record = GoatHealthRecord::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.health.index')
            ->with('success', 'Catatan kesehatan berhasil dihapus.');
    }
}
