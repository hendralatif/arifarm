@extends('layouts.admin')
@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Manajemen Pakan</h1>
            <p class="text-sm text-slate-500 mt-1">Catat pemberian pakan harian, kelola stok gudang, dan atur jadwal pakan mingguan.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openRefillModal()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-650 hover:bg-indigo-750 text-white text-sm font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-parachute-box"></i> Tambah Stok Pakan
            </button>
            <a href="{{ route('admin.feedings.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-900/20 transition">
                <i class="fa-solid fa-plus"></i> Beri Pakan
            </a>
        </div>
    </div>

    {{-- Mineral Blok Bi-Weekly Reminder --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
        @if($isMineralBlokDue)
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900/60 p-5 rounded-2xl">
                <div class="flex gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center text-rose-650 dark:text-rose-400 text-lg shrink-0 mt-0.5 sm:mt-0">
                        <i class="fa-solid fa-circle-exclamation animate-pulse"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-rose-800 dark:text-rose-450 text-sm">Pengingat Mineral Blok! (Setiap 2 Minggu)</h4>
                        <p class="text-xs text-rose-700 dark:text-rose-400 mt-1">
                            Pemberian Mineral Blok terakhir dicatat: 
                            <strong class="font-bold">
                                {{ $lastMineralBlok ? $lastMineralBlok->feeding_date->format('d M Y') . ' (' . $mineralBlokDaysAgo . ' hari yang lalu)' : 'Belum pernah diberikan' }}
                            </strong>.
                            Kambing membutuhkan suplemen mineral ini secara berkala setiap 2 minggu sekali.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.feedings.create', ['mineral_blok' => 1]) }}"
                   class="inline-flex items-center justify-center px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-sm transition whitespace-nowrap">
                    Beri Mineral Blok Sekarang
                </a>
            </div>
        @else
            <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900/60 p-4 rounded-2xl">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-sm shrink-0">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-emerald-800 dark:text-emerald-450 text-xs">Status Pakan Mineral Blok Terpenuhi</h4>
                    <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-0.5">
                        Mineral Blok telah diberikan <strong class="font-bold">{{ $mineralBlokDaysAgo }} hari yang lalu</strong> ({{ $lastMineralBlok->feeding_date->format('d M Y') }}). Pemberian berikutnya dijadwalkan pada <strong class="font-bold">{{ $nextMineralBlokDate->format('d M Y') }}</strong>.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- Stock Available Summary Cards --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mb-4 flex items-center gap-2">
            <i class="fa-solid fa-boxes-stacked text-[#09422a] dark:text-emerald-400"></i>
            Stok Pakan Tersedia
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            @foreach($feedStocks as $stock)
            <div class="p-5 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 flex flex-col justify-between gap-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $stock->name }}</span>
                    @php
                        $iconClass = match($stock->name) {
                            'Konsentrat' => 'fa-seedling text-amber-500',
                            'Rumput Pakchong' => 'fa-leaf text-emerald-500',
                            'Mineral Blok' => 'fa-cubes text-blue-500',
                            default => 'fa-box text-slate-500'
                        };
                    @endphp
                    <i class="fa-solid {{ $iconClass }} text-lg"></i>
                </div>
                <div>
                    <span class="text-3xl font-black text-slate-900 dark:text-white">
                        {{ number_format($stock->stock_kg, 1, ',', '.') }}
                    </span>
                    <span class="text-xs text-slate-400 font-bold">kg</span>
                </div>
                <p class="text-xs text-slate-500 line-clamp-1 italic">{{ $stock->description }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="flex border-b border-slate-200 dark:border-slate-800">
        <button onclick="switchTab('log')" id="tab-btn-log" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all duration-150">
            <i class="fa-solid fa-clock-rotate-left mr-2"></i> Log Pemberian Pakan
        </button>
        <button onclick="switchTab('schedule')" id="tab-btn-schedule" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all duration-150">
            <i class="fa-solid fa-calendar-days mr-2"></i> Jadwal Pakan Mingguan
        </button>
    </div>

    {{-- Panel 1: Log Pemberian Pakan --}}
    <div id="tab-panel-log" class="space-y-6">
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
                <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 text-2xl">
                    <i class="fa-solid fa-wheat-awn"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-semibold uppercase block">Total Pakan Terpakai</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($totalKg, 1) }} kg</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
                <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-2xl">
                    <i class="fa-solid fa-sun"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-semibold uppercase block">Diberikan Hari Ini</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($todayKg, 1) }} kg</span>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
                <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/20 dark:text-indigo-400 text-2xl">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-400 font-semibold uppercase block">Pemberian Bulan Ini</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $monthCount }} Kali</span>
                </div>
            </div>
        </div>

        {{-- Filter + Search --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center gap-3">
            <div class="flex gap-2 flex-wrap">
                @foreach([''=>'Semua Sesi', 'pagi'=>'Pagi Only', 'sore'=>'Sore Only'] as $val => $label)
                    <a href="{{ route('admin.feedings.index', array_merge(request()->except('session','page'), $val ? ['session'=>$val] : [])) }}"
                       class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ request('session') == $val ? 'bg-slate-900 text-white dark:bg-emerald-600' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <form action="{{ route('admin.feedings.index') }}" method="GET" class="flex-1 relative w-full md:max-w-xs ml-auto">
                @if(request('session'))<input type="hidden" name="session" value="{{ request('session') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jenis pakan..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            </form>
        </div>

        {{-- Table of Log --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800">
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Sesi</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Pemberian Pakan</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Total Kg</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Jml Kambing</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Notes / Petugas</th>
                            <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($feedings as $item)
                        @php $isAuto = $item->recorded_by === null && str_contains($item->notes ?? '', '[AUTO]'); @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors align-middle {{ $isAuto ? 'bg-emerald-50/30 dark:bg-emerald-950/10' : '' }}">
                            <td class="px-6 py-4 text-xs text-slate-500">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $item->feeding_date->format('d M Y') }}</div>
                                @if($item->feeding_time)<div class="text-slate-400">{{ $item->feeding_time }}</div>@endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $sessionBadge = ['pagi'=>'bg-amber-50 text-amber-700 border-amber-200','sore'=>'bg-indigo-50 text-indigo-700 border-indigo-200'];
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $sessionBadge[$item->session] ?? 'bg-slate-50 border-slate-200' }}">
                                        {{ $item->session_label }}
                                    </span>
                                    @if($isAuto)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <i class="fa-solid fa-robot text-[8px]"></i> AUTO
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-bold text-slate-800 dark:text-white flex flex-col gap-1">
                                    @if($item->feed_type_1)
                                        <span class="inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i> {{ $item->feed_type_1 }} ({{ number_format($item->quantity_1_kg, 1) }} kg)</span>
                                    @endif
                                    @if($item->feed_type_2)
                                        <span class="inline-flex items-center gap-1"><i class="fa-solid fa-circle-check text-indigo-500 text-[9px]"></i> {{ $item->feed_type_2 }} ({{ number_format($item->quantity_2_kg, 1) }} kg)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-emerald-700 dark:text-emerald-400">
                                {{ number_format($item->quantity_1_kg + $item->quantity_2_kg, 1) }} kg
                            </td>
                            <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400 font-semibold">{{ $item->goat_count }} ekor</td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                @if($item->notes)
                                    @php $cleanNote = str_replace('[AUTO] ', '', $item->notes); @endphp
                                    <div class="font-medium text-slate-700 dark:text-slate-300 italic mb-0.5">"{{ $cleanNote }}"</div>
                                @endif
                                @if($isAuto)
                                    <div class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-robot"></i> Sistem Otomatis (Jadwal Permanen)
                                    </div>
                                @else
                                    <div class="text-[10px] text-slate-400">Dicatat oleh: {{ $item->recorder?->name ?? '-' }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="confirmDelete({{ $item->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-650 hover:bg-rose-100 font-bold text-xs transition">
                                    <i class="fa-solid fa-trash-can text-[10px]"></i> Hapus
                                </button>
                                <form id="del-{{ $item->id }}" action="{{ route('admin.feedings.destroy', $item->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-14 text-slate-400">
                            <i class="fa-solid fa-wheat-awn text-4xl block mb-3 opacity-30"></i>
                            <p class="font-semibold">Belum ada catatan pakan.</p>
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($feedings->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">{{ $feedings->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Panel 2: Jadwal Pakan Mingguan --}}
    <div id="tab-panel-schedule" class="space-y-6 hidden">
        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-3xl p-5 flex items-start gap-4">
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/40 text-[#09422a] dark:text-emerald-400 rounded-2xl text-lg shrink-0">
                <i class="fa-solid fa-infinity"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="font-extrabold text-[#09422a] dark:text-emerald-400 text-sm">Jadwal Pakan Permanen</h4>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white">
                        <i class="fa-solid fa-circle text-[6px]"></i> Aktif Selamanya
                    </span>
                </div>
                <p class="text-xs text-emerald-800 dark:text-emerald-400/80 leading-relaxed">
                    Jadwal ini berlaku <strong>permanen setiap minggunya</strong> dan akan terus berulang secara otomatis sampai admin mengubahnya. Tidak ada batas waktu — jadwal yang sudah diatur akan terus menjadi acuan rutin anak kandang setiap harinya. Untuk mengubah jadwal, klik tombol <strong>Edit</strong> pada slot yang ingin diperbarui.
                </p>
            </div>
        </div>

        {{-- Quick Copy Action Bar --}}
        @php
            $daysWithSchedule = $schedules->pluck('day_of_week')->unique()->values()->toArray();
        @endphp
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl px-5 py-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="flex items-center gap-3 flex-1">
                <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 text-indigo-600 dark:text-indigo-400 text-base shrink-0">
                    <i class="fa-solid fa-copy"></i>
                </div>
                <div>
                    <p class="text-sm font-extrabold text-slate-800 dark:text-white">Salin Jadwal ke Semua Hari</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih hari sumber, jadwal pagi & sorenya akan disalin ke semua hari lainnya dalam seminggu.</p>
                </div>
            </div>
            <form id="copy-all-form" method="POST" action="{{ route('admin.feedings.schedules.copy-all') }}" class="flex items-center gap-2 shrink-0">
                @csrf
                <select name="source_day" id="copy-source-day" required
                    class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm font-bold text-slate-700 dark:text-white focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none transition">
                    <option value="">-- Pilih Hari Sumber --</option>
                    @foreach(['senin'=>'Senin','selasa'=>'Selasa','rabu'=>'Rabu','kamis'=>'Kamis','jumat'=>'Jumat','sabtu'=>'Sabtu','minggu'=>'Minggu'] as $code => $label)
                        <option value="{{ $code }}"
                            {{ $code === 'senin' ? 'selected' : '' }}
                            {{ !in_array($code, $daysWithSchedule) ? 'disabled' : '' }}>
                            {{ $label }}{{ !in_array($code, $daysWithSchedule) ? ' (belum ada jadwal)' : '' }}
                        </option>
                    @endforeach
                </select>
                <button type="button" onclick="confirmCopyAll()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl transition shadow-sm">
                    <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                    Terapkan ke Semua Hari
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $daysMap = [
                    'senin' => 'Senin',
                    'selasa' => 'Selasa',
                    'rabu' => 'Rabu',
                    'kamis' => 'Kamis',
                    'jumat' => 'Jumat',
                    'sabtu' => 'Sabtu',
                    'minggu' => 'Minggu',
                ];
            @endphp
            @foreach($daysMap as $dayCode => $dayLabel)
                <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3">
                            <h4 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                {{ $dayLabel }}
                            </h4>
                            @php $dayHasSchedule = $schedules->contains(fn($s) => $s->day_of_week == $dayCode); @endphp
                            @if($dayHasSchedule)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fa-solid fa-infinity text-[8px]"></i> Aktif Permanen
                                </span>
                            @endif
                        </div>
                        <div class="space-y-4">
                            @foreach(['pagi', 'sore'] as $sess)
                                @php
                                    $sch = $schedules->first(fn($s) => $s->day_of_week == $dayCode && $s->session == $sess);
                                @endphp
                                <div class="p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/60 flex flex-col justify-between gap-3 min-h-[110px]">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $sess == 'pagi' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200' }}">
                                                {{ ucfirst($sess) }}
                                            </span>
                                        </div>
                                        @if($sch)
                                            <div class="text-xs space-y-1.5 mt-2 text-slate-700 dark:text-slate-300">
                                                @if($sch->feedStock1)
                                                    @php $est1 = $sch->getEstimatedKg('1', $totalGoats); @endphp
                                                    <div class="font-bold flex items-center gap-1.5">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                        <span class="flex-1">{{ $sch->feedStock1->name }}</span>
                                                        <span class="text-emerald-700 dark:text-emerald-400 font-extrabold">
                                                            @if($sch->qty_type_1 === 'per_goat')
                                                                {{ number_format($sch->quantity_1_kg, 2) }} kg/ekor
                                                                <span class="text-[9px] text-slate-400 font-normal">(≈{{ number_format($est1, 1) }} kg total)</span>
                                                            @else
                                                                {{ number_format($sch->quantity_1_kg, 1) }} kg
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if($sch->qty_type_1 === 'per_goat')
                                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <i class="fa-solid fa-calculator text-[8px]"></i> Per Ekor × {{ $totalGoats }} ekor
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                                                            <i class="fa-solid fa-lock text-[8px]"></i> Tetap
                                                        </div>
                                                    @endif
                                                @endif
                                                @if($sch->feedStock2)
                                                    @php $est2 = $sch->getEstimatedKg('2', $totalGoats); @endphp
                                                    <div class="font-bold flex items-center gap-1.5 mt-1">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                                        <span class="flex-1">{{ $sch->feedStock2->name }}</span>
                                                        <span class="text-indigo-700 dark:text-indigo-400 font-extrabold">
                                                            @if($sch->qty_type_2 === 'per_goat')
                                                                {{ number_format($sch->quantity_2_kg, 2) }} kg/ekor
                                                                <span class="text-[9px] text-slate-400 font-normal">(≈{{ number_format($est2, 1) }} kg total)</span>
                                                            @else
                                                                {{ number_format($sch->quantity_2_kg, 1) }} kg
                                                            @endif
                                                        </span>
                                                    </div>
                                                    @if($sch->qty_type_2 === 'per_goat')
                                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <i class="fa-solid fa-calculator text-[8px]"></i> Per Ekor × {{ $totalGoats }} ekor
                                                        </div>
                                                    @else
                                                        <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-50 text-slate-500 border border-slate-200">
                                                            <i class="fa-solid fa-lock text-[8px]"></i> Tetap
                                                        </div>
                                                    @endif
                                                @endif
                                                @if($sch->notes)
                                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 italic mt-1 font-medium line-clamp-2">"{{ $sch->notes }}"</p>
                                                @endif
                                            </div>
                                        @else
                                            <p class="text-xs text-slate-400 italic font-medium mt-2">Belum diatur</p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 self-end border-t border-slate-100/50 dark:border-slate-800/40 pt-2 w-full justify-end">
                                        @if($sch)
                                            <a href="{{ route('admin.feedings.create', ['session' => $sess]) }}"
                                               class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/30 text-[#09422a] dark:text-emerald-400 transition text-xs font-bold" title="Catat Pemberian Pakan Sesi Ini">
                                                <i class="fa-solid fa-bowl-food"></i>
                                            </a>
                                            <button onclick="openScheduleModal('{{ $dayCode }}', '{{ $sess }}', '{{ $sch->feed_stock_1_id }}', '{{ $sch->quantity_1_kg }}', '{{ $sch->qty_type_1 ?? 'fixed' }}', '{{ $sch->feed_stock_2_id }}', '{{ $sch->quantity_2_kg }}', '{{ $sch->qty_type_2 ?? 'fixed' }}', '{{ addslashes($sch->notes) }}')"
                                                    class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-650 dark:text-slate-350 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold" title="Edit Jadwal">
                                                <i class="fa-solid fa-edit"></i>
                                            </button>
                                            <button onclick="confirmDeleteSchedule({{ $sch->id }})"
                                                    class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-650 transition text-xs font-bold" title="Hapus Jadwal">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <form id="del-sch-{{ $sch->id }}" action="{{ route('admin.feedings.schedules.destroy', $sch->id) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                                        @else
                                            <button onclick="openScheduleModal('{{ $dayCode }}', '{{ $sess }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 text-[#09422a] dark:text-emerald-400 text-xs font-extrabold transition">
                                                <i class="fa-solid fa-plus text-[9px]"></i> Atur Jadwal
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Refill Stock Modal --}}
<div id="refill-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeRefillModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-650 dark:text-indigo-400 text-2xl">
                <i class="fa-solid fa-parachute-box"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Tambah Stok Pakan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Isi ulang persediaan pakan kandang.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.feedings.add-stock') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Jenis Pakan <span class="text-rose-500">*</span></label>
                <select name="feed_stock_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    @foreach($feedStocks as $stock)
                        <option value="{{ $stock->id }}">{{ $stock->name }} (Stok: {{ number_format($stock->stock_kg, 1) }} kg)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jumlah Ditambahkan (Kg) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="added_kg" required min="0.1" step="0.1" placeholder="0.0"
                               class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">kg</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Biaya Pembelian <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <input type="number" name="cost" required min="0" step="100" placeholder="0"
                               class="w-full pl-8 pr-3 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Supplier (Opsional)</label>
                <input type="text" name="supplier" placeholder="Contoh: Toko Pakan Pak Darto"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
            </div>

            <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/60 rounded-xl p-3 flex gap-2 text-xs text-amber-800 dark:text-amber-400">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <span>Biaya pembelian akan otomatis tercatat sebagai <strong>Pengeluaran Pakan</strong> di menu Transaksi. Isi <strong>0</strong> jika pakan diperoleh secara gratis atau hibah.</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeRefillModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-750 text-white text-sm font-bold shadow-md transition">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan & Catat Biaya
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Weekly Schedule Modal --}}
<div id="schedule-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeScheduleModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-lg mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 dark:text-emerald-450 text-2xl">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Atur Jadwal Pakan Mingguan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Tentukan rincian jenis dan takaran pakan per sesi.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.feedings.schedules.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-550 uppercase tracking-wider mb-2">Hari <span class="text-rose-500">*</span></label>
                    <select id="sch_day" name="day_of_week" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        @foreach($daysMap as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-550 uppercase tracking-wider mb-2">Sesi <span class="text-rose-500">*</span></label>
                    <select id="sch_session" name="session" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        <option value="pagi">Pagi</option>
                        <option value="sore">Sore</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 dark:border-slate-800 pt-3">
                {{-- Feed 1 --}}
                <div class="p-3.5 rounded-2xl border border-slate-150 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 space-y-3">
                    <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase block tracking-wider">Pakan 1</span>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Jenis Pakan <span class="text-rose-500">*</span></label>
                        <select id="sch_feed_1" name="feed_stock_1_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($feedStocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tipe Takaran</label>
                            <select id="sch_qty_type_1" name="qty_type_1" class="w-full px-2 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                                <option value="fixed">Tetap (kg)</option>
                                <option value="per_goat">Per Ekor (kg/ekor)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Takaran <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input id="sch_qty_1" type="number" name="quantity_1_kg" min="0" step="0.01" placeholder="0.0"
                                       class="w-full pl-2 pr-7 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-400">kg</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Feed 2 --}}
                <div class="p-3.5 rounded-2xl border border-slate-150 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 space-y-3">
                    <span class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase block tracking-wider">Pakan 2 (Opsional)</span>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Jenis Pakan</label>
                        <select id="sch_feed_2" name="feed_stock_2_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Jenis (Opsional) --</option>
                            @foreach($feedStocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tipe Takaran</label>
                            <select id="sch_qty_type_2" name="qty_type_2" class="w-full px-2 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                                <option value="fixed">Tetap (kg)</option>
                                <option value="per_goat">Per Ekor (kg/ekor)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Takaran</label>
                            <div class="relative">
                                <input id="sch_qty_2" type="number" name="quantity_2_kg" min="0" step="0.01" placeholder="0.0"
                                       class="w-full pl-2 pr-7 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-400">kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/60 rounded-xl p-3 flex gap-2 text-xs text-indigo-800 dark:text-indigo-400">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <span>Jadwal berlaku seterusnya setiap minggu hingga Anda mengubah atau menghapusnya. Mode <strong>"Per Ekor"</strong> menghitung total pakan berdasarkan jumlah kambing aktif saat ini ({{ $totalGoats }} ekor).</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Catatan Tambahan (Opsional)</label>
                <input id="sch_notes" type="text" name="notes" placeholder="Contoh: Tambah mineral blok jika hari ini adalah minggu genap..."
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
            </div>

            <div class="flex gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeScheduleModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md transition">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Hapus catatan pakan ini?')) document.getElementById('del-' + id).submit();
}
function confirmDeleteSchedule(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jadwal pakan mingguan ini?')) document.getElementById('del-sch-' + id).submit();
}
function openRefillModal() {
    document.getElementById('refill-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeRefillModal() {
    document.getElementById('refill-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
function openScheduleModal(day = '', session = '', feed1Id = '', qty1 = '', qtyType1 = 'fixed', feed2Id = '', qty2 = '', qtyType2 = 'fixed', notes = '') {
    document.getElementById('schedule-modal').classList.remove('hidden');
    document.getElementById('sch_day').value = day;
    document.getElementById('sch_session').value = session;
    document.getElementById('sch_feed_1').value = feed1Id;
    document.getElementById('sch_qty_1').value = qty1;
    document.getElementById('sch_qty_type_1').value = qtyType1;
    document.getElementById('sch_feed_2').value = feed2Id;
    document.getElementById('sch_qty_2').value = qty2;
    document.getElementById('sch_qty_type_2').value = qtyType2;
    document.getElementById('sch_notes').value = notes;
    document.body.style.overflow = 'hidden';
}
function closeScheduleModal() {
    document.getElementById('schedule-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
function switchTab(tabName) {
    // Hide all tab panels
    document.getElementById('tab-panel-log').classList.add('hidden');
    document.getElementById('tab-panel-schedule').classList.add('hidden');
    
    // Deactivate all tab buttons
    document.getElementById('tab-btn-log').className = 'px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all duration-150';
    document.getElementById('tab-btn-schedule').className = 'px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all duration-150';
    
    // Show active panel and button
    document.getElementById('tab-panel-' + tabName).classList.remove('hidden');
    document.getElementById('tab-btn-' + tabName).className = 'px-6 py-3 text-sm font-bold border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-450 transition-all duration-150';
    
    // Update URL query parameter
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// On page load
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const activeTab = params.get('tab') === 'schedule' ? 'schedule' : 'log';
    switchTab(activeTab);
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRefillModal();
        closeScheduleModal();
        closeCopyConfirmModal();
    }
});

function confirmCopyAll() {
    const select = document.getElementById('copy-source-day');
    const selectedOption = select.options[select.selectedIndex];
    if (!select.value) {
        alert('Pilih hari sumber terlebih dahulu.');
        return;
    }
    const dayLabel = selectedOption.text.replace(' (belum ada jadwal)', '');
    document.getElementById('copy-confirm-day-label').textContent = dayLabel;
    document.getElementById('copy-confirm-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCopyConfirmModal() {
    document.getElementById('copy-confirm-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
function submitCopyAll() {
    document.getElementById('copy-all-form').submit();
}
</script>

{{-- Copy To All Days Confirmation Modal --}}
<div id="copy-confirm-modal" class="fixed inset-0 z-[200] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCopyConfirmModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-sm mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-500 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Konfirmasi Salin Jadwal</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tindakan ini akan menimpa jadwal yang sudah ada.</p>
            </div>
        </div>

        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 mb-5 text-xs text-amber-800 dark:text-amber-300 space-y-1.5">
            <p class="font-extrabold text-sm">Yang akan terjadi:</p>
            <ul class="space-y-1 pl-1">
                <li class="flex items-start gap-1.5"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i> Semua jadwal (Pagi &amp; Sore) dari hari <strong id="copy-confirm-day-label">—</strong> akan disalin</li>
                <li class="flex items-start gap-1.5"><i class="fa-solid fa-rotate text-indigo-500 mt-0.5 shrink-0"></i> Disalin ke <strong>Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu</strong></li>
                <li class="flex items-start gap-1.5"><i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i> Jadwal yang sudah ada di hari lain akan <strong>ditimpa (overwrite)</strong></li>
            </ul>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="closeCopyConfirmModal()"
                class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                Batal
            </button>
            <button type="button" onclick="submitCopyAll()"
                class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Ya, Salin Sekarang
            </button>
        </div>
    </div>
</div>

@endsection
