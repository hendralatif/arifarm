@extends('layouts.admin')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.health.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tambah Catatan Kesehatan</h1>
            <p class="text-sm text-slate-500 mt-0.5">Rekam kondisi kesehatan dan penanganan medis kambing.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.health.store') }}" class="space-y-5">
                @csrf

                {{-- Pilih Kambing --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Kambing <span class="text-rose-500">*</span></label>
                    <select name="goat_id" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('goat_id') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        <option value="">-- Pilih Kambing --</option>
                        @foreach($goats as $goat)
                            <option value="{{ $goat->id }}" {{ old('goat_id') == $goat->id ? 'selected' : '' }}>
                                {{ $goat->name }} — {{ $goat->breed }} ({{ $goat->gender === 'male' ? 'Jantan' : 'Betina' }})
                            </option>
                        @endforeach
                    </select>
                    @error('goat_id')<p class="mt-1 text-xs text-rose-600"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Cek <span class="text-rose-500">*</span></label>
                        <input type="date" name="check_date" value="{{ old('check_date', today()->format('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-xl border {{ $errors->has('check_date') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        @error('check_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Jenis Catatan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jenis Catatan <span class="text-rose-500">*</span></label>
                        <select name="record_type" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('record_type') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(['checkup'=>'Pemeriksaan Rutin','vaccination'=>'Vaksinasi','treatment'=>'Pengobatan','observation'=>'Observasi'] as $v => $l)
                                <option value="{{ $v }}" {{ old('record_type') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('record_type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status Kesehatan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Status Kesehatan <span class="text-rose-500">*</span></label>
                        <select name="health_status" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('health_status') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Status --</option>
                            @foreach(['healthy'=>'Sehat','sick'=>'Sakit','recovering'=>'Dalam Pemulihan','critical'=>'Kritis'] as $v => $l)
                                <option value="{{ $v }}" {{ old('health_status') == $v ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('health_status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Dokter Hewan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Dokter / Petugas</label>
                        <input type="text" name="vet_name" value="{{ old('vet_name') }}" placeholder="drh. ..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Diagnosis --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Diagnosis</label>
                        <input type="text" name="diagnosis" value="{{ old('diagnosis') }}" placeholder="cth: Kembung, Diare, Cacingan..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Penanganan --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Penanganan</label>
                        <input type="text" name="treatment" value="{{ old('treatment') }}" placeholder="cth: Pemberian obat, Isolasi..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Obat --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Obat / Vaksin</label>
                        <input type="text" name="medicine" value="{{ old('medicine') }}" placeholder="cth: Anthelmintik, Vaksin PMK..."
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Dosis --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Dosis (ml/mg)</label>
                        <input type="number" name="medicine_dose" value="{{ old('medicine_dose') }}" min="0" step="0.01" placeholder="0.00"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    </div>

                    {{-- Jadwal Cek Berikutnya --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Jadwal Cek Berikutnya</label>
                        <input type="date" name="next_checkup" value="{{ old('next_checkup') }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        @error('next_checkup')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan Tambahan</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white resize-none" placeholder="Informasi tambahan tentang kondisi kambing...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-3 bg-[#09422a] hover:bg-[#083a25] text-white font-bold text-sm rounded-xl shadow-md transition">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Catatan
                    </button>
                    <a href="{{ route('admin.health.index') }}" class="flex-1 py-3 text-center border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">Batal</a>
                </div>
            </form>
        </div>

        {{-- Info --}}
        <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900 rounded-3xl p-6 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-blue-600"><i class="fa-solid fa-circle-info"></i></div>
                <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">Panduan Pengisian</h4>
            </div>
            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Pilih kambing yang tepat dari daftar stok.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Status kesehatan akan otomatis memperbarui data kambing.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Isi jadwal cek berikutnya agar muncul di pengingat.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Diagnosis dan obat wajib diisi jika status sakit.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
