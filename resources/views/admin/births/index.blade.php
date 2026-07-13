@extends('layouts.admin')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Data Kelahiran</h1>
            <p class="text-sm text-slate-500 mt-1">Catat dan pantau data kelahiran anak kambing di peternakan.</p>
        </div>
        <a href="{{ route('admin.births.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-900/20 transition">
            <i class="fa-solid fa-plus"></i> Tambah Data Kelahiran
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-pink-50 text-pink-600 dark:bg-pink-950/20 dark:text-pink-400 text-2xl"><i class="fa-solid fa-heart"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Total Kelahiran</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalBirths }} Kejadian</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 text-2xl"><i class="fa-solid fa-baby"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Total Anak Lahir</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalKids }} Ekor</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/20 dark:text-indigo-400 text-2xl"><i class="fa-solid fa-calendar-check"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Bulan Ini</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $thisMonthBirths }} Kelahiran</span>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex items-center gap-3">
        <form action="{{ route('admin.births.index') }}" method="GET" class="relative w-full md:max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama induk..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
            <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Lahir</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Induk (Betina)</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Pejantan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Anak Lahir</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">♂ / ♀ / ✝</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Proses</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($births as $birth)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="px-6 py-4 text-xs">
                            <div class="font-bold text-slate-800 dark:text-white">{{ $birth->birth_date->format('d M Y') }}</div>
                            <div class="text-slate-400">{{ $birth->birth_date->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $birth->mother->name ?? '—' }}</div>
                            <div class="text-xs text-slate-400">{{ $birth->mother->breed ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $birth->father->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 font-black text-base">
                                {{ $birth->total_kids }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-xs font-bold">
                            <span class="text-blue-600">{{ $birth->male_count }}♂</span> /
                            <span class="text-pink-500">{{ $birth->female_count }}♀</span>
                            @if($birth->stillborn_count > 0)
                                / <span class="text-slate-400">{{ $birth->stillborn_count }}✝</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $condBadge = ['normal'=>'bg-emerald-50 text-emerald-700 border-emerald-200','assisted'=>'bg-amber-50 text-amber-700 border-amber-200','cesarean'=>'bg-rose-50 text-rose-700 border-rose-200'];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $condBadge[$birth->birth_condition] ?? '' }}">
                                {{ $birth->birth_condition_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="confirmDelete({{ $birth->id }})"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition">
                                <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                            </button>
                            <form id="del-{{ $birth->id }}" action="{{ route('admin.births.destroy', $birth->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-14 text-slate-400">
                        <i class="fa-solid fa-heart text-4xl block mb-3 opacity-30"></i>
                        <p class="font-semibold">Belum ada data kelahiran dicatat.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($births->hasPages())
        <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">{{ $births->links() }}</div>
        @endif
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Hapus data kelahiran ini?')) document.getElementById('del-' + id).submit();
}
</script>
@endsection
