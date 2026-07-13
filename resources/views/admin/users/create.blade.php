@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}"
           class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tambah Pelanggan Baru</h1>
            <p class="text-sm text-slate-500 mt-0.5">Daftarkan akun pelanggan baru ke sistem Ari Farm.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Card --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-8 shadow-sm">
            <form method="POST" action="{{ route('admin.users.store') }}" id="create-customer-form">
                @csrf

                <div class="space-y-5">

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   placeholder="Masukkan nama lengkap pelanggan"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50 dark:bg-rose-950/10' : 'border-slate-200 dark:border-slate-700 dark:bg-slate-950' }} text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   placeholder="contoh@email.com"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50 dark:bg-rose-950/10' : 'border-slate-200 dark:border-slate-700 dark:bg-slate-950' }} text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" id="password" name="password"
                                   placeholder="Minimal 8 karakter"
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50 dark:bg-rose-950/10' : 'border-slate-200 dark:border-slate-700 dark:bg-slate-950' }} text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <button type="button" onclick="togglePassword('password', this)"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Konfirmasi Password <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   placeholder="Ulangi password"
                                   class="w-full pl-10 pr-12 py-3 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition dark:text-white">
                            <button type="button" onclick="togglePassword('password_confirmation', this)"
                                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition">
                                <i class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2"></div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-1">
                        <button type="submit"
                                class="flex-1 flex items-center justify-center gap-2 py-3 bg-[#09422a] hover:bg-[#083a25] text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-900/20 transition-all duration-150">
                            <i class="fa-solid fa-user-plus"></i>
                            Simpan & Daftarkan Pelanggan
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex-1 flex items-center justify-center gap-2 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            <i class="fa-solid fa-times"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Info / Help Card --}}
        <div class="space-y-4">
            {{-- Tips Card --}}
            <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">Panduan Pengisian</h4>
                </div>
                <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                        Gunakan nama lengkap sesuai identitas pelanggan.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                        Email digunakan untuk login ke akun pelanggan.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                        Password minimal 8 karakter. Informasikan ke pelanggan.
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                        Akun yang dibuat akan langsung dapat login.
                    </li>
                </ul>
            </div>

            {{-- Role Info --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                <h4 class="font-extrabold text-slate-800 dark:text-white text-sm mb-3">
                    <i class="fa-solid fa-user-tag mr-2 text-indigo-500"></i> Role Akun
                </h4>
                <div class="flex items-center gap-2 p-3 bg-indigo-50 dark:bg-indigo-950/20 rounded-xl border border-indigo-100 dark:border-indigo-900">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold">
                        <i class="fa-solid fa-user text-[9px]"></i> Pelanggan
                    </span>
                    <span class="text-xs text-slate-500 dark:text-slate-400">— Akun standar non-admin</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endsection
