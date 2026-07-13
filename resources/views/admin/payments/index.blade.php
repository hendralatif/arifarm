@extends('layouts.admin')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Pembayaran</h1>
            <p class="text-sm text-slate-500 mt-1">Verifikasi bukti pembayaran, lacak status transaksi, dan pantau pemasukan.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Pemasukan</p>
                    <p class="text-lg font-extrabold text-emerald-700 dark:text-emerald-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Bulan Ini</p>
                    <p class="text-lg font-extrabold text-indigo-700 dark:text-indigo-400">Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menunggu Verifikasi</p>
                    <p class="text-lg font-extrabold text-amber-700 dark:text-amber-400">{{ $pendingVerification }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/30 flex items-center justify-center text-rose-600 dark:text-rose-400 text-xl">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Menunggu Pembayaran</p>
                    <p class="text-lg font-extrabold text-rose-700 dark:text-rose-400">{{ $pendingPayment }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari invoice atau nama pelanggan..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                </div>
            </div>
            <select name="status" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 outline-none transition dark:text-white">
                <option value="">Semua Status</option>
                <option value="pending_payment" {{ request('status') == 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
            <div class="flex gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari"
                       class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 outline-none transition dark:text-white">
                <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai"
                       class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 outline-none transition dark:text-white">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
        </form>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950">
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Bukti</th>
                        <th class="px-6 py-4 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($payments as $order)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm font-extrabold text-[#09422a] dark:text-emerald-400 hover:underline">
                                {{ $order->invoice_number }}
                            </a>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $order->user->name ?? '-' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $order->user->email ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ $order->formatted_total }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $order->payment_method == 'transfer' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                <i class="fa-solid {{ $order->payment_method == 'transfer' ? 'fa-building-columns' : 'fa-money-bill' }} text-[8px]"></i>
                                {{ ucfirst($order->payment_method ?? 'Transfer') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold border {{ $order->status_badge_class }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($order->payment_receipt)
                                <button onclick="openReceiptModal('{{ asset($order->payment_receipt) }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-[10px] font-bold transition border border-blue-200">
                                    <i class="fa-solid fa-image text-[9px]"></i> Lihat Bukti
                                </button>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Belum upload</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($order->status === 'pending_verification')
                                <div class="flex items-center justify-center gap-1.5">
                                    <form method="POST" action="{{ route('admin.payments.verify', $order->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition text-xs font-bold" title="Setujui Pembayaran">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.verify', $order->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" onclick="return confirm('Tolak pembayaran ini? Pelanggan harus upload ulang bukti.')" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition text-xs font-bold" title="Tolak Pembayaran">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            @elseif($order->status === 'pending_payment')
                                <span class="text-[10px] text-amber-600 font-bold italic">Menunggu Upload</span>
                            @else
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold transition">
                                    <i class="fa-solid fa-eye text-[9px]"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-14 text-slate-400">
                            <i class="fa-solid fa-credit-card text-4xl block mb-3 opacity-30"></i>
                            <p class="font-semibold">Belum ada data pembayaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">{{ $payments->links() }}</div>
        @endif
    </div>
</div>

{{-- Receipt Preview Modal --}}
<div id="receipt-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeReceiptModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-6 w-full max-w-lg mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white"><i class="fa-solid fa-receipt mr-2 text-indigo-500"></i>Bukti Pembayaran</h3>
            <button onclick="closeReceiptModal()" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950">
            <img id="receipt-image" src="" alt="Bukti Pembayaran" class="w-full h-auto max-h-[500px] object-contain">
        </div>
    </div>
</div>

<script>
function openReceiptModal(imageUrl) {
    document.getElementById('receipt-image').src = imageUrl;
    document.getElementById('receipt-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeReceiptModal() {
    document.getElementById('receipt-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeReceiptModal();
});
</script>
@endsection
