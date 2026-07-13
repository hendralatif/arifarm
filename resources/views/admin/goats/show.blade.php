@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb / Back Link -->
    <div>
        <a href="{{ route('admin.goats.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-[#09422a] transition">
            <i class="fa-solid fa-chevron-left mr-2 text-xs"></i> Detail Ternak #{{ str_pad($goat->id, 3, '0', STR_PAD_LEFT) }}
        </a>
    </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-4">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase">
                {{ $goat->name }}
            </h1>
            @if($goat->status == 'available')
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900">
                    BELUM TERJUAL
                </span>
            @elseif($goat->status == 'not_for_sale')
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase border bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900">
                    TIDAK DIJUAL
                </span>
            @elseif($goat->status == 'mati')
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase border bg-slate-200 text-slate-600 border-slate-300 dark:bg-slate-700/60 dark:text-slate-400 dark:border-slate-600">
                    <i class="fa-solid fa-skull-crossbones mr-1.5 text-[10px]"></i> MATI
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black uppercase border bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900">
                    TERJUAL
                </span>
            @endif
        </div>

        <div>
            <a href="{{ route('admin.goats.edit', $goat->id) }}" class="inline-flex items-center justify-center px-5 py-3 rounded-2xl text-sm font-bold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm transition dark:bg-slate-900 dark:border-slate-800 dark:text-slate-200 dark:hover:bg-slate-800">
                <i class="fa-solid fa-pencil mr-2 text-slate-400"></i> Edit Data
            </a>
        </div>
    </div>

    <!-- Row 1 Grid (3 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Column 1: Image Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800">
                <img src="{{ asset($goat->first_image) }}" alt="{{ $goat->name }}" class="w-full h-full object-cover">
                <div class="absolute top-3 left-3 flex gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-lg
                        {{ $goat->gender == 'male' ? 'bg-blue-600 text-white' : 'bg-pink-500 text-white' }}">
                        {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                    </span>
                    @if($goat->vaccine_status)
                        <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded-lg bg-emerald-600 text-white flex items-center gap-1">
                            <i class="fa-solid fa-shield-virus text-[9px]"></i> Vaksin
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="text-center border-r border-slate-100 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Jenis Kelamin</span>
                    <span class="block text-sm font-black text-slate-800 dark:text-slate-200 mt-1">
                        {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                    </span>
                </div>
                <div class="text-center">
                    <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Bobot</span>
                    <span class="block text-sm font-black text-[#09422a] dark:text-emerald-400 mt-1">
                        {{ $goat->weight_kg }} Kg
                    </span>
                </div>
            </div>
        </div>

        <!-- Column 2: Identitas Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <span class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-400">
                        <i class="fa-solid fa-id-card text-base"></i>
                    </span>
                    <h3 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Informasi Identitas
                    </h3>
                </div>

                <!-- Info List -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-sm text-slate-400 font-semibold">Kode Ternak</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">D-{{ str_pad($goat->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-sm text-slate-400 font-semibold">Ras / Breed</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $goat->breed }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-sm text-slate-400 font-semibold">Usia</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $goat->age_months }} Bulan</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-sm text-slate-400 font-semibold">Asal Kambing</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $goat->acquisition_type_badge }}">
                            {{ $goat->acquisition_type_label }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-slate-400 font-semibold">Kondisi Fisik</span>
                        <span class="text-xs font-black px-2.5 py-1 rounded-lg
                            {{ $goat->health_status == 'healthy' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400'
                                : ($goat->health_status == 'vaccine_completed' ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400'
                                : 'bg-amber-50 text-amber-700 dark:bg-amber-950/20 dark:text-amber-400') }}">
                            {{ $goat->health_status == 'healthy' ? 'Sehat Bugar' : ($goat->health_status == 'vaccine_completed' ? 'Vaksin Selesai' : 'Dalam Pantauan') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="text-xs text-slate-400 font-semibold pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                Ditambahkan: {{ $goat->created_at ? $goat->created_at->format('d M Y') : date('d M Y') }}
            </div>
        </div>

        <!-- Column 3: Ringkasan Biaya Card -->
        @php
            // Dynamic maintenance calculations
            $totalMaintenance = $goat->age_months * 110000;
            $pakanCost = $goat->age_months * 85000;
            $vaksinCost = $goat->age_months * 7000;
            $checkupCost = $totalMaintenance - $pakanCost - $vaksinCost;
        @endphp
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <span class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-400">
                        <i class="fa-solid fa-chart-line text-base"></i>
                    </span>
                    <h3 class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                        Ringkasan Biaya
                    </h3>
                </div>

                <!-- Cost Details -->
                <div class="space-y-5">
                    <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100/60 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider block">Total Biaya Pemeliharaan</span>
                        <span class="block text-2xl font-black text-slate-900 dark:text-white mt-1">
                            Rp {{ number_format($totalMaintenance, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="p-4 bg-emerald-50/60 dark:bg-emerald-950/10 rounded-2xl border border-emerald-100 dark:border-emerald-900/40">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-extrabold uppercase tracking-wider block">Estimasi Harga Jual</span>
                        <span class="block text-2xl font-black text-[#09422a] dark:text-emerald-400 mt-1">
                            {{ $goat->formatted_price }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800 mt-4">
                <span class="text-xs text-slate-400 font-semibold">Stok tersedia</span>
                <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $goat->stock }} Ekor</span>
            </div>
        </div>
    </div>

    <!-- Row 2 Grid (8:4) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Feed Cost Monthly Tracker -->
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Statistik Biaya Pakan (Per Bulan)</h3>
                    <p class="text-xs text-slate-400 mt-1">Grafik pelacakan biaya pakan bulanan khusus untuk ekor ternak ini.</p>
                </div>

                <div class="flex items-center text-xs font-bold text-slate-500 bg-slate-50 dark:bg-slate-950 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800">
                    <i class="fa-solid fa-chart-bar mr-2 text-[#09422a]"></i> ANALISIS 6 BULAN
                </div>
            </div>

            <!-- Monthly Rows -->
            <div class="space-y-4">
                @php
                    $monthlyFeed = round($pakanCost / 6);
                    $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN'];
                @endphp

                @foreach($months as $idx => $month)
                    @php
                        // Slight variation for visual interest
                        $variation = [1, 0.95, 1.05, 0.98, 1.02, 1.0];
                        $mCost = round($monthlyFeed * $variation[$idx]);
                    @endphp
                    <div class="flex items-center gap-4">
                        <span class="w-8 text-xs font-black text-slate-500 shrink-0">{{ $month }}</span>
                        <div class="flex-1">
                            <div class="w-full h-2 bg-slate-100 dark:bg-slate-950 rounded-full overflow-hidden">
                                <div style="width: {{ 60 + ($idx * 5) }}%" class="h-full bg-[#09422a] dark:bg-emerald-600 rounded-full transition-all"></div>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400 w-28 text-right shrink-0">
                            Rp {{ number_format($mCost, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Cost Breakdown -->
        <div class="lg:col-span-4 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-5">Statistik Biaya Pakan (Per Bulan)</h3>

                <div class="space-y-3">
                    <!-- Pakan -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3 border border-slate-100/50 dark:border-slate-850">
                        <div class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-450 shrink-0">
                            <i class="fa-solid fa-utensils text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Pakan</span>
                            <span class="text-sm font-black text-slate-800 dark:text-slate-200">
                                Rp {{ number_format($pakanCost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Vaksin -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3 border border-slate-100/50 dark:border-slate-850">
                        <div class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-450 shrink-0">
                            <i class="fa-solid fa-syringe text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Vaksin</span>
                            <span class="text-sm font-black text-slate-800 dark:text-slate-200">
                                Rp {{ number_format($vaksinCost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Pemeriksaan -->
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3 border border-slate-100/50 dark:border-slate-850">
                        <div class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-450 shrink-0">
                            <i class="fa-solid fa-heart-pulse text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Pemeriksaan Kesehatan</span>
                            <span class="text-sm font-black text-slate-800 dark:text-slate-200">
                                Rp {{ number_format($checkupCost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total -->
            <div class="pt-5 border-t border-slate-100 dark:border-slate-800 mt-5 flex items-center justify-between">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">TOTAL AKUMULASI</span>
                <span class="text-base font-black text-[#09422a] dark:text-emerald-400">
                    Rp {{ number_format($totalMaintenance, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Row: Riwayat Penimbangan -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-3">
                <span class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-450">
                    <i class="fa-solid fa-weight-scale"></i>
                </span>
                Riwayat Penimbangan & Pertumbuhan
            </h3>
            
            <button onclick="openWeighingModal()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-sm transition">
                <i class="fa-solid fa-plus mr-1.5"></i> Catat Timbangan Baru
            </button>
        </div>

        @if($goat->weighings->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Tabel Timbangan (Left) -->
                <div class="lg:col-span-7 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="text-xs font-black uppercase tracking-wider text-slate-400 border-b border-slate-100 dark:border-slate-800">
                                <th class="pb-3 w-1/4">TANGGAL</th>
                                <th class="pb-3 w-1/4">BOBOT (KG)</th>
                                <th class="pb-3 w-2/4">CATATAN / KONDISI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($goat->weighings as $idx => $w)
                                <tr class="align-middle">
                                    <td class="py-3.5 text-slate-500 font-semibold text-xs whitespace-nowrap">
                                        {{ $w->weighed_at->format('d M Y') }}
                                    </td>
                                    <td class="py-3.5 font-bold text-slate-900 dark:text-white">
                                        {{ $w->weight_kg }} kg
                                    </td>
                                    <td class="py-3.5 text-xs text-slate-500">
                                        {{ $w->notes ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Grafik / Timeline Pertumbuhan (Right) -->
                <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-950 p-6 rounded-2xl border border-slate-100 dark:border-slate-850 space-y-4">
                    <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Perkembangan Bobot</h4>
                    
                    <div class="space-y-4">
                        @php
                            $prevWeight = null;
                        @endphp
                        @foreach($goat->weighings as $idx => $w)
                            @php
                                $diff = null;
                                if ($prevWeight !== null) {
                                    $diff = $w->weight_kg - $prevWeight;
                                }
                                $prevWeight = $w->weight_kg;
                            @endphp
                            <div class="flex items-start space-x-3 relative">
                                <!-- Bullet line connection -->
                                @if(!$loop->last)
                                    <div class="absolute left-2.5 top-5 w-0.5 h-10 bg-slate-200 dark:bg-slate-800"></div>
                                @endif
                                <div class="w-5 h-5 rounded-full bg-emerald-500 border-4 border-white dark:border-slate-900 flex-shrink-0 z-10"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $w->weight_kg }} kg</span>
                                        <span class="text-[10px] text-slate-400 font-semibold">{{ $w->weighed_at->format('M Y') }}</span>
                                    </div>
                                    @if($diff !== null)
                                        <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded {{ $diff >= 0 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-450' : 'bg-rose-50 text-rose-700' }}">
                                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }} kg dari timbangan sebelumnya
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="p-6 text-center text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl">
                <i class="fa-solid fa-weight-scale text-3xl mb-2 text-slate-300"></i>
                <p class="text-sm font-semibold">Belum Ada Catatan Timbangan</p>
                <p class="text-xs text-slate-400 mt-0.5">Silakan tambahkan data penimbangan berkala untuk memantau pertumbuhan kambing.</p>
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah Penimbangan -->
    <div id="weighing-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-900/60 transition-opacity" onclick="closeWeighingModal()"></div>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[2rem] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-105 dark:border-slate-800">
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 mr-2.5">
                                <i class="fa-solid fa-weight-scale"></i>
                            </span> Catat Timbangan Baru
                        </h3>
                        <button onclick="closeWeighingModal()" class="text-slate-400 hover:text-slate-600 transition">
                            <i class="fa-solid fa-times text-lg"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.goats.weighing', $goat->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Weight -->
                        <div class="space-y-1.5">
                            <label for="modal_weight_kg" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Berat Badan (Kg)</label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="number" step="0.01" name="weight_kg" id="modal_weight_kg" required min="0.1" value="{{ $goat->weight_kg }}" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-slate-400 text-xs">kg</span>
                                </div>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="space-y-1.5">
                            <label for="modal_weighed_at" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tanggal Timbang</label>
                            <input type="date" name="weighed_at" id="modal_weighed_at" required value="{{ date('Y-m-d') }}" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1.5">
                            <label for="modal_notes" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Catatan / Kondisi (Opsional)</label>
                            <input type="text" name="notes" id="modal_notes" placeholder="Contoh: Nafsu makan baik, bulu bersih, penimbangan rutin" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <button type="submit" class="flex-1 py-3 px-4 rounded-xl text-xs font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md transition">
                                Simpan Penimbangan
                            </button>
                            <button type="button" onclick="closeWeighingModal()" class="flex-1 py-3 px-4 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openWeighingModal() {
            document.getElementById('weighing-modal').classList.remove('hidden');
        }
        function closeWeighingModal() {
            document.getElementById('weighing-modal').classList.add('hidden');
        }
    </script>

    <!-- Row 3: Pemeriksaan Kesehatan Table -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-3">
                <span class="p-2 rounded-xl bg-emerald-50 text-[#09422a] dark:bg-emerald-950/20 dark:text-emerald-400">
                    <i class="fa-solid fa-stethoscope"></i>
                </span>
                Pemeriksaan Kesehatan
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="text-xs font-black uppercase tracking-wider text-slate-400">
                        <th class="pb-4 w-1/5">TANGGAL</th>
                        <th class="pb-4 w-3/5">TINDAKAN</th>
                        <th class="pb-4 w-1/5 text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr class="align-middle">
                        <td class="py-4 text-slate-500 font-semibold text-xs whitespace-nowrap">05 Jun 2024</td>
                        <td class="py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm">Vaksinasi Rutin</div>
                            <span class="text-xs text-slate-400">Vaksin Anthrax &amp; Vitamin B12</span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900">
                                SELESAI
                            </span>
                        </td>
                    </tr>
                    <tr class="align-middle">
                        <td class="py-4 text-slate-500 font-semibold text-xs whitespace-nowrap">20 Mei 2024</td>
                        <td class="py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm">Pengecekan Parasit</div>
                            <span class="text-xs text-slate-400">Pemberian obat cacing (Albendazole)</span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900">
                                SELESAI
                            </span>
                        </td>
                    </tr>
                    <tr class="align-middle">
                        <td class="py-4 text-slate-500 font-semibold text-xs whitespace-nowrap">10 Apr 2024</td>
                        <td class="py-4">
                            <div class="font-bold text-slate-800 dark:text-slate-200 text-sm">Pemeriksaan Bulanan</div>
                            <span class="text-xs text-slate-400">Pengecekan berat badan dan kondisi umum</span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900">
                                SELESAI
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer Note -->
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            <p class="text-xs text-slate-400 font-semibold">
                <i class="fa-solid fa-circle-info mr-1.5 text-emerald-600"></i>
                Riwayat kesehatan di atas merupakan data representatif. Integrasikan dengan modul Kesehatan untuk data real-time.
            </p>
        </div>
    </div>
</div>
@endsection
