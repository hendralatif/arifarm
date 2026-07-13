@extends('layouts.admin')
@section('content')
<style>
@media print {
    /* Hide sidebar, top header, filter widgets, and print button */
    aside, header, .no-print, .no-print-btn, form, button {
        display: none !important;
    }
    /* Adjust content area spacing for page margins */
    main {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    body {
        background: white !important;
        color: black !important;
    }
    /* Ensure clean 2-column or 3-column split layouts in print preview */
    .grid {
        display: grid !important;
        gap: 1rem !important;
    }
    .md\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    .lg\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
    }
    .lg\:grid-cols-4 {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }
    .lg\:col-span-2 {
        grid-column: span 2 / span 2 !important;
    }
    .shadow-sm, .shadow-md, .shadow-2xl {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
    .rounded-3xl {
        border-radius: 1rem !important;
    }
}
</style>

<div class="space-y-6">

    {{-- Header & Period Filter --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Laporan & Analisis</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau performa keuangan, populasi ternak, kesehatan, serta log aktivitas peternakan.</p>
        </div>
        
        <div class="flex items-center gap-3 no-print">
            {{-- Filter Form --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-2xl p-3 shadow-sm">
                <form id="filter-form" method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-center gap-3">
                    <select name="period" id="period-select" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs font-bold focus:border-emerald-500 outline-none transition dark:text-white">
                        <option value="this_month" {{ $periodType == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="last_month" {{ $periodType == 'last_month' ? 'selected' : '' }}>Bulan Lalu</option>
                        <option value="daily" {{ $periodType == 'daily' ? 'selected' : '' }}>Laporan Harian (Per Hari)</option>
                        <option value="weekly" {{ $periodType == 'weekly' ? 'selected' : '' }}>Laporan Mingguan (Per Minggu)</option>
                        <option value="monthly" {{ $periodType == 'monthly' ? 'selected' : '' }}>Laporan Bulanan (Per Bulan)</option>
                        <option value="yearly" {{ $periodType == 'yearly' ? 'selected' : '' }}>Laporan Tahunan (Per Tahun)</option>
                        <option value="transaction" {{ $periodType == 'transaction' ? 'selected' : '' }}>Laporan Per Transaksi</option>
                        <option value="this_year" {{ $periodType == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                        <option value="custom" {{ $periodType == 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                    </select>

                    {{-- Daily Input --}}
                    <div id="daily-input-wrapper" class="hidden items-center">
                        <input type="date" name="date" value="{{ request('date', today()->toDateString()) }}"
                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Weekly Input --}}
                    <div id="weekly-input-wrapper" class="hidden items-center">
                        <input type="week" name="week" value="{{ request('week', now()->format('Y-\WW')) }}"
                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Monthly Input --}}
                    <div id="monthly-input-wrapper" class="hidden items-center">
                        <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Yearly Input --}}
                    <div id="yearly-input-wrapper" class="hidden items-center">
                        <select name="year" class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div id="custom-date-inputs" class="hidden items-center gap-2">
                        <input type="date" name="date_from" value="{{ request('date_from', $dateFrom ? $dateFrom->format('Y-m-d') : '') }}"
                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                        <span class="text-slate-400 text-xs">s/d</span>
                        <input type="date" name="date_to" value="{{ request('date_to', $dateTo ? $dateTo->format('Y-m-d') : '') }}"
                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-xs focus:border-emerald-500 outline-none transition dark:text-white">
                    </div>
                    
                    <button type="submit" class="px-4 py-2 bg-[#09422a] hover:bg-[#083a25] text-white text-xs font-bold rounded-xl shadow-md transition">
                        <i class="fa-solid fa-sync mr-1 text-[10px]"></i> Terapkan
                    </button>
                </form>
            </div>

            @php
                $downloadUrl = route('admin.reports.download', request()->all());
            @endphp
            <a href="{{ $downloadUrl }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5" title="Unduh PDF laporan peternakan secara instan">
                <i class="fa-solid fa-file-pdf"></i> Unduh PDF Laporan
            </a>
        </div>
    </div>

    {{-- Info Banner Period --}}
    <div class="bg-indigo-50 dark:bg-indigo-950/20 border border-indigo-100 dark:border-indigo-900/60 p-4 rounded-2xl text-xs text-indigo-800 dark:text-indigo-400 flex items-center gap-2">
        <i class="fa-solid fa-calendar-alt"></i>
        <span>Menampilkan laporan dari tanggal <strong>{{ $dateFrom->format('d M Y') }}</strong> sampai <strong>{{ $dateTo->format('d M Y') }}</strong>.</span>
    </div>

    {{-- Master Spreadsheet Table --}}
    @php
        $totalKids = $birthStats['total_male'] + $birthStats['total_female'] + $birthStats['total_dead'];
        $deathRate = $totalKids > 0 ? ($birthStats['total_dead'] / $totalKids) * 100 : 0;
    @endphp
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden p-6 mt-6">
        <div class="mb-5">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center"><i class="fa-solid fa-file-excel mr-2 text-[#09422a] dark:text-emerald-450 text-lg"></i>Master Laporan Spreadsheet (Single Workbook)</h3>
            <p class="text-xs text-slate-500 mt-1">Seluruh data finansial, tren, alokasi anggaran, data operasional kandang, dan buku kas detail digabungkan ke dalam satu lembar kerja terintegrasi.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-slate-250 dark:border-slate-800 text-left text-xs">
                <thead>
                    <tr class="bg-slate-200 dark:bg-slate-950 font-black text-slate-800 dark:text-slate-200">
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-center w-12">No</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 w-32">Tanggal / Periode</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 w-36">Kategori / Tipe</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 w-40">Referensi / Pos</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 w-2/5">Keterangan / Rincian Laporan</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right text-emerald-700 dark:text-emerald-450 w-36">Debit (In)</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right text-rose-600 w-36">Kredit (Out)</th>
                        <th class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right w-36">Saldo / Status</th>
                    </tr>
                </thead>
                <tbody>
                    
                    {{-- ================= SECTION I ================= --}}
                    <tr>
                        <td colspan="8" class="bg-[#09422a] text-white font-extrabold px-4 py-2.5 uppercase text-[10px] tracking-wider">
                            I. Ikhtisar Finansial (Financial Summary)
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">1</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-emerald-700 dark:text-emerald-400">FINANSIAL IN</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold">Total Pemasukan</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">Seluruh kas masuk yang bersumber dari hasil penjualan kambing & domba terverifikasi.</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-emerald-600 dark:text-emerald-400">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">2</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-rose-650">FINANSIAL OUT</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold">Total Pengeluaran</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">Biaya operasional kandang, logistik pakan, obat-obatan ternak, dan pembelian bibit baru.</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-rose-600 dark:text-rose-450">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                    </tr>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 bg-slate-50/30 dark:bg-slate-950/20 font-bold transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">3</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-indigo-650 dark:text-indigo-400">FINANSIAL NET</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-extrabold text-[#09422a] dark:text-emerald-450">Laba Bersih (Profit)</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-600">Selisih laba bersih (Pemasukan dikurangi pengeluaran operasional).</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-indigo-600 dark:text-indigo-400">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                    </tr>

                    {{-- ================= SECTION II ================= --}}
                    <tr>
                        <td colspan="8" class="bg-indigo-900 text-white font-extrabold px-4 py-2.5 uppercase text-[10px] tracking-wider">
                            II. Tren Keuangan 6 Bulan Terakhir (Monthly Trends)
                        </td>
                    </tr>
                    @foreach($incomeByMonth as $idx => $inc)
                        @php
                            $exp = $expenseByMonth[$idx];
                            $diff = $inc['total'] - $exp['total'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">{{ $idx + 1 }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-slate-800 dark:text-slate-200">{{ $inc['label'] }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">Tren Finansial</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5">Bulanan</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400">Rekap bulanan debit-kredit-selisih pada periode {{ $inc['label'] }}.</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-emerald-600 dark:text-emerald-450 font-bold">Rp {{ number_format($inc['total'], 0, ',', '.') }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-rose-600 font-bold">Rp {{ number_format($exp['total'], 0, ',', '.') }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black {{ $diff >= 0 ? 'text-indigo-650 dark:text-indigo-400' : 'text-rose-600' }}">
                                Rp {{ number_format($diff, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- ================= SECTION III ================= --}}
                    <tr>
                        <td colspan="8" class="bg-rose-900 text-white font-extrabold px-4 py-2.5 uppercase text-[10px] tracking-wider">
                            III. Alokasi Anggaran Pengeluaran (Expense Allocations)
                        </td>
                    </tr>
                    @forelse($expenseByCategory as $eIdx => $expCat)
                        @php
                            $pct = $totalExpense > 0 ? ($expCat->total / $totalExpense) * 100 : 0;
                            $label = match($expCat->category) {
                                'pakan' => 'Pakan & Nutrisi',
                                'kesehatan' => 'Obat & Kesehatan',
                                'operasional' => 'Operasional Kandang',
                                'pembelian_hewan' => 'Pembelian Indukan/Bibit',
                                default => 'Lainnya'
                            };
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">{{ $eIdx + 1 }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-rose-650">ANGGARAN OUT</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-semibold text-slate-800 dark:text-slate-200">{{ $label }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">Distribusi pengeluaran kas operasional untuk kategori {{ $label }}.</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-rose-600 dark:text-rose-400">Rp {{ number_format($expCat->total, 0, ',', '.') }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-bold text-slate-700 dark:text-slate-350">{{ number_format($pct, 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 border border-slate-200 dark:border-slate-800 text-slate-400 font-semibold">
                                Tidak ada data pengeluaran dalam periode ini.
                            </td>
                        </tr>
                    @endforelse

                    {{-- ================= SECTION IV ================= --}}
                    <tr>
                        <td colspan="8" class="bg-slate-700 text-white font-extrabold px-4 py-2.5 uppercase text-[10px] tracking-wider">
                            IV. Ikhtisar Operasional Kandang (Operational Summaries)
                        </td>
                    </tr>
                    {{-- Row 4.1: Populasi --}}
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">1</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-slate-800 dark:text-slate-200">POPULASI</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-semibold">Stok Kambing</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">
                            Tersedia: <strong>{{ $goatStats['available'] }}</strong> | Terjual: <strong>{{ $goatStats['sold'] }}</strong> | Lahir: <strong>{{ $goatStats['by_origin']['kelahiran'] ?? 0 }}</strong> | Beli: <strong>{{ $goatStats['by_origin']['beli'] ?? 0 }}</strong>
                        </td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-slate-800 dark:text-white">{{ $goatStats['total'] }} Ekor</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                    </tr>
                    {{-- Row 4.2: Kelahiran --}}
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">2</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-slate-800 dark:text-slate-200">KELAHIRAN</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-semibold">Cempe Baru</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">
                            Hidup: <strong>{{ $birthStats['total_male'] }} Jantan, {{ $birthStats['total_female'] }} Betina</strong> | Lahir Mati: <strong class="text-rose-650">{{ $birthStats['total_dead'] }} cempe</strong>
                        </td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-slate-800 dark:text-white">{{ $birthStats['total_births'] }} Indukan</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-bold text-rose-650">Mati: {{ number_format($deathRate, 1) }}%</td>
                    </tr>
                    {{-- Row 4.3: Kesehatan --}}
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">3</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-slate-800 dark:text-slate-200">KESEHATAN</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-semibold">Medis Ternak</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">
                            Vaksin/Vitamin: <strong>{{ $healthStats['by_type']['vaksin'] ?? ($healthStats['by_type']['vaksinasi'] ?? 0) }} kali</strong> | Pengobatan Sakit: <strong>{{ $healthStats['by_type']['pengobatan'] ?? 0 }} kali</strong>
                        </td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-slate-800 dark:text-white">{{ $healthStats['total_records'] }} Rekam</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                    </tr>
                    {{-- Row 4.4: Pakan --}}
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-center font-mono">4</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-400 font-medium font-mono">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-bold text-slate-800 dark:text-slate-200">PAKAN</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 font-semibold">Gudang Pakan</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-slate-500">
                            Frekuensi Distribusi: <strong>{{ $feedingStats['feeding_count'] }} kali log</strong> | Rata-rata per Log: <strong>{{ $feedingStats['feeding_count'] > 0 ? number_format($feedingStats['total_kg_period'] / $feedingStats['feeding_count'], 1) . ' kg' : '-' }}</strong>
                        </td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right font-black text-slate-800 dark:text-white">{{ number_format($feedingStats['total_kg_period'], 1) }} Kg</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                        <td class="border border-slate-200 dark:border-slate-800 px-4 py-2.5 text-right text-slate-400">-</td>
                    </tr>

                    {{-- ================= SECTION V ================= --}}
                    <tr>
                        <td colspan="8" class="bg-[#09422a]/80 text-white font-extrabold px-4 py-2.5 uppercase text-[10px] tracking-wider">
                            V. Buku Kas Detail - Mutasi Kronologis (Detailed Ledger Entries)
                        </td>
                    </tr>
                    @php
                        $ledger = collect([]);
                        foreach($ordersList as $ord) {
                            $ledger->push([
                                'date' => $ord->created_at,
                                'type' => 'pemasukan',
                                'label' => $ord->invoice_number,
                                'desc' => 'Penjualan kambing ke ' . ($ord->user->name ?? '-'),
                                'in' => $ord->total_amount,
                                'out' => 0
                            ]);
                        }
                        foreach($expensesList as $exp) {
                            $ledger->push([
                                'date' => $exp->expense_date,
                                'type' => 'pengeluaran',
                                'label' => $exp->category_label,
                                'desc' => $exp->title . ($exp->description ? ' (' . $exp->description . ')' : ''),
                                'in' => 0,
                                'out' => $exp->amount
                            ]);
                        }
                        
                        // Chronological sorting for Excel flow
                        $ledger = $ledger->sortBy('date')->values();
                        $runningSaldo = 0;
                    @endphp
                    
                    @forelse($ledger as $index => $item)
                        @php
                            if ($item['type'] == 'pemasukan') {
                                $runningSaldo += $item['in'];
                            } else {
                                $runningSaldo -= $item['out'];
                            }
                        @endphp
                        <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-800/50 transition">
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-center text-slate-500 font-mono">{{ $index + 1 }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 font-mono text-slate-700 dark:text-slate-350">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $item['type'] == 'pemasukan' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900' : 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900' }}">
                                    {{ $item['type'] == 'pemasukan' ? 'DEBIT' : 'KREDIT' }}
                                </span>
                            </td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 font-bold text-slate-900 dark:text-white">{{ $item['label'] }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-slate-500">{{ $item['desc'] }}</td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-right font-extrabold text-emerald-600 dark:text-emerald-450">
                                {{ $item['in'] > 0 ? 'Rp ' . number_format($item['in'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-right font-extrabold text-rose-600 dark:text-rose-400">
                                {{ $item['out'] > 0 ? 'Rp ' . number_format($item['out'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="border border-slate-200 dark:border-slate-800 px-4 py-2 text-right font-black {{ $runningSaldo >= 0 ? 'text-indigo-650 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }}">
                                Rp {{ number_format($runningSaldo, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400 font-semibold border border-slate-200 dark:border-slate-800">
                                <i class="fa-solid fa-receipt text-3xl block mb-2 opacity-30"></i>
                                Tidak ada transaksi dalam periode terpilih.
                            </td>
                        </tr>
                    @endforelse
                    
                    {{-- Excel Summary Footer --}}
                    @if(count($ledger) > 0)
                        <tr class="bg-slate-200 dark:bg-slate-950 font-black text-slate-900 dark:text-white text-xs">
                            <td colspan="5" class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right uppercase tracking-wider">TOTAL AKHIR KAS (MUTASI PERIODE INI)</td>
                            <td class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right text-emerald-700 dark:text-emerald-450 text-sm">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right text-rose-600 dark:text-rose-455 text-sm">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            <td class="border border-slate-300 dark:border-slate-800 px-4 py-3 text-right text-indigo-600 dark:text-indigo-400 text-sm">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const periodSelect = document.getElementById('period-select');
    
    function toggleFields(val) {
        document.getElementById('daily-input-wrapper').classList.add('hidden');
        document.getElementById('daily-input-wrapper').classList.remove('flex');
        
        document.getElementById('weekly-input-wrapper').classList.add('hidden');
        document.getElementById('weekly-input-wrapper').classList.remove('flex');
        
        document.getElementById('monthly-input-wrapper').classList.add('hidden');
        document.getElementById('monthly-input-wrapper').classList.remove('flex');
        
        document.getElementById('yearly-input-wrapper').classList.add('hidden');
        document.getElementById('yearly-input-wrapper').classList.remove('flex');
        
        document.getElementById('custom-date-inputs').classList.add('hidden');
        document.getElementById('custom-date-inputs').classList.remove('flex');
        
        if (val === 'daily') {
            document.getElementById('daily-input-wrapper').classList.remove('hidden');
            document.getElementById('daily-input-wrapper').classList.add('flex');
        } else if (val === 'weekly') {
            document.getElementById('weekly-input-wrapper').classList.remove('hidden');
            document.getElementById('weekly-input-wrapper').classList.add('flex');
        } else if (val === 'monthly') {
            document.getElementById('monthly-input-wrapper').classList.remove('hidden');
            document.getElementById('monthly-input-wrapper').classList.add('flex');
        } else if (val === 'yearly') {
            document.getElementById('yearly-input-wrapper').classList.remove('hidden');
            document.getElementById('yearly-input-wrapper').classList.add('flex');
        } else if (val === 'custom' || val === 'transaction') {
            document.getElementById('custom-date-inputs').classList.remove('hidden');
            document.getElementById('custom-date-inputs').classList.add('flex');
        }
    }
    
    periodSelect.addEventListener('change', (e) => {
        toggleFields(e.target.value);
    });
    
    toggleFields(periodSelect.value);
});
</script>
@endsection
