<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Goat;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Core Summary Stats
        $totalSales = Order::where('status', 'completed')->sum('total_amount');
        $totalOrdersCount = Order::count();
        $totalGoatsCount = Goat::count();
        $totalCustomersCount = User::where('role', 'user')->count();

        // 2. Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 3. Product inventory status
        $availableGoats = Goat::where('status', 'available')->count();
        $soldGoats = Goat::where('status', 'sold')->count();

        // 4. Sales performance by status
        $orderStats = Order::select('status', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        // 5. Popular breeds
        $popularBreeds = Goat::select('breed', DB::raw('count(*) as count'), DB::raw('sum(price) as total'))
            ->groupBy('breed')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrdersCount',
            'totalGoatsCount',
            'totalCustomersCount',
            'recentOrders',
            'availableGoats',
            'soldGoats',
            'orderStats',
            'popularBreeds'
        ));
    }
}
