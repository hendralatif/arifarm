@extends('layouts.public')

@section('title', 'Histori Pesanan - ARI FARM')

@section('content')
<div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-10 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white font-display">Histori Pesanan Anda</h1>
                <p class="text-sm text-slate-500 mt-2">Pantau seluruh riwayat transaksi pemesanan kambing & domba Anda di Ari Farm.</p>
            </div>
            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#09422a] hover:bg-[#083a25] text-white text-xs font-bold shadow-md transition self-center sm:self-auto">
                <i class="fa-solid fa-store text-[10px]"></i> Belanja Lagi
            </a>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 rounded-3xl p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $orders->count() }}</h3>
                    <span class="text-[9px] text-slate-400">Seluruh pesanan Anda</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-600 text-xl shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 rounded-3xl p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Belanja (Selesai)</p>
                    <h3 class="text-2xl font-black text-emerald-700 dark:text-emerald-450 mt-1">
                        Rp {{ number_format($orders->where('status', 'completed')->sum('total_amount'), 0, ',', '.') }}
                    </h3>
                    <span class="text-[9px] text-slate-400">Total nominal pesanan selesai</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 text-xl shrink-0">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700 rounded-3xl p-6 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Sedang Berjalan</p>
                    <h3 class="text-2xl font-black text-amber-700 dark:text-amber-450 mt-1">
                        {{ $orders->whereIn('status', ['pending_approval', 'pending_payment', 'pending_verification', 'processing', 'shipped'])->count() }}
                    </h3>
                    <span class="text-[9px] text-slate-400">Pesanan aktif saat ini</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 text-xl shrink-0">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="flex items-center gap-1.5 border-b border-slate-200 dark:border-slate-800 mb-6 pb-px flex-wrap">
            <a href="{{ route('histori') }}" class="px-5 py-3 border-b-2 font-bold text-xs transition {{ !$status ? 'border-[#09422a] text-[#09422a] dark:border-emerald-400 dark:text-emerald-450' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Semua
            </a>
            <a href="{{ route('histori', ['status' => 'active']) }}" class="px-5 py-3 border-b-2 font-bold text-xs transition {{ $status === 'active' ? 'border-[#09422a] text-[#09422a] dark:border-emerald-400 dark:text-emerald-450' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Sedang Berjalan
            </a>
            <a href="{{ route('histori', ['status' => 'completed']) }}" class="px-5 py-3 border-b-2 font-bold text-xs transition {{ $status === 'completed' ? 'border-[#09422a] text-[#09422a] dark:border-emerald-400 dark:text-emerald-450' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Selesai
            </a>
            <a href="{{ route('histori', ['status' => 'cancelled']) }}" class="px-5 py-3 border-b-2 font-bold text-xs transition {{ $status === 'cancelled' ? 'border-[#09422a] text-[#09422a] dark:border-emerald-400 dark:text-emerald-450' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                Dibatalkan
            </a>
        </div>

        {{-- History Table --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-750">
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider text-right">Total Transaksi</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-750">
                        @forelse($orders as $order)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-900/40 transition">
                            <td class="px-6 py-4 font-extrabold text-slate-900 dark:text-white text-sm">
                                {{ $order->invoice_number }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $order->created_at->format('d M Y, H:i') }} WIB
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-slate-900 dark:text-white text-right">
                                {{ $order->formatted_total }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold border {{ $order->status_badge_class }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('orders.show', $order->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm hover:shadow-md transition active:scale-95 cursor-pointer">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-14 text-slate-400">
                                <i class="fa-solid fa-receipt text-4xl block mb-3 opacity-30"></i>
                                <p class="font-semibold">Belum ada histori pesanan yang cocok.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
