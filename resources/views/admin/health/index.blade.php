@extends('layouts.admin')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Catatan Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-1">Rekam pemeriksaan, vaksinasi, dan pengobatan kambing.</p>
        </div>
        <a href="{{ route('admin.health.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-900/20 transition">
            <i class="fa-solid fa-plus"></i> Tambah Catatan Kesehatan
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/20 dark:text-rose-400 text-2xl"><i class="fa-solid fa-stethoscope"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Sakit / Kritis</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $sickCount }} Kambing</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/20 dark:text-blue-400 text-2xl"><i class="fa-solid fa-syringe"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Vaksinasi Bulan Ini</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $vaccinatedThisMonth }} Kali</span>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-2xl"><i class="fa-solid fa-calendar-days"></i></div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Jadwal Cek (7 Hari)</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $upcomingCheckups }} Kambing</span>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center gap-3 flex-wrap">
        <div class="flex gap-2 flex-wrap">
            @foreach(['' => 'Semua Status', 'healthy' => 'Sehat', 'sick' => 'Sakit', 'recovering' => 'Pemulihan', 'critical' => 'Kritis'] as $val => $label)
                <a href="{{ route('admin.health.index', array_merge(request()->except('status','page'), $val ? ['status'=>$val] : [])) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('status') == $val ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <form action="{{ route('admin.health.index') }}" method="GET" class="relative w-full md:max-w-xs ml-auto">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kambing..."
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
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Kambing</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Jenis Catatan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Diagnosis / Keterangan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Cek Berikutnya</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($records as $rec)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors align-middle">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $rec->goat->name ?? '—' }}</div>
                            <div class="text-xs text-slate-400">{{ $rec->goat->breed ?? '' }} · {{ $rec->goat->gender === 'male' ? '♂ Jantan' : '♀ Betina' }}</div>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $rec->check_date->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $typeBadge = ['checkup'=>'bg-blue-50 text-blue-700 border-blue-200','vaccination'=>'bg-emerald-50 text-emerald-700 border-emerald-200','treatment'=>'bg-amber-50 text-amber-700 border-amber-200','observation'=>'bg-slate-50 text-slate-600 border-slate-200'];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $typeBadge[$rec->record_type] ?? '' }}">
                                {{ $rec->record_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400 max-w-[200px]">{{ $rec->diagnosis ?: ($rec->notes ?: '—') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rec->health_status_badge }}">
                                {{ $rec->health_status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs {{ $rec->next_checkup && $rec->next_checkup->lte(today()->addDays(3)) ? 'text-amber-600 font-bold' : 'text-slate-400' }}">
                            {{ $rec->next_checkup ? $rec->next_checkup->format('d M Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.health.show', $rec->id) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs transition">
                                    <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                </a>
                                <button onclick="confirmDelete({{ $rec->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition">
                                    <i class="fa-solid fa-trash text-[10px]"></i> Hapus
                                </button>
                                <form id="del-{{ $rec->id }}" action="{{ route('admin.health.destroy', $rec->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-14 text-slate-400">
                        <i class="fa-solid fa-stethoscope text-4xl block mb-3 opacity-30"></i>
                        <p class="font-semibold">Belum ada catatan kesehatan.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
        <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">{{ $records->links() }}</div>
        @endif
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Hapus catatan kesehatan ini?')) document.getElementById('del-' + id).submit();
}
</script>
@endsection
