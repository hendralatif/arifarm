<x-guest-layout>
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-900">Selamat Datang!</h2>
        <p class="text-slate-500 mt-2 font-medium">Masuk ke panel manajemen ARI FARM</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 flex items-center p-4 text-sm text-emerald-700 border border-emerald-200 rounded-2xl bg-emerald-50" role="alert">
            <i class="fa-solid fa-circle-check mr-2.5 text-emerald-600"></i>
            <span class="font-semibold">{{ session('status') }}</span>
        </div>
    @endif

    <!-- Error display -->
    @if ($errors->any())
        <div class="mb-6 flex items-start p-4 text-sm text-red-700 border border-red-200 rounded-2xl bg-red-50" role="alert">
            <i class="fa-solid fa-triangle-exclamation mr-2.5 text-red-500 mt-0.5 shrink-0"></i>
            <div>
                <span class="font-bold block mb-1">Login gagal:</span>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li class="font-medium">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Field -->
        <div class="space-y-1.5">
            <label for="email" class="block text-sm font-bold text-slate-700">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fa-solid fa-envelope text-slate-400 text-sm"></i>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="admin@arifarm.com"
                    class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-800 font-medium text-sm focus:outline-none focus:border-[#09422a] focus:ring-2 focus:ring-[#09422a]/10 transition placeholder:text-slate-350 @error('email') border-red-400 focus:border-red-400 focus:ring-red-400/10 @enderror"
                >
            </div>
        </div>

        <!-- Password Field -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-bold text-slate-700">
                    Kata Sandi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#09422a] hover:underline">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                    class="w-full pl-11 pr-12 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-800 font-medium text-sm focus:outline-none focus:border-[#09422a] focus:ring-2 focus:ring-[#09422a]/10 transition placeholder:text-slate-350 @error('password') border-red-400 focus:border-red-400 focus:ring-red-400/10 @enderror"
                >
                <!-- Toggle password visibility -->
                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-350 hover:text-slate-600 transition">
                    <i id="password-eye" class="fa-solid fa-eye text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="rounded-lg border-slate-300 text-[#09422a] focus:ring-[#09422a]/20 cursor-pointer"
            >
            <label for="remember_me" class="ml-2.5 text-sm font-semibold text-slate-600 cursor-pointer select-none">
                Ingat saya di perangkat ini
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            id="login-submit-btn"
            class="w-full py-4 px-6 bg-[#09422a] hover:bg-[#083a25] text-white font-black text-sm rounded-2xl shadow-lg shadow-[#09422a]/20 hover:shadow-[#09422a]/30 transition-all duration-200 active:scale-[0.98] flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-right-to-bracket"></i>
            Masuk ke Dashboard
        </button>

        <!-- Divider -->
        <div class="relative flex items-center">
            <div class="flex-1 border-t border-slate-200"></div>
            <span class="px-4 text-xs text-slate-400 font-semibold">atau</span>
            <div class="flex-1 border-t border-slate-200"></div>
        </div>

        <!-- Register Link -->
        <p class="text-center text-sm text-slate-500 font-medium">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-[#09422a] font-bold hover:underline ml-1">
                Daftar Sekarang
            </a>
        </p>
    </form>

    <!-- Hint credentials -->
    <div class="mt-8 p-4 bg-slate-100 rounded-2xl border border-slate-200/80">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
            <i class="fa-solid fa-circle-info mr-1.5 text-slate-400"></i> Demo Kredensial
        </p>
        <div class="space-y-1">
            <p class="text-xs font-mono text-slate-600">
                <span class="font-bold text-[#09422a]">Admin:</span> admin@arifarm.com / password
            </p>
            <p class="text-xs font-mono text-slate-600">
                <span class="font-bold text-slate-500">User:</span> user@arifarm.com / password
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eye = document.getElementById('password-eye');
            if (input.type === 'password') {
                input.type = 'text';
                eye.className = 'fa-solid fa-eye-slash text-sm';
            } else {
                input.type = 'password';
                eye.className = 'fa-solid fa-eye text-sm';
            }
        }
    </script>
</x-guest-layout>
