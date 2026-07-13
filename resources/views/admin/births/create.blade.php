@extends('layouts.admin')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.births.index') }}" class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tambah Data Kelahiran</h1>
            <p class="text-sm text-slate-500 mt-0.5">Catat data kelahiran anak kambing baru.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.births.store') }}" class="space-y-5">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Induk Betina --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Induk (Betina) <span class="text-rose-500">*</span></label>
                        <select name="mother_id" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('mother_id') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Pilih Induk Betina --</option>
                            @foreach($femaleGoats as $g)
                                <option value="{{ $g->id }}" {{ old('mother_id') == $g->id ? 'selected' : '' }}>{{ $g->name }} ({{ $g->breed }})</option>
                            @endforeach
                        </select>
                        @error('mother_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Pejantan (opsional) --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Pejantan (Jantan, Opsional)</label>
                        <select name="father_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="">-- Tidak Diketahui --</option>
                            @foreach($maleGoats as $g)
                                <option value="{{ $g->id }}" {{ old('father_id') == $g->id ? 'selected' : '' }}>{{ $g->name }} ({{ $g->breed }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', today()->format('Y-m-d')) }}" required max="{{ today()->format('Y-m-d') }}"
                               class="w-full px-4 py-3 rounded-xl border {{ $errors->has('birth_date') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        @error('birth_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    {{-- Proses Kelahiran --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Proses Kelahiran <span class="text-rose-500">*</span></label>
                        <select name="birth_condition" required class="w-full px-4 py-3 rounded-xl border {{ $errors->has('birth_condition') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <option value="normal" {{ old('birth_condition','normal') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="assisted" {{ old('birth_condition') == 'assisted' ? 'selected' : '' }}>Dibantu</option>
                            <option value="cesarean" {{ old('birth_condition') == 'cesarean' ? 'selected' : '' }}>Caesar</option>
                        </select>
                    </div>
                </div>

                {{-- Jumlah Anak --}}
                <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl p-5 space-y-4">
                    <h4 class="font-bold text-sm text-slate-700 dark:text-slate-300"><i class="fa-solid fa-baby mr-2 text-emerald-500"></i>Detail Anak Lahir</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2">Total Anak <span class="text-rose-500">*</span></label>
                            <input type="number" name="total_kids" id="total_kids" value="{{ old('total_kids', 1) }}" min="1" max="10" required
                                   class="w-full px-3 py-2.5 rounded-xl border {{ $errors->has('total_kids') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-900 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white text-center font-bold"
                                   oninput="validateCounts()">
                            @error('total_kids')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-blue-500 mb-2">♂ Jantan</label>
                            <input type="number" name="male_count" id="male_count" value="{{ old('male_count', 0) }}" min="0" max="10" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white text-center font-bold"
                                   oninput="validateCounts()">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-pink-500 mb-2">♀ Betina</label>
                            <input type="number" name="female_count" id="female_count" value="{{ old('female_count', 0) }}" min="0" max="10" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white text-center font-bold"
                                   oninput="validateCounts()">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 mb-2">✝ Lahir Mati</label>
                            <input type="number" name="stillborn_count" id="stillborn_count" value="{{ old('stillborn_count', 0) }}" min="0" max="10" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-900 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white text-center font-bold"
                                   oninput="validateCounts()">
                        </div>
                    </div>
                    {{-- Live Counter Hint --}}
                    <div id="count-hint" class="text-xs text-slate-400 flex items-center gap-1.5 hidden">
                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                        <span id="count-hint-text"></span>
                    </div>
                </div>


                {{-- Kondisi Induk --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Kondisi Induk Setelah Melahirkan <span class="text-rose-500">*</span></label>
                    <input type="text" name="mother_condition" value="{{ old('mother_condition', 'Baik dan sehat') }}" required
                           placeholder="cth: Baik dan sehat, Perlu pengawasan..."
                           class="w-full px-4 py-3 rounded-xl border {{ $errors->has('mother_condition') ? 'border-rose-400' : 'border-slate-200 dark:border-slate-700' }} dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                    @error('mother_condition')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan Tambahan</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white resize-none" placeholder="Informasi tambahan tentang kelahiran ini...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-3 bg-[#09422a] hover:bg-[#083a25] text-white font-bold text-sm rounded-xl shadow-md transition">
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Data Kelahiran
                    </button>
                    <a href="{{ route('admin.births.index') }}" class="flex-1 py-3 text-center border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">Batal</a>
                </div>
            </form>
        </div>

        {{-- Info Card --}}
        <div class="bg-pink-50 dark:bg-pink-950/20 border border-pink-100 dark:border-pink-900 rounded-3xl p-6 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-pink-100 dark:bg-pink-900/40 flex items-center justify-center text-pink-600"><i class="fa-solid fa-heart"></i></div>
                <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">Catatan Penting</h4>
            </div>
            <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Pastikan jumlah jantan + betina + lahir mati = total anak.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Pejantan boleh dikosongkan jika tidak diketahui.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Catat kondisi induk untuk pemantauan pasca-melahirkan.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>Anak hidup (jantan+betina) otomatis masuk ke Data Stok sebagai cempe baru.</li>
                <li class="flex items-start gap-2"><i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>Lahir mati <strong>TIDAK</strong> masuk ke stok &mdash; hanya dicatat secara statistik.</li>
            </ul>
        </div>
    </div>
</div>

<script>
function validateCounts() {
    const total     = parseInt(document.getElementById('total_kids').value) || 0;
    const male      = parseInt(document.getElementById('male_count').value) || 0;
    const female    = parseInt(document.getElementById('female_count').value) || 0;
    const stillborn = parseInt(document.getElementById('stillborn_count').value) || 0;
    const sum = male + female + stillborn;
    const hint = document.getElementById('count-hint');
    const hintText = document.getElementById('count-hint-text');

    if (sum !== total) {
        hint.classList.remove('hidden');
        hintText.textContent = 'Jumlah jantan + betina + lahir mati (' + sum + ') harus = total anak (' + total + ').';
        hint.querySelector('i').className = 'fa-solid fa-circle-exclamation text-rose-500';
    } else if (total > 0) {
        hint.classList.remove('hidden');
        hintText.textContent = '✓ Jumlah sudah sesuai: ' + total + ' anak (' + male + '♂ + ' + female + '♀ + ' + stillborn + '✝).';
        hint.querySelector('i').className = 'fa-solid fa-circle-check text-emerald-500';
    } else {
        hint.classList.add('hidden');
    }
}
validateCounts();
</script>
@endsection
