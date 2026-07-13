<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goat;
use Illuminate\Http\Request;

class GoatController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        
        $query = Goat::with('category')->whereIn('status', ['available', 'not_for_sale']);

        // Search Name/Description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('breed', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Gender Filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // Weight Filter
        if ($request->filled('weight_min')) {
            $query->where('weight_kg', '>=', $request->input('weight_min'));
        }
        if ($request->filled('weight_max')) {
            $query->where('weight_kg', '<=', $request->input('weight_max'));
        }

        // Price Filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'weight_desc') {
            $query->orderBy('weight_kg', 'desc');
        } else {
            $query->latest();
        }

        $goats = $query->paginate(9)->withQueryString();

        return view('catalog', compact('goats', 'categories'));
    }

    public function show($slug)
    {
        $goat = Goat::with('category')->where('slug', $slug)->firstOrFail();
        
        // Fetch related goats from the same breed / category
        $relatedGoats = Goat::where('category_id', $goat->category_id)
            ->where('id', '!=', $goat->id)
            ->whereIn('status', ['available', 'not_for_sale'])
            ->take(3)
            ->get();

        return view('goats.show', compact('goat', 'relatedGoats'));
    }
}
