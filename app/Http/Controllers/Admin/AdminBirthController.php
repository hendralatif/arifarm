<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goat;
use App\Models\GoatBirth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminBirthController extends Controller
{
    public function index(Request $request)
    {
        $query = GoatBirth::with(['mother', 'father', 'recorder'])->latest('birth_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('mother', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $births     = $query->paginate(15)->withQueryString();
        $totalBirths = GoatBirth::count();
        $totalKids   = GoatBirth::sum('total_kids');
        $thisMonthBirths = GoatBirth::whereMonth('birth_date', now()->month)->count();

        $femaleGoats = Goat::where('gender', 'female')->orderBy('name')->get(['id', 'name', 'breed']);
        $maleGoats   = Goat::where('gender', 'male')->orderBy('name')->get(['id', 'name', 'breed']);

        return view('admin.births.index', compact('births', 'totalBirths', 'totalKids', 'thisMonthBirths', 'femaleGoats', 'maleGoats'));
    }

    public function create()
    {
        $femaleGoats = Goat::where('gender', 'female')->orderBy('name')->get(['id', 'name', 'breed']);
        $maleGoats   = Goat::where('gender', 'male')->orderBy('name')->get(['id', 'name', 'breed']);
        return view('admin.births.create', compact('femaleGoats', 'maleGoats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mother_id'       => 'required|exists:goats,id',
            'father_id'       => 'nullable|exists:goats,id',
            'birth_date'      => 'required|date|before_or_equal:today',
            'total_kids'      => 'required|integer|min:1|max:10',
            'male_count'      => 'required|integer|min:0',
            'female_count'    => 'required|integer|min:0',
            'stillborn_count' => 'required|integer|min:0',
            'birth_condition' => 'required|in:normal,assisted,cesarean',
            'mother_condition'=> 'required|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ], [
            'mother_id.required'    => 'Induk betina wajib dipilih.',
            'birth_date.required'   => 'Tanggal lahir wajib diisi.',
            'total_kids.required'   => 'Jumlah anak lahir wajib diisi.',
        ]);

        // Validate that counts add up correctly
        $maleCount      = (int) $request->male_count;
        $femaleCount    = (int) $request->female_count;
        $stillbornCount = (int) $request->stillborn_count;
        $totalKids      = (int) $request->total_kids;

        if ($maleCount + $femaleCount + $stillbornCount !== $totalKids) {
            return redirect()->back()->withInput()->withErrors([
                'total_kids' => 'Jumlah jantan (' . $maleCount . ') + betina (' . $femaleCount . ') + lahir mati (' . $stillbornCount . ') = ' . ($maleCount + $femaleCount + $stillbornCount) . ' harus sama dengan total anak (' . $totalKids . ').',
            ]);
        }

        $mother = Goat::findOrFail($request->mother_id);

        $birth = GoatBirth::create([
            ...$request->only([
                'mother_id', 'father_id', 'birth_date', 'total_kids',
                'male_count', 'female_count', 'stillborn_count',
                'birth_condition', 'mother_condition', 'notes',
            ]),
            'recorded_by' => Auth::id(),
        ]);

        // Auto-generate Goats in database for LIVE births only (stillborns are NOT added to stock)
        // Male Kids
        for ($i = 1; $i <= $maleCount; $i++) {
            $kidName = "Cempe Jantan #" . $i . " dari " . $mother->name;
            $slug = Str::slug($kidName) . '-' . Str::random(4);
            Goat::create([
                'category_id'    => $mother->category_id,
                'name'           => $kidName,
                'slug'           => $slug,
                'description'    => "Anak kambing jantan lahir dari induk {$mother->name} pada {$birth->birth_date->format('d M Y')}.",
                'price'          => 0,
                'stock'          => 1,
                'weight_kg'      => 3.50,
                'age_months'     => 0,
                'gender'         => 'male',
                'breed'          => $mother->breed,
                'health_status'  => 'healthy',
                'vaccine_status' => false,
                'images'         => ['https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=800&auto=format&fit=crop'],
                'status'         => 'available',
                'acquisition_type' => 'kelahiran',
            ]);
        }

        // Female Kids
        for ($i = 1; $i <= $femaleCount; $i++) {
            $kidName = "Cempe Betina #" . $i . " dari " . $mother->name;
            $slug = Str::slug($kidName) . '-' . Str::random(4);
            Goat::create([
                'category_id'    => $mother->category_id,
                'name'           => $kidName,
                'slug'           => $slug,
                'description'    => "Anak kambing betina lahir dari induk {$mother->name} pada {$birth->birth_date->format('d M Y')}.",
                'price'          => 0,
                'stock'          => 1,
                'weight_kg'      => 3.50,
                'age_months'     => 0,
                'gender'         => 'female',
                'breed'          => $mother->breed,
                'health_status'  => 'healthy',
                'vaccine_status' => false,
                'images'         => ['https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=800&auto=format&fit=crop'],
                'status'         => 'available',
                'acquisition_type' => 'kelahiran',
            ]);
        }

        // NOTE: Stillborn kids are NOT added to stock — recorded statistically only.
        $liveBirths = $maleCount + $femaleCount;
        $successMsg = 'Data kelahiran berhasil dicatat.';
        if ($liveBirths > 0) {
            $successMsg .= ' ' . $liveBirths . ' anak kambing hidup telah ditambahkan ke stok.';
        }
        if ($stillbornCount > 0) {
            $successMsg .= ' ' . $stillbornCount . ' lahir mati hanya dicatat secara statistik (tidak masuk stok).';
        }

        return redirect()->route('admin.births.index')->with('success', $successMsg);
    }

    public function destroy($id)
    {
        $birth = GoatBirth::findOrFail($id);
        $birth->delete();

        return redirect()->route('admin.births.index')
            ->with('success', 'Data kelahiran berhasil dihapus.');
    }
}
