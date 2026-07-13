@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Dashboard Analisis</h1>
            <p class="text-sm text-slate-500">Gambaran kinerja penjualan kambing, inventaris stok, dan pesanan terkini.</p>
        </div>
        
        <div class="flex items-center text-xs font-bold text-slate-400 bg-white dark:bg-slate-900 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <i class="fa-solid fa-calendar mr-2 text-emerald-500"></i> Data Per: Hari Ini, {{ date('d M Y') }}
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stat 1 -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center space-x-5">
            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Total Penjualan</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">
                    Rp {{ number_format($totalSales, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Stat 2 -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center space-x-5">
            <div class="p-4 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/20 dark:text-blue-400 text-2xl">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Total Pesanan</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $totalOrdersCount }} Order</span>
            </div>
        </div>

        <!-- Stat 3 -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center space-x-5">
            <div class="p-4 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-2xl">
                <i class="fa-solid fa-cow"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Total Kambing</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $totalGoatsCount }} Ekor</span>
            </div>
        </div>

        <!-- Stat 4 -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm flex items-center space-x-5">
            <div class="p-4 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/20 dark:text-indigo-400 text-2xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold block uppercase">Pelanggan</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">{{ $totalCustomersCount }} Orang</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders (Left 2 cols) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pesanan Terbaru</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Semua Pesanan</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 pb-3">
                            <th class="pb-3">Invoice</th>
                            <th class="pb-3">Pelanggan</th>
                            <th class="pb-3 text-right">Total</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($recentOrders as $order)
                            <tr class="align-middle">
                                <td class="py-3.5 font-bold text-slate-900 dark:text-white">{{ $order->invoice_number }}</td>
                                <td class="py-3.5 text-slate-600 dark:text-slate-400">{{ $order->user->name }}</td>
                                <td class="py-3.5 text-right font-semibold">{{ $order->formatted_total }}</td>
                                <td class="py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1 px-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-xs font-bold transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory / Stock Overview (Right 1 col) -->
        <div class="space-y-6">
            <!-- Inventory Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Inventaris Kambing</h3>
                
                <div class="space-y-4">
                    <!-- Stat Available -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-circle text-emerald-500 text-xs mr-2"></i> Kambing Ready</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{{ $availableGoats }} Ekor</span>
                    </div>

                    <!-- Stat Sold -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-circle text-rose-500 text-xs mr-2"></i> Kambing Terjual</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{{ $soldGoats }} Ekor</span>
                    </div>

                    <!-- Stock fill bar -->
                    @php
                        $totalStock = max(1, $availableGoats + $soldGoats);
                        $availPercent = ($availableGoats / $totalStock) * 100;
                    @endphp
                    <div class="space-y-1">
                        <div class="h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden flex">
                            <div style="width: {{ $availPercent }}%" class="h-full bg-emerald-500"></div>
                            <div style="width: {{ 100 - $availPercent }}%" class="h-full bg-rose-500"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 block text-right">Rasio Ready vs Terjual: {{ round($availPercent) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Popular Breeds Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Statistik Breed / Ras</h3>
                
                <div class="space-y-3">
                    @foreach($popularBreeds as $breed)
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $breed->breed ?: 'Umum/Campuran' }}</span>
                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] font-extrabold">{{ $breed->count }} Hewan</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
