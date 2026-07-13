@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Kategori</h1>
            <p class="text-sm text-slate-500">Buat dan kelola ras kambing/domba serta paket aqiqah/qurban.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/10 transition">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Kategori
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-400 pb-3">
                        <th class="pb-3">Gambar</th>
                        <th class="pb-3">Nama Kategori</th>
                        <th class="pb-3">Slug</th>
                        <th class="pb-3">Deskripsi</th>
                        <th class="pb-3 text-center">Jumlah Hewan</th>
                        <th class="pb-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($categories as $category)
                        <tr class="align-middle">
                            <td class="py-4">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950">
                                    <img src="{{ asset($category->image) }}" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="py-4 font-bold text-slate-900 dark:text-white">{{ $category->name }}</td>
                            <td class="py-4 text-slate-400 select-all font-mono text-xs">{{ $category->slug }}</td>
                            <td class="py-4 text-slate-500 max-w-xs truncate">{{ $category->description ?: '-' }}</td>
                            <td class="py-4 text-center font-semibold">{{ $category->goats_count }} Ekor</td>
                            <td class="py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Link -->
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-2 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-350 dark:hover:bg-slate-700 transition" title="Edit">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    
                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="inline m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Semua kambing dalam kategori ini juga akan terhapus!')" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
