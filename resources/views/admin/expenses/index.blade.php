@extends('layouts.admin')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Keuangan Peternakan</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau pemasukan dari pesanan dan catat semua pos pengeluaran operasional.</p>
        </div>
    </div>

    {{-- Unified Transaksi Tabs --}}
    <div class="border-b border-slate-200 dark:border-slate-800">
        <div class="flex gap-6 text-sm font-bold">
            <a href="{{ route('admin.orders.index') }}"
               class="pb-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition">
                Pemasukan (Pesanan Pelanggan)
            </a>
            <a href="{{ route('admin.expenses.index') }}"
               class="pb-4 border-b-2 border-[#09422a] dark:border-emerald-400 text-[#09422a] dark:text-emerald-400">
                Pengeluaran (Operasional Kandang)
            </a>
        </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400 text-2xl">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Total Pengeluaran</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-2xl">
                <i class="fa-solid fa-calendar-minus"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Pengeluaran Bulan Ini</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">Rp {{ number_format($thisMonthExpense, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-slate-50 text-slate-600 dark:bg-slate-950 dark:text-slate-400 text-2xl">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Pengeluaran Hari Ini</span>
                <span class="text-xl font-black text-slate-900 dark:text-white">Rp {{ number_format($todayExpense, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Main Content Grid: Left table list, Right create form --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Expenses List Table --}}
        <div class="lg:col-span-2 space-y-4">
            {{-- Filter + Search bar --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col sm:flex-row items-center gap-3">
                <div class="flex gap-2 flex-wrap">
                    @foreach([''=>'Semua', 'pakan'=>'Pakan', 'kesehatan'=>'Kesehatan', 'operasional'=>'Operasional', 'pembelian_hewan'=>'Beli Hewan', 'lainnya'=>'Lainnya'] as $v => $l)
                        <a href="{{ route('admin.expenses.index', array_merge(request()->except('category','page'), $v ? ['category'=>$v] : [])) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ request('category') == $v ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                            {{ $l }}
                        </a>
                    @endforeach
                </div>
                <form action="{{ route('admin.expenses.index') }}" method="GET" class="relative w-full sm:max-w-xs ml-auto">
                    @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pengeluaran..."
                           class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400">
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Pengeluaran</th>
                                <th class="px-6 py-4 text-right">Nominal</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($expenses as $exp)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors align-middle">
                                <td class="px-6 py-4 text-xs text-slate-500 font-semibold">
                                    {{ $exp->expense_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $exp->category_badge_class }}">
                                        {{ $exp->category_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $exp->title }}</div>
                                    @if($exp->description)
                                        <div class="text-xs text-slate-400 italic mt-0.5 line-clamp-1">{{ $exp->description }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-black text-rose-600 dark:text-rose-455">
                                    {{ $exp->formatted_amount }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="confirmDeleteExpense({{ $exp->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition">
                                        <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                    </button>
                                    <form id="del-expense-{{ $exp->id }}" action="{{ route('admin.expenses.destroy', $exp->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-14 text-slate-400">
                                    <i class="fa-solid fa-arrow-trend-down text-4xl block mb-3 opacity-30"></i>
                                    <p class="font-semibold">Belum ada catatan pengeluaran.</p>
                                </td>
                            </tr>
                            @endempty
                        </tbody>
                    </table>
                </div>
                @if($expenses->hasPages())
                <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">{{ $expenses->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Right: Add Expense Form --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm h-fit space-y-4">
            <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-[#09422a] dark:text-emerald-400"></i> Catat Pengeluaran Baru
            </h3>
            <form method="POST" action="{{ route('admin.expenses.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tanggal Pengeluaran <span class="text-rose-500">*</span></label>
                    <input type="date" name="expense_date" value="{{ old('expense_date', today()->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('expense_date') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('category') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="pakan" {{ old('category') == 'pakan' ? 'selected' : '' }}>Pakan</option>
                        <option value="kesehatan" {{ old('category') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                        <option value="operasional" {{ old('category') == 'operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="pembelian_hewan" {{ old('category') == 'pembelian_hewan' ? 'selected' : '' }}>Pembelian Hewan</option>
                        <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama / Kebutuhan Pengeluaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="cth: Gaji bulanan anak kandang, beli pakan..." required
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('title') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nominal (Rp) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" min="1000" placeholder="0" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border {{ $errors->has('amount') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi Detail</label>
                    <textarea name="description" rows="3" placeholder="Detail pengeluaran..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white resize-none">{{ old('description') }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-[#09422a] hover:bg-[#083a25] text-white font-bold text-sm rounded-xl shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Pengeluaran
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function confirmDeleteExpense(id) {
    if (confirm('Hapus catatan pengeluaran ini?')) {
        document.getElementById('del-expense-' + id).submit();
    }
}
</script>
@endsection
