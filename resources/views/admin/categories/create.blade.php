@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 mb-2">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Kategori
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tambah Kategori Baru</h1>
        <p class="text-sm text-slate-500">Definisikan ras kambing baru atau pilihan paket pesanan.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Name -->
            <div class="space-y-1">
                <label for="name" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nama Kategori</label>
                <input type="text" id="name" name="name" required placeholder="Contoh: Kambing Boer, Paket Aqiqah" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                @error('name')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label for="description" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Deskripsi Singkat</label>
                <textarea id="description" name="description" rows="4" placeholder="Tuliskan keterangan mengenai karakteristik fisik ras kambing atau spesifikasi paket ini..." class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                @error('description')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Image File -->
            <div class="space-y-1">
                <label for="image" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Gambar Banner Kategori</label>
                <input type="file" id="image" name="image" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                @error('image')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-650 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/10 transition">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
