@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Penimbangan Bobot Ternak</h1>
            <p class="text-sm text-slate-500">Mencatat riwayat pertumbuhan dan perkembangan berat badan kambing & domba secara terpusat.</p>
        </div>
        <button @click="showModal = true" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/10 transition">
            <i class="fa-solid fa-scale-balanced mr-2"></i> Catat Penimbangan
        </button>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Rata-rata Bobot --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-550 uppercase tracking-wider">Rata-rata Bobot Ternak</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($avgWeight, 1) }} kg</h3>
                <span class="text-[10px] text-slate-400">Dari seluruh kambing aktif tersedia</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/30 flex items-center justify-center text-indigo-650 text-xl shrink-0">
                <i class="fa-solid fa-calculator"></i>
            </div>
        </div>

        {{-- Bobot Tertinggi --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-550 uppercase tracking-wider">Bobot Tertinggi</p>
                <h3 class="text-2xl font-black text-emerald-650 dark:text-emerald-450">{{ number_format($maxWeight, 1) }} kg</h3>
                <span class="text-[10px] text-slate-400">Rekor berat kambing terberat</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 text-xl shrink-0">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>

        {{-- Total Log --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-550 uppercase tracking-wider">Total Log Penimbangan</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalLogs }} Kali</h3>
                <span class="text-[10px] text-slate-400">Total riwayat data tersimpan</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/30 flex items-center justify-center text-amber-600 text-xl shrink-0">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>
    </div>

    {{-- Search / Filter bar --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <form action="{{ route('admin.weighings.index') }}" method="GET" class="relative w-full md:max-w-xs">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kambing..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-250 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500 outline-none transition">
            <i class="fa-solid fa-search absolute left-3.5 top-3 text-slate-400 text-sm"></i>
        </form>
        
        <div class="text-xs text-slate-400 font-semibold">
            Menampilkan {{ $weighings->firstItem() ?: 0 }} - {{ $weighings->lastItem() ?: 0 }} dari {{ $weighings->total() }} catatan
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 pb-3">
                        <th class="pb-3">Hewan Ternak</th>
                        <th class="pb-3 text-center">Bobot (kg)</th>
                        <th class="pb-3">Tanggal Penimbangan</th>
                        <th class="pb-3">Catatan Perkembangan</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($weighings as $weigh)
                        <tr class="align-middle">
                            <!-- Goat Details -->
                            <td class="py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-105 border border-slate-150 shrink-0">
                                        <img src="{{ asset($weigh->goat->first_image) }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.goats.show', $weigh->goat_id) }}" class="font-extrabold text-slate-900 dark:text-white hover:text-emerald-650 hover:underline">
                                            {{ $weigh->goat->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 block">{{ $weigh->goat->breed }} ({{ $weigh->goat->category->name }})</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Weight -->
                            <td class="py-4 text-center font-black text-slate-900 dark:text-white text-base">
                                {{ number_format($weigh->weight_kg, 1) }} kg
                            </td>

                            <!-- Weighed Date -->
                            <td class="py-4 text-xs font-bold text-slate-600 dark:text-slate-400">
                                {{ $weigh->weighed_at->format('d M Y') }}
                            </td>

                            <!-- Notes -->
                            <td class="py-4 text-xs text-slate-500 max-w-sm">
                                {{ $weigh->notes ?: '-' }}
                            </td>

                            <!-- Action -->
                            <td class="py-4 text-center">
                                <form action="{{ route('admin.weighings.destroy', $weigh->id) }}" method="POST" class="inline m-0 p-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus catatan penimbangan ini?')" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-scale-balanced text-4xl block mb-3 opacity-30"></i>
                                <p class="font-semibold text-sm">Tidak ada catatan penimbangan yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6 border-t border-slate-100 dark:border-slate-800 pt-6">
            {{ $weighings->links() }}
        </div>
    </div>

    {{-- Modal Add Weighing --}}
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-3xl shadow-xl border border-slate-200/60 dark:border-slate-800 overflow-hidden transform transition-all" @click.away="showModal = false">
            
            {{-- Modal Header --}}
            <div class="h-16 border-b border-slate-100 dark:border-slate-850 px-6 flex items-center justify-between">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-emerald-600"></i> Catat Penimbangan Baru
                </h3>
                <button @click="showModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>

            {{-- Modal Form --}}
            <form action="{{ route('admin.weighings.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                
                {{-- Goat Selector --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-750 dark:text-slate-300">Pilih Kambing / Domba</label>
                    <select name="goat_id" required class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">-- Pilih Kambing --</option>
                        @foreach($goats as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} ({{ $g->breed }} - {{ number_format($g->weight_kg, 1) }} kg)</option>
                        @endforeach
                    </select>
                </div>

                {{-- Weight --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-750 dark:text-slate-300">Bobot Baru (kg)</label>
                    <input type="number" step="0.1" min="0.1" name="weight_kg" required placeholder="Contoh: 45.5" class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                {{-- Date --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-750 dark:text-slate-300">Tanggal Penimbangan</label>
                    <input type="date" name="weighed_at" required value="{{ today()->toDateString() }}" class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                {{-- Notes --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-750 dark:text-slate-300">Catatan (Opsional)</label>
                    <textarea name="notes" rows="3" placeholder="Contoh: Bobot naik, nafsu makan tinggi..." class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-850">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-750 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/10 transition">
                        Simpan Catatan
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection
