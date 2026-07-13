<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')->where('role', 'user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $totalCustomers = User::where('role', 'user')->count();
        $activeCustomers = User::where('role', 'user')
            ->whereHas('orders', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })->count();

        return view('admin.users.index', compact('users', 'totalCustomers', 'activeCustomers'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ], [
            'name.required'      => 'Nama pelanggan wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Pelanggan \"{$request->name}\" berhasil ditambahkan.");
    }

    public function show($id)
    {
        $user = User::with(['orders' => function ($q) {
            $q->latest()->take(10);
        }])->findOrFail($id);

        $totalOrders    = $user->orders()->count();
        $totalSpent     = $user->orders()->where('status', 'completed')->sum('total_amount');
        $cancelledOrders = $user->orders()->where('status', 'cancelled')->count();

        return view('admin.users.show', compact('user', 'totalOrders', 'totalSpent', 'cancelledOrders'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak bisa menghapus akun administrator.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }

        $name = $user->name;

        // Cancel active orders first
        $user->orders()->whereNotIn('status', ['completed', 'cancelled'])->each(function ($order) {
            foreach ($order->items as $item) {
                $goat = $item->goat;
                if ($goat) {
                    $goat->stock += $item->quantity;
                    if ($goat->status === 'sold') {
                        $goat->status = 'available';
                    }
                    $goat->save();
                }
            }
            $order->status = 'cancelled';
            $order->save();
        });

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Akun pelanggan \"{$name}\" berhasil dihapus.");
    }
}
