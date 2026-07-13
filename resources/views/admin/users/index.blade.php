@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Manajemen Pelanggan</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data pelanggan terdaftar peternakan Ari Farm.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-900/20 transition-all duration-150">
            <i class="fa-solid fa-user-plus"></i>
            Tambah Pelanggan
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/20 dark:text-indigo-400 text-2xl">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Total Pelanggan</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalCustomers }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/20 dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Pelanggan Aktif</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $activeCustomers }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm flex items-center gap-4">
            <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 text-2xl">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase block">Belum Bertransaksi</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalCustomers - $activeCustomers }}</span>
            </div>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full md:max-w-sm flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition">
                <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            </div>
            <button type="submit" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 transition">
                Cari
            </button>
        </form>

        <span class="text-xs font-semibold text-slate-400 shrink-0">
            Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pelanggan
        </span>
    </div>

    {{-- Users Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Email</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Total Pesanan</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Bergabung</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                        <tr class="align-middle hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors duration-100">
                            {{-- Avatar + Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-[10px] text-slate-400">ID #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-slate-500 text-xs select-all">
                                {{ $user->email }}
                            </td>

                            {{-- Order Count --}}
                            <td class="px-6 py-4 text-center">
                                @if($user->orders_count > 0)
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400 text-xs font-bold hover:bg-indigo-100 transition">
                                        <i class="fa-solid fa-receipt text-[9px]"></i>
                                        {{ $user->orders_count }} Pesanan
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-400 dark:bg-slate-800 text-xs font-semibold">
                                        Belum ada
                                    </span>
                                @endif
                            </td>

                            {{-- Join Date --}}
                            <td class="px-6 py-4 text-xs text-slate-400">
                                <i class="fa-solid fa-calendar-alt mr-1 opacity-60"></i>
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                       title="Lihat Detail"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-950/50 font-bold text-xs transition">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>

                                    {{-- Delete Button → triggers modal --}}
                                    <button type="button"
                                            onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            title="Hapus Pelanggan"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-950/50 font-bold text-xs transition">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-16">
                                <div class="flex flex-col items-center gap-3 text-slate-400">
                                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-3xl">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-600 dark:text-slate-300">Tidak ada pelanggan ditemukan</p>
                                        <p class="text-xs mt-1">Coba ubah kata kunci pencarian atau tambahkan pelanggan baru.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4 border border-slate-200 dark:border-slate-800 z-10">
        <div class="flex items-center gap-4 mb-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-950/30 flex items-center justify-center text-rose-600 text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Hapus Pelanggan</h3>
                <p class="text-sm text-slate-500 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>

        <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-100 dark:border-rose-900 rounded-2xl px-5 py-4 mb-6">
            <p class="text-sm text-slate-700 dark:text-slate-300">
                Anda akan menghapus akun pelanggan: <br>
                <span id="modal-user-name" class="font-extrabold text-rose-700 dark:text-rose-400 text-base"></span>
            </p>
            <p class="text-xs text-slate-500 mt-2">
                <i class="fa-solid fa-circle-info mr-1 text-amber-500"></i>
                Semua pesanan aktif pelanggan ini akan otomatis dibatalkan dan stok kambing dikembalikan.
            </p>
        </div>

        <form id="delete-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-md shadow-rose-900/20 transition">
                    <i class="fa-solid fa-trash mr-1.5"></i> Ya, Hapus Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(userId, userName) {
    document.getElementById('modal-user-name').textContent = userName;
    document.getElementById('delete-form').action = '/admin/users/' + userId;
    document.getElementById('delete-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endsection
