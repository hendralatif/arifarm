@extends('layouts.public')

@section('title', 'Edit Profil Saya - ARI FARM')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-950/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold font-display text-slate-900 dark:text-white">Pengaturan Profil</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Perbarui detail akun, alamat email, kata sandi, dan keamanan profil Anda.</p>
            </div>
            
            <div class="flex items-center space-x-2 w-full sm:w-auto">
                <a href="{{ route('dashboard') }}" class="w-1/2 sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-sm font-bold text-slate-700 dark:text-slate-350 bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-arrow-left mr-2 text-xs"></i> Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-1/2 sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:hover:bg-rose-950/40 transition">
                        <i class="fa-solid fa-sign-out mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Sidebar Ringkasan -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Info Profile Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl p-6 shadow-sm space-y-4 text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 dark:bg-[#09422a]/30 dark:text-emerald-400 text-3xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base">{{ $user->name }}</h3>
                        <span class="text-xs text-slate-400 block mt-0.5">{{ $user->email }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-850 dark:bg-emerald-950/30 dark:text-emerald-400">
                            Member Terdaftar
                        </span>
                    </div>
                </div>

                <!-- Navigation List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl p-4 shadow-sm space-y-1">
                    <a href="#profile-info" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <span class="text-emerald-600 dark:text-emerald-450"><i class="fa-solid fa-user-gear"></i></span>
                        <span>Informasi Profil</span>
                    </a>
                    <a href="#update-password" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        <span class="text-emerald-600 dark:text-emerald-450"><i class="fa-solid fa-lock"></i></span>
                        <span>Ubah Kata Sandi</span>
                    </a>
                    <a href="#delete-account" class="flex items-center space-x-3 px-4 py-3 rounded-2xl text-sm font-bold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition">
                        <span class="text-rose-500"><i class="fa-solid fa-trash-can"></i></span>
                        <span>Hapus Akun</span>
                    </a>
                </div>
            </div>

            <!-- Right: Forms -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Profile Information Card -->
                <div id="profile-info" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-150 text-[#09422a] dark:bg-emerald-950/40 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span> Informasi Profil
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Perbarui nama lengkap dan alamat email akun Anda.</p>
                    </div>

                    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div class="space-y-1">
                            <label for="name" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @if($errors->has('name'))
                                <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <label for="email" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @if($errors->has('email'))
                                <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->first('email') }}</span>
                            @endif

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-4 p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/20 text-amber-800 dark:text-amber-400 text-xs flex items-start gap-2.5">
                                    <i class="fa-solid fa-circle-exclamation text-amber-600 mt-0.5"></i>
                                    <div>
                                        <span>Email Anda belum diverifikasi.</span>
                                        <button form="send-verification" class="underline hover:text-amber-900 dark:hover:text-amber-300 font-bold block mt-1 text-left">Kirim ulang tautan verifikasi.</button>
                                        @if (session('status') === 'verification-link-sent')
                                            <span class="block mt-1.5 font-bold text-emerald-600 dark:text-emerald-400">Tautan baru telah dikirim ke email Anda.</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md shadow-[#09422a]/10 transition">
                                Simpan Perubahan
                            </button>
                            @if (session('status') === 'profile-updated')
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 transition" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Profil berhasil diperbarui.
                                </p>
                            @endif
                        </div>
                    </form>

                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                </div>

                <!-- Update Password Card -->
                <div id="update-password" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-150 text-[#09422a] dark:bg-emerald-950/40 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </span> Perbarui Kata Sandi
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.</p>
                    </div>

                    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                        @csrf
                        @method('put')

                        <div class="space-y-1">
                            <label for="current_password" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kata Sandi Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @if($errors->updatePassword->has('current_password'))
                                <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->updatePassword->first('current_password') }}</span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <label for="password" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kata Sandi Baru</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @if($errors->updatePassword->has('password'))
                                <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->updatePassword->first('password') }}</span>
                            @endif
                        </div>

                        <div class="space-y-1">
                            <label for="password_confirmation" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            @if($errors->updatePassword->has('password_confirmation'))
                                <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->updatePassword->first('password_confirmation') }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md shadow-[#09422a]/10 transition">
                                Simpan Sandi
                            </button>
                            @if (session('status') === 'password-updated')
                                <p class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 transition" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                                    <i class="fa-solid fa-circle-check mr-1"></i> Kata sandi berhasil diperbarui.
                                </p>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Danger Zone Card -->
                <div id="delete-account" class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-850 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6 scroll-mt-24" x-data="{ open: @json($errors->userDeletion->isNotEmpty()) }">
                    <div>
                        <h2 class="text-lg font-bold text-rose-600 dark:text-rose-450 flex items-center">
                            <span class="p-1.5 rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 mr-2.5">
                                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                            </span> Hapus Akun
                        </h2>
                        <p class="text-xs text-slate-400 mt-1">Hapus akun secara permanen beserta semua data terkait.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-rose-50/50 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun Anda, harap unduh data atau informasi apa pun yang ingin Anda pertahankan.
                    </div>

                    <div class="pt-2">
                        <button type="button" @click="open = true" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/10 transition">
                            Hapus Akun Saya
                        </button>
                    </div>

                    <!-- Alpine.js Confirmation Modal -->
                    <div x-show="open" 
                         class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0" 
                         style="display: none;">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 transform transition-all" @click="open = false" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
                        </div>

                        <!-- Modal Content -->
                        <div class="bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-xl transform transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6 z-10" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <form method="post" action="{{ route('profile.destroy') }}">
                                @csrf
                                @method('delete')

                                <div class="space-y-2">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                        Apakah Anda yakin ingin menghapus akun Anda?
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                                        Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                                    </p>
                                </div>

                                <div class="mt-5 space-y-1">
                                    <label for="delete_password" class="sr-only">Kata Sandi</label>
                                    <input type="password" id="delete_password" name="password" placeholder="Kata Sandi Anda" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-rose-500 focus:ring-rose-500">
                                    @if($errors->userDeletion->has('password'))
                                        <span class="text-xs text-rose-500 block mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->userDeletion->first('password') }}</span>
                                    @endif
                                </div>

                                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <button type="button" @click="open = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-650 bg-slate-100 hover:bg-slate-200 transition">
                                        Batal
                                    </button>
                                    <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/10 transition">
                                        Hapus Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
