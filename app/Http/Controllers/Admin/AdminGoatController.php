<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Goat;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminGoatController extends Controller
{
    public function index(Request $request)
    {
        $query = Goat::with('category');

        // Pencarian nama / breed
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Jenis Kelamin
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter Asal Usul
        if ($request->filled('acquisition_type')) {
            $query->where('acquisition_type', $request->acquisition_type);
        }

        // Filter Kesehatan
        if ($request->filled('health_status')) {
            $query->where('health_status', $request->health_status);
        }

        // Filter Harga Min
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }

        // Filter Harga Maks
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'name_asc'    => $query->orderBy('name', 'asc'),
            'name_desc'   => $query->orderBy('name', 'desc'),
            'price_asc'   => $query->orderBy('price', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc'),
            'weight_desc' => $query->orderBy('weight_kg', 'desc'),
            'weight_asc'  => $query->orderBy('weight_kg', 'asc'),
            default       => $query->latest(),
        };

        $goats      = $query->paginate(15)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        // Hitung jumlah filter aktif
        $filterCount = collect(['status','gender','category','acquisition_type','health_status','price_min','price_max'])
            ->filter(fn($key) => $request->filled($key))
            ->count();

        return view('admin.goats.index', compact('goats', 'categories', 'filterCount'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.goats.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required_if:acquisition_type,beli|nullable|numeric|min:0',
            'stock' => 'required|integer|min:1',
            'weight_kg' => 'required|numeric|min:0',
            'age_months' => 'required|integer|min:0',
            'gender' => 'required|in:male,female',
            'breed' => 'required|string|max:255',
            'health_status' => 'required|in:healthy,vaccine_completed,under_observation',
            'vaccine_status' => 'required|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:available,sold,not_for_sale,mati',
            'acquisition_type' => 'required|in:beli,kelahiran,lainnya',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imageName = 'goat_' . time() . '_' . Str::random(5) . '.' . $file->extension();
                $file->move(public_path('uploads/goats'), $imageName);
                $images[] = 'uploads/goats/' . $imageName;
            }
        } else {
            // Default image if none provided
            $images[] = 'https://images.unsplash.com/photo-1608755728617-aefab37d2edd?w=800&auto=format&fit=crop';
        }

        $count = $request->input('stock', 1);

        for ($i = 1; $i <= $count; $i++) {
            $name = $request->name;
            if ($count > 1) {
                $name = $name . ' #' . $i;
            }

            $goat = Goat::create([
                'category_id' => $request->category_id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(4),
                'description' => $request->description,
                'price' => $request->price,
                'purchase_price' => $request->acquisition_type == 'beli' ? $request->purchase_price : null,
                'stock' => 1, // Always 1 per record
                'weight_kg' => $request->weight_kg,
                'age_months' => $request->age_months,
                'gender' => $request->gender,
                'breed' => $request->breed,
                'health_status' => $request->health_status,
                'vaccine_status' => $request->vaccine_status,
                'images' => $images,
                'status' => $request->status,
                'acquisition_type' => $request->acquisition_type,
            ]);

            if ($request->acquisition_type == 'beli' && $request->filled('purchase_price')) {
                \App\Models\Expense::create([
                    'expense_date' => now(),
                    'category' => 'pembelian_hewan',
                    'title' => 'Pembelian Kambing: ' . $goat->name,
                    'amount' => $request->purchase_price,
                    'description' => 'Biaya pembelian kambing ras ' . $goat->breed . ' (' . $goat->name . ')',
                    'recorded_by' => auth()->id() ?? 1,
                    'goat_id' => $goat->id,
                ]);
            }
        }

        return redirect()->route('admin.goats.index')->with('success', 'Kambing berhasil ditambahkan.');
    }

    public function show($id)
    {
        $goat = Goat::with(['weighings' => function($q) {
            $q->orderBy('weighed_at', 'asc');
        }, 'category'])->findOrFail($id);

        // Monthly maintenance cost estimates (per animal)
        $pakanCost      = 450000;  // Rp 450.000/bulan pakan
        $vaksinCost     = 75000;   // Rp 75.000/bulan vaksin
        $checkupCost    = 50000;   // Rp 50.000/bulan pemeriksaan
        $totalMaintenance = $pakanCost + $vaksinCost + $checkupCost;

        return view('admin.goats.show', compact('goat', 'pakanCost', 'vaksinCost', 'checkupCost', 'totalMaintenance'));
    }

    public function addWeighing(Request $request, $id)
    {
        $goat = Goat::findOrFail($id);

        $request->validate([
            'weight_kg' => 'required|numeric|min:0',
            'weighed_at' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $goat->weighings()->create([
            'weight_kg' => $request->weight_kg,
            'weighed_at' => $request->weighed_at,
            'notes' => $request->notes,
        ]);

        // Update current weight in goats table
        $goat->weight_kg = $request->weight_kg;
        $goat->save();

        return redirect()->route('admin.goats.show', $goat->id)->with('success', 'Riwayat penimbangan berhasil dicatat dan bobot utama terupdate.');
    }

    public function edit(Goat $goat)
    {
        $categories = Category::all();
        return view('admin.goats.edit', compact('goat', 'categories'));
    }

    public function update(Request $request, Goat $goat)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required_if:acquisition_type,beli|nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight_kg' => 'required|numeric|min:0',
            'age_months' => 'required|integer|min:0',
            'gender' => 'required|in:male,female',
            'breed' => 'required|string|max:255',
            'health_status' => 'required|in:healthy,vaccine_completed,under_observation',
            'vaccine_status' => 'required|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'status' => 'required|in:available,sold,not_for_sale,mati',
            'acquisition_type' => 'required|in:beli,kelahiran,lainnya',
        ]);

        $images = $goat->images ?: [];
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $file) {
                $imageName = 'goat_' . time() . '_' . Str::random(5) . '.' . $file->extension();
                $file->move(public_path('uploads/goats'), $imageName);
                $newImages[] = 'uploads/goats/' . $imageName;
            }
            $images = $newImages; // Replace old images
        }

        $goat->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'description' => $request->description,
            'price' => $request->price,
            'purchase_price' => $request->acquisition_type == 'beli' ? $request->purchase_price : null,
            'stock' => $request->stock,
            'weight_kg' => $request->weight_kg,
            'age_months' => $request->age_months,
            'gender' => $request->gender,
            'breed' => $request->breed,
            'health_status' => $request->health_status,
            'vaccine_status' => $request->vaccine_status,
            'images' => $images,
            'status' => $request->status,
            'acquisition_type' => $request->acquisition_type,
        ]);

        if ($request->acquisition_type == 'beli' && $request->filled('purchase_price')) {
            \App\Models\Expense::updateOrCreate(
                ['goat_id' => $goat->id],
                [
                    'expense_date' => $goat->created_at ?: now(),
                    'category' => 'pembelian_hewan',
                    'title' => 'Pembelian Kambing: ' . $goat->name,
                    'amount' => $request->purchase_price,
                    'description' => 'Biaya pembelian kambing ras ' . $goat->breed . ' (' . $goat->name . ')',
                    'recorded_by' => auth()->id() ?? 1,
                ]
            );
        } else {
            \App\Models\Expense::where('goat_id', $goat->id)->delete();
        }

        return redirect()->route('admin.goats.index')->with('success', 'Kambing berhasil diperbarui.');
    }

    public function destroy(Goat $goat)
    {
        // Delete image files from storage if necessary, then delete model
        if (is_array($goat->images)) {
            foreach ($goat->images as $path) {
                $fullPath = public_path($path);
                if (file_exists($fullPath) && !str_starts_with($path, 'http')) {
                    unlink($fullPath);
                }
            }
        }

        $goat->delete();
        return redirect()->route('admin.goats.index')->with('success', 'Kambing berhasil dihapus.');
    }
}
