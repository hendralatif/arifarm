@extends('layouts.admin')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.health.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Detail Catatan Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $record->check_date->format('d F Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Goat Info --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">Info Kambing</h3>
            <div class="space-y-3 text-sm">
                <div><span class="text-slate-400 text-xs block">Nama</span><span class="font-bold text-slate-900 dark:text-white">{{ $record->goat->name }}</span></div>
                <div><span class="text-slate-400 text-xs block">Ras</span><span class="text-slate-600 dark:text-slate-300">{{ $record->goat->breed }}</span></div>
                <div><span class="text-slate-400 text-xs block">Jenis Kelamin</span><span class="text-slate-600 dark:text-slate-300">{{ $record->goat->gender === 'male' ? '♂ Jantan' : '♀ Betina' }}</span></div>
                <div><span class="text-slate-400 text-xs block">Status Saat Ini</span>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $record->health_status_badge }}">
                        {{ $record->health_status_label }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Medical Detail --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="font-bold text-sm text-slate-400 uppercase tracking-wider mb-4">Detail Catatan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-xs text-slate-400 block">Jenis Catatan</span><span class="font-bold text-slate-800 dark:text-white">{{ $record->record_type_label }}</span></div>
                <div><span class="text-xs text-slate-400 block">Tanggal Pemeriksaan</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->check_date->format('d M Y') }}</span></div>
                @if($record->vet_name)
                <div><span class="text-xs text-slate-400 block">Dokter / Petugas</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->vet_name }}</span></div>
                @endif
                @if($record->diagnosis)
                <div><span class="text-xs text-slate-400 block">Diagnosis</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->diagnosis }}</span></div>
                @endif
                @if($record->treatment)
                <div><span class="text-xs text-slate-400 block">Penanganan</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->treatment }}</span></div>
                @endif
                @if($record->medicine)
                <div><span class="text-xs text-slate-400 block">Obat / Vaksin</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->medicine }}</span></div>
                @endif
                @if($record->medicine_dose)
                <div><span class="text-xs text-slate-400 block">Dosis</span><span class="font-semibold text-slate-700 dark:text-slate-300">{{ $record->medicine_dose }} ml/mg</span></div>
                @endif
                @if($record->next_checkup)
                <div><span class="text-xs text-slate-400 block">Cek Berikutnya</span>
                    <span class="font-semibold {{ $record->next_checkup->lte(today()->addDays(3)) ? 'text-amber-600' : 'text-slate-700 dark:text-slate-300' }}">
                        {{ $record->next_checkup->format('d M Y') }}
                        @if($record->next_checkup->isToday()) <span class="text-xs font-bold text-rose-600">(Hari ini!)</span> @endif
                    </span>
                </div>
                @endif
            </div>
            @if($record->notes)
            <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl text-sm text-slate-600 dark:text-slate-400">
                <span class="text-xs font-bold text-slate-400 block mb-1">Catatan</span>
                {{ $record->notes }}
            </div>
            @endif

            <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-400">Dicatat oleh <strong>{{ $record->recorder?->name }}</strong> pada {{ $record->created_at->format('d M Y, H:i') }}</span>
                <form action="{{ route('admin.health.destroy', $record->id) }}" method="POST" class="ml-auto" onsubmit="return confirm('Hapus catatan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold text-xs transition">
                        <i class="fa-solid fa-trash"></i> Hapus Catatan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
