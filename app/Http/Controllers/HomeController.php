<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Goat;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('goats')->get();
        
        $featuredGoats = Goat::with('category')
            ->whereIn('status', ['available', 'not_for_sale'])
            ->latest()
            ->take(4)
            ->get();

        // Get some stats for the banner
        $stats = [
            'total_goats' => Goat::count(),
            'sold_goats' => Goat::where('status', 'sold')->count() + 142, // Add offset for mock realistic values
            'happy_customers' => 98,
            'trusted_partners' => 12,
        ];

        return view('welcome', compact('categories', 'featuredGoats', 'stats'));
    }
}
