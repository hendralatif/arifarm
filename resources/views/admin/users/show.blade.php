@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
               class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Detail Pelanggan</h1>
                <p class="text-sm text-slate-500 mt-0.5">Profil & riwayat transaksi pelanggan.</p>
            </div>
        </div>

        {{-- Delete Button --}}
        <button type="button"
                onclick="document.getElementById('delete-modal').classList.remove('hidden'); document.body.style.overflow='hidden';"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md shadow-rose-900/20 transition-all duration-150">
            <i class="fa-solid fa-user-slash"></i>
            Hapus Pelanggan Ini
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                {{-- Avatar --}}
                <div class="flex flex-col items-center text-center pb-5 border-b border-slate-100 dark:border-slate-800 mb-5">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-black text-2xl shadow-lg mb-4">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-sm text-slate-500 mt-1">{{ $user->email }}</p>
                    <span class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold">
                        <i class="fa-solid fa-user text-[9px]"></i> Pelanggan
                    </span>
                </div>

                {{-- Stats --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-receipt w-5 text-indigo-500"></i> Total Pesanan</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{{ $totalOrders }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-wallet w-5 text-emerald-500"></i> Total Belanja</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">Rp {{ number_format($totalSpent, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-ban w-5 text-rose-500"></i> Dibatalkan</span>
                        <span class="font-extrabold text-slate-900 dark:text-white">{{ $cancelledOrders }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-semibold"><i class="fa-solid fa-calendar w-5 text-amber-500"></i> Bergabung</span>
                        <span class="font-bold text-slate-600 dark:text-slate-400 text-xs">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders History --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                <h3 class="font-extrabold text-slate-900 dark:text-white text-base">
                    <i class="fa-solid fa-clock-rotate-left mr-2 text-indigo-500"></i> Riwayat Pesanan
                </h3>
                <span class="text-xs text-slate-400 font-semibold">10 Pesanan Terbaru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-xs font-bold uppercase tracking-wider text-slate-400">
                            <th class="px-6 py-3">Invoice</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-center">Status</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($user->orders as $order)
                            <tr class="align-middle hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-3.5 font-bold text-slate-800 dark:text-white text-xs font-mono">
                                    {{ $order->invoice_number }}
                                </td>
                                <td class="px-6 py-3.5 text-right font-semibold text-sm">
                                    {{ $order->formatted_total }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-400">
                                    {{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 hover:bg-emerald-100 font-bold text-xs transition">
                                        <i class="fa-solid fa-eye text-[10px]"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400">
                                    <i class="fa-solid fa-box-open text-4xl block mb-3 opacity-40"></i>
                                    <p class="text-sm font-semibold">Pelanggan ini belum memiliki pesanan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         onclick="document.getElementById('delete-modal').classList.add('hidden'); document.body.style.overflow='';"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-950/30 flex items-center justify-center text-rose-600 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Hapus Pelanggan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-2xl px-5 py-4 mb-6">
            <p class="text-sm text-slate-700 dark:text-slate-300">
                Anda akan menghapus akun pelanggan:<br>
                <span class="font-extrabold text-rose-700 dark:text-rose-400 text-base">{{ $user->name }}</span>
            </p>
            <p class="text-xs text-slate-500 mt-2">
                <i class="fa-solid fa-circle-info mr-1 text-amber-500"></i>
                Semua pesanan aktif pelanggan ini akan otomatis dibatalkan dan stok kambing dikembalikan.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('delete-modal').classList.add('hidden'); document.body.style.overflow='';"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-md shadow-rose-900/20 transition">
                    <i class="fa-solid fa-trash mr-1.5"></i> Ya, Hapus
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('delete-modal').classList.add('hidden');
        document.body.style.overflow = '';
    }
});
</script>
@endsection
