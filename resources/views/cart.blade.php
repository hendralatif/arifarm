@extends('layouts.public')

@section('title', 'Keranjang Belanja Anda - Ari Farm')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold font-display text-slate-900 dark:text-white">Keranjang Belanja</h1>
            <p class="text-slate-500 text-sm mt-1">Periksa kembali daftar kambing pilihan Anda sebelum meminangnya.</p>
        </div>

        @if(count($cart) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Cart Items Table -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800 pb-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                    <th class="pb-4">Hewan / Paket</th>
                                    <th class="pb-4 text-right">Harga Satuan</th>
                                    <th class="pb-4 text-center">Jumlah</th>
                                    <th class="pb-4 text-right">Total</th>
                                    <th class="pb-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($cart as $id => $item)
                                    <tr class="align-middle">
                                        <!-- Product Detail -->
                                        <td class="py-6">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 flex-shrink-0">
                                                    <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-1">
                                                        {{ $item['name'] }}
                                                    </h3>
                                                    <span class="text-xs text-slate-400">
                                                        Ras: {{ $item['breed'] }} | {{ $item['weight'] }} kg
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Unit Price -->
                                        <td class="py-6 text-right font-semibold text-sm text-slate-800 dark:text-slate-200">
                                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                                        </td>

                                        <!-- Quantity -->
                                        <td class="py-6">
                                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center justify-center">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $id }}">
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" onchange="this.form.submit()" class="w-16 text-center text-sm font-semibold rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white py-1">
                                            </form>
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="py-6 text-right font-bold text-sm text-slate-900 dark:text-white">
                                            Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                        </td>

                                        <!-- Delete -->
                                        <td class="py-6 text-center">
                                            <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-rose-500 hover:text-rose-700 transition" title="Hapus">
                                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-between">
                        <a href="{{ route('catalog') }}" class="inline-flex items-center text-sm font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Tambah Kambing Lainnya
                        </a>
                    </div>
                </div>

                <!-- Order Summary Side Card -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ringkasan Belanja</h2>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-slate-500">
                                <span>Total Item</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ count($cart) }} Kambing</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Subtotal</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $formattedTotal }}</span>
                            </div>
                            <div class="flex justify-between text-slate-500">
                                <span>Pengiriman (Simulasi)</span>
                                <span class="font-semibold text-emerald-600">Gratis Ongkir</span>
                            </div>
                        </div>

                        <hr class="border-slate-100 dark:border-slate-800">

                        <div class="flex justify-between">
                            <span class="font-bold text-slate-950 dark:text-white text-base">Total Bayar</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-lg">{{ $formattedTotal }}</span>
                        </div>

                        <!-- Info Alert -->
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/40 rounded-xl text-xs flex items-start space-x-2">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-base"></i>
                            <span>Kambing dikirim langsung menggunakan armada angkut kami untuk menjamin kondisi kesehatan hewan terjaga.</span>
                        </div>

                        <!-- Check out Button -->
                        @auth
                            <a href="{{ route('checkout') }}" class="w-full flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/10 transition">
                                Lanjutkan Ke Checkout <i class="fa-solid fa-arrow-right ml-2"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}?redirect=cart.index" class="w-full flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold bg-amber-500 hover:bg-amber-600 text-slate-950 shadow-lg shadow-amber-500/10 transition">
                                Masuk Akun untuk Checkout <i class="fa-solid fa-sign-in ml-2"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart View -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-16 text-center space-y-5 shadow-sm max-w-2xl mx-auto">
                <div class="text-slate-300 text-7xl">
                    <i class="fa-solid fa-shopping-basket"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Keranjang Anda Masih Kosong</h2>
                <p class="text-slate-500">Anda belum memilih kambing atau domba untuk dibeli. Silakan lihat katalog hewan ternak pilihan kami.</p>
                <a href="{{ route('catalog') }}" class="inline-flex px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-lg shadow-emerald-600/10 transition">
                    Jelajahi Katalog Kambing
                </a>
            </div>
        @endif

    </div>
</section>
@endsection
