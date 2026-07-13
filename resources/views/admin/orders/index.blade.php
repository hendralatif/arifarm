@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Keuangan Peternakan</h1>
            <p class="text-sm text-slate-500 mt-1">Tinjau bukti transfer pembayaran, setujui pesanan masuk, dan pantau cashflow.</p>
        </div>
    </div>

    {{-- Unified Transaksi Tabs --}}
    <div class="border-b border-slate-200 dark:border-slate-800">
        <div class="flex gap-6 text-sm font-bold">
            <a href="{{ route('admin.orders.index') }}"
               class="pb-4 border-b-2 border-[#09422a] dark:border-emerald-400 text-[#09422a] dark:text-emerald-400">
                Pemasukan (Pesanan Pelanggan)
            </a>
            <a href="{{ route('admin.expenses.index') }}"
               class="pb-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition">
                Pengeluaran (Operasional Kandang)
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Tabs / Buttons -->
        <div class="flex flex-wrap gap-1.5 w-full md:w-auto">
            <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ !request('status') ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                Semua
            </a>
            <a href="{{ route('admin.orders.index') }}?status=pending_verification" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition relative {{ request('status') == 'pending_verification' ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                Verifikasi Pembayaran
                @php
                    $pendingCount = \App\Models\Order::where('status', 'pending_verification')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[8px] font-bold text-slate-950 ring-2 ring-white">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.orders.index') }}?status=processing" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') == 'processing' ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                Diproses
            </a>
            <a href="{{ route('admin.orders.index') }}?status=shipped" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') == 'shipped' ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                Dikirim
            </a>
            <a href="{{ route('admin.orders.index') }}?status=completed" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') == 'completed' ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                Selesai
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.orders.index') }}" method="GET" class="relative w-full md:max-w-xs">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice atau nama..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
            <i class="fa-solid fa-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
        </form>
    </div>

    <!-- Orders Spreadsheet Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 pb-3">
                        <th class="pb-3">Invoice</th>
                        <th class="pb-3">Pelanggan</th>
                        <th class="pb-3">Tanggal Order</th>
                        <th class="pb-3 text-right">Total Transaksi</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3">Resi Pengiriman</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($orders as $order)
                        <tr class="align-middle">
                            <!-- Invoice -->
                            <td class="py-4 font-bold text-slate-900 dark:text-white">
                                {{ $order->invoice_number }}
                            </td>

                            <!-- Customer details -->
                            <td class="py-4">
                                <div class="font-bold">{{ $order->user->name }}</div>
                                <span class="text-xs text-slate-400 block">{{ $order->phone_number }}</span>
                            </td>

                            <!-- Order Date -->
                            <td class="py-4 text-xs text-slate-500">
                                {{ $order->created_at->format('d M Y, H:i') }} WIB
                            </td>

                            <!-- Total -->
                            <td class="py-4 text-right font-black text-slate-900 dark:text-white">
                                {{ $order->formatted_total }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>

                            <!-- Tracking Code -->
                            <td class="py-4 text-xs select-all font-mono">
                                {{ $order->tracking_number ?: '-' }}
                            </td>

                            <!-- Actions -->
                            <td class="py-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex px-3.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 hover:bg-emerald-100 transition font-bold text-xs">
                                    Tinjau <i class="fa-solid fa-clipboard-check ml-1.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-slate-400">
                                Tidak ada transaksi pesanan yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 border-t border-slate-100 dark:border-slate-800 pt-6">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
