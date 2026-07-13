@extends('layouts.admin')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.feedings.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Beri Pakan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Catat pemberian pakan dari persediaan stok (Mendukung hingga 2 jenis pakan sekaligus).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.feedings.store') }}" class="space-y-5">
                @csrf

                {{-- Today's Active Permanent Schedule Banner --}}
                @php
                    $todayKey = ['Monday'=>'senin','Tuesday'=>'selasa','Wednesday'=>'rabu','Thursday'=>'kamis','Friday'=>'jumat','Saturday'=>'sabtu','Sunday'=>'minggu'][now()->format('l')] ?? 'senin';
                    $dayLabelId = ['senin'=>'Senin','selasa'=>'Selasa','rabu'=>'Rabu','kamis'=>'Kamis','jumat'=>'Jumat','sabtu'=>'Sabtu','minggu'=>'Minggu'][$todayKey] ?? ucfirst($todayKey);
                @endphp
                @if($todaySchedules->count() > 0)
                    <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900 rounded-2xl p-4 flex items-start gap-3">
                        <div class="text-emerald-600 text-base mt-0.5 shrink-0">
                            <i class="fa-solid fa-infinity"></i>
                        </div>
                        <div class="flex-1 text-xs">
                            <p class="font-extrabold text-[#09422a] dark:text-emerald-400 text-sm">Jadwal Pakan Aktif Hari Ini — {{ $dayLabelId }}</p>
                            <p class="text-emerald-800 dark:text-emerald-400/80 mt-0.5 leading-relaxed">
                                Jadwal pakan permanen untuk hari <strong>{{ $dayLabelId }}</strong> sudah dikonfigurasikan. Pilih sesi <strong>Pagi</strong> atau <strong>Sore</strong> di bawah — form akan otomatis terisi sesuai jadwal yang berlaku.
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($todaySchedules as $sess => $sch)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold {{ $sess == 'pagi' ? 'bg-amber-100 text-amber-800 border border-amber-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-300' }}">
                                        <i class="fa-solid fa-{{ $sess == 'pagi' ? 'sun' : 'moon' }} text-[9px]"></i>
                                        {{ ucfirst($sess) }}:
                                        {{ $sch->feedStock1?->name ?? '-' }}
                                        @if($sch->feedStock2)
                                            + {{ $sch->feedStock2?->name }}
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-2xl p-4 flex items-start gap-3">
                        <div class="text-amber-500 text-base mt-0.5 shrink-0">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="text-xs">
                            <p class="font-extrabold text-amber-800 dark:text-amber-400">Belum Ada Jadwal Aktif Hari Ini — {{ $dayLabelId }}</p>
                            <p class="text-amber-700 dark:text-amber-400/80 mt-0.5">Jadwal pakan untuk hari <strong>{{ $dayLabelId }}</strong> belum dikonfigurasi. Silakan isi form secara manual atau <a href="{{ route('admin.feedings.index', ['tab' => 'schedule']) }}" class="font-bold underline">atur jadwal permanen</a> terlebih dahulu.</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal <span class="text-rose-500">*</span></label>
                        <input type="date" name="feeding_date" value="{{ old('feeding_date', today()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-xl border {{ $errors->has('feeding_date') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        @error('feeding_date')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                    </div>

                    {{-- Waktu --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Waktu (Opsional)</label>
                        <input type="time" name="feeding_time" value="{{ old('feeding_time') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Sesi --}}
                    <div>
                        @php
                            $defaultSession = old('session', request('session'));
                            if (!$defaultSession) {
                                $defaultSession = now()->hour < 12 ? 'pagi' : 'sore';
                            }
                        @endphp
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Sesi <span class="text-rose-500">*</span></label>
                        <select name="session" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('session') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Sesi --</option>
                            @foreach(['pagi'=>'Pagi', 'sore'=>'Sore'] as $v => $l)
                                <option value="{{ $v }}" {{ $defaultSession == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('session')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Double Pakan Selection --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-t border-slate-100 dark:border-slate-800 pt-4">
                    {{-- Pakan 1 --}}
                    <div class="space-y-4 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/20">
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Jenis Pakan 1
                        </h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Pilih Stok Pakan <span class="text-rose-500">*</span></label>
                            <select name="feed_stock_1_id" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('feed_stock_1_id') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <option value="">-- Pilih Pakan --</option>
                                @foreach($feedStocks as $stock)
                                    <option value="{{ $stock->id }}" {{ (old('feed_stock_1_id') == $stock->id || (request('mineral_blok') && $stock->name == 'Mineral Blok')) ? 'selected' : '' }}>
                                        {{ $stock->name }} (Stok: {{ number_format($stock->stock_kg, 1) }} kg)
                                    </option>
                                @endforeach
                            </select>
                            @error('feed_stock_1_id')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Jumlah (Kg) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="number" name="quantity_1_kg" value="{{ old('quantity_1_kg') }}" min="0.1" max="9999" step="0.1" placeholder="0.0"
                                       class="w-full pl-4 pr-12 py-3 rounded-xl border {{ $errors->has('quantity_1_kg') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">kg</span>
                            </div>
                            @error('quantity_1_kg')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Pakan 2 --}}
                    <div class="space-y-4 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-950/20">
                        <h3 class="text-xs font-extrabold text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Jenis Pakan 2 (Opsional)
                        </h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Pilih Stok Pakan (Opsional)</label>
                            <select name="feed_stock_2_id" class="w-full px-4 py-3 rounded-xl border {{ $errors->has('feed_stock_2_id') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <option value="">-- Pilih Pakan (Kosongkan jika 1 jenis) --</option>
                                @foreach($feedStocks as $stock)
                                    <option value="{{ $stock->id }}" {{ old('feed_stock_2_id') == $stock->id ? 'selected' : '' }}>
                                        {{ $stock->name }} (Stok: {{ number_format($stock->stock_kg, 1) }} kg)
                                    </option>
                                @endforeach
                            </select>
                            @error('feed_stock_2_id')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Jumlah (Kg)</label>
                            <div class="relative">
                                <input type="number" name="quantity_2_kg" value="{{ old('quantity_2_kg') }}" min="0.1" max="9999" step="0.1" placeholder="0.0"
                                       class="w-full pl-4 pr-12 py-3 rounded-xl border {{ $errors->has('quantity_2_kg') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">kg</span>
                            </div>
                            @error('quantity_2_kg')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Jumlah Kambing & Catatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jumlah Kambing <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="goat_count" value="{{ old('goat_count') }}" min="1" max="9999" placeholder="0" required
                                   class="w-full pl-4 pr-16 py-3 rounded-xl border {{ $errors->has('goat_count') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">ekor</span>
                        </div>
                        @error('goat_count')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Catatan tambahan pemberian pakan..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>
                </div>

                {{-- Schedule Auto-fill Info Banner --}}
                <div id="schedule-info-banner" class="hidden bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-150 dark:border-indigo-900 rounded-2xl p-4 flex gap-3 text-xs text-indigo-800 dark:text-indigo-300">
                    <i class="fa-solid fa-magic-wand-sparkles mt-0.5 shrink-0 animate-pulse text-indigo-650 dark:text-indigo-400"></i>
                    <div>
                        <span class="font-bold">Terisi Otomatis!</span>
                        <p id="schedule-details" class="mt-0.5 text-slate-600 dark:text-slate-400"></p>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="flex-1 py-3 bg-[#09422a] hover:bg-[#083a25] text-white font-bold text-sm rounded-xl shadow-md transition">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Catatan Pakan
                    </button>
                    <a href="{{ route('admin.feedings.index') }}" class="flex-1 py-3 text-center border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Tip Card --}}
        <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900 rounded-3xl p-6 shadow-sm h-fit space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">Persyaratan Pakan</h4>
            </div>
            <ul class="space-y-3 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                    <span><strong>Konsentrat</strong>: Pakan bergizi tinggi untuk penggemukan.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                    <span><strong>Rumput Pakchong</strong>: Serat kasar esensial untuk pencernaan sehat.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                    <span><strong>Mineral Blok</strong>: Suplemen garam & mineral mikronutrisi yang diletakkan di kandang.</span>
                </li>
                <li class="flex items-start gap-2 text-indigo-700 dark:text-indigo-400">
                    <i class="fa-solid fa-bell mt-0.5 shrink-0"></i>
                    <span><strong>Mineral Blok</strong> dijadwalkan diberikan setiap <strong>2 minggu</strong> sekali secara rutin.</span>
                </li>
                <li class="flex items-start gap-2 text-rose-750 dark:text-rose-400">
                    <i class="fa-solid fa-circle-exclamation mt-0.5 shrink-0"></i>
                    <span>Pengisian data pakan secara otomatis mengurangi stok yang tersedia di gudang pakan.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const todaySchedules = @json($todaySchedules);
    const totalGoats = {{ $totalGoats }};
    const sessionSelect = document.querySelector('select[name="session"]');

    sessionSelect.addEventListener('change', function() {
        const session = this.value;
        const banner = document.getElementById('schedule-info-banner');
        const details = document.getElementById('schedule-details');

        // Clear values first
        document.querySelector('select[name="feed_stock_1_id"]').value = "";
        document.querySelector('input[name="quantity_1_kg"]').value = "";
        document.querySelector('select[name="feed_stock_2_id"]').value = "";
        document.querySelector('input[name="quantity_2_kg"]').value = "";

        if (session && todaySchedules[session]) {
            const sch = todaySchedules[session];

            // Auto fill feed 1
            if (sch.feed_stock_1_id) {
                document.querySelector('select[name="feed_stock_1_id"]').value = sch.feed_stock_1_id;
                let qty1 = parseFloat(sch.quantity_1_kg);
                if (sch.qty_type_1 === 'per_goat') {
                    qty1 = (qty1 * totalGoats).toFixed(1);
                }
                document.querySelector('input[name="quantity_1_kg"]').value = qty1;
            }

            // Auto fill feed 2
            if (sch.feed_stock_2_id) {
                document.querySelector('select[name="feed_stock_2_id"]').value = sch.feed_stock_2_id;
                let qty2 = parseFloat(sch.quantity_2_kg);
                if (sch.qty_type_2 === 'per_goat') {
                    qty2 = (qty2 * totalGoats).toFixed(1);
                }
                document.querySelector('input[name="quantity_2_kg"]').value = qty2;
            }

            // Auto fill goat count
            document.querySelector('input[name="goat_count"]').value = totalGoats;

            // Show banner
            banner.classList.remove('hidden');
            let feed1Name = sch.feed_stock1 ? sch.feed_stock1.name : '-';
            let feed2Name = sch.feed_stock2 ? sch.feed_stock2.name : 'Tidak ada';
            details.innerHTML = `Jadwal hari ini (Sesi <strong>${session.toUpperCase()}</strong>) diterapkan otomatis:<br>Pakan 1: <strong>${feed1Name}</strong>, Pakan 2: <strong>${feed2Name}</strong>, Jumlah kambing aktif: <strong>${totalGoats} ekor</strong>.`;
        } else {
            banner.classList.add('hidden');
        }
    });

    // Handle initial trigger if old value exists
    if (sessionSelect.value) {
        sessionSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
