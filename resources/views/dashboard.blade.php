@extends('layouts.public')

@section('title', 'Dashboard Akun Saya - ARI FARM')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold font-display text-slate-900 dark:text-white">Dashboard Akun</h1>
                <p class="text-slate-500 text-sm mt-1">Kelola pesanan, unggah bukti pembayaran, dan sunting profil Anda.</p>
            </div>
            
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <a href="{{ route('profile.edit') }}" class="w-1/2 sm:w-auto text-center px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white hover:bg-slate-50 transition">
                    <i class="fa-solid fa-user-gear mr-2"></i> Edit Profil
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-1/2 sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full text-center px-5 py-2.5 rounded-xl text-sm font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                        <i class="fa-solid fa-sign-out mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: User Profile Details Summary -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Info Profile Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4 text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400 text-3xl font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">{{ Auth::user()->name }}</h3>
                        <span class="text-xs text-slate-400">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-400">
                            Member Terdaftar
                        </span>
                    </div>
                </div>

                <!-- Short Quick Stats -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm grid grid-cols-2 gap-4">
                    <div class="text-center space-y-1">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $orders->count() }}</span>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Total Order</span>
                    </div>
                    <div class="text-center border-l border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                            {{ $orders->where('status', 'completed')->count() }}
                        </span>
                        <span class="text-[10px] text-slate-400 block uppercase font-bold">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Right: Order History -->
            <div class="lg:col-span-9 space-y-6">
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Riwayat Transaksi</h2>

                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-800 pb-4 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        <th class="pb-3">No. Invoice</th>
                                        <th class="pb-3">Tanggal Pemesanan</th>
                                        <th class="pb-3 text-right">Total Transaksi</th>
                                        <th class="pb-3 text-center">Status Pesanan</th>
                                        <th class="pb-3 text-center">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @foreach($orders as $order)
                                        <tr class="align-middle">
                                            <!-- Invoice -->
                                            <td class="py-4 font-bold text-sm text-slate-900 dark:text-white">
                                                {{ $order->invoice_number }}
                                            </td>

                                            <!-- Date -->
                                            <td class="py-4 text-sm text-slate-500">
                                                {{ $order->created_at->format('d M Y, H:i') }} WIB
                                            </td>

                                            <!-- Total Amount -->
                                            <td class="py-4 text-right font-bold text-sm text-slate-800 dark:text-slate-200">
                                                {{ $order->formatted_total }}
                                            </td>

                                            <!-- Status -->
                                            <td class="py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->status_badge_class }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>

                                            <!-- Action -->
                                            <td class="py-4 text-center">
                                                <a href="{{ route('orders.show', $order->id) }}" class="inline-flex px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 hover:bg-emerald-100 transition font-bold text-xs">
                                                    Lihat <i class="fa-solid fa-eye ml-1 mt-0.5"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- No Orders -->
                        <div class="text-center py-12 space-y-4">
                            <div class="text-slate-300 text-6xl">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-base">Belum Ada Transaksi</h3>
                            <p class="text-sm text-slate-500 max-w-sm mx-auto">Anda belum pernah memesan kambing di ARI FARM. Temukan kambing impian Anda sekarang.</p>
                            <a href="{{ route('catalog') }}" class="inline-flex px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition">
                                Belanja Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
