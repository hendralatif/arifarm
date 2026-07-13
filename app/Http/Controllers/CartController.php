<?php

namespace App\Http\Controllers;

use App\Models\Goat;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        $formattedTotal = 'Rp ' . number_format($total, 0, ',', '.');
        
        return view('cart', compact('cart', 'total', 'formattedTotal'));
    }

    public function add(Request $request, $id)
    {
        $goat = Goat::findOrFail($id);

        if ($goat->status !== 'available') {
            return redirect()->back()->with('error', 'Kambing ini sudah terjual.');
        }

        $cart = session()->get('cart', []);

        // check if product already in cart
        if (isset($cart[$id])) {
            // For packaged products, check stock limit
            if ($cart[$id]['quantity'] < $goat->stock) {
                $cart[$id]['quantity']++;
            } else {
                return redirect()->back()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
        } else {
            $cart[$id] = [
                "name" => $goat->name,
                "quantity" => 1,
                "price" => $goat->price,
                "image" => $goat->first_image,
                "breed" => $goat->breed,
                "weight" => $goat->weight_kg,
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Kambing berhasil ditambahkan ke keranjang belanja!');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);
            $goat = Goat::findOrFail($request->id);

            if ($request->quantity <= $goat->stock && $request->quantity > 0) {
                $cart[$request->id]["quantity"] = $request->quantity;
                session()->put('cart', $cart);
                session()->flash('success', 'Keranjang berhasil diperbarui.');
            } else {
                session()->flash('error', 'Jumlah melebihi stok yang tersedia.');
            }
        }
        return redirect()->route('cart.index');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('checkout', compact('cart', 'total'));
    }
}
