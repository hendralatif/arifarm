<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - Ari Farm</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Heroicons / FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" defer></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200">

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="hidden md:flex flex-col w-64 bg-white text-slate-600 flex-shrink-0 border-r border-slate-200/80 dark:bg-slate-900 dark:border-slate-850">
            <!-- Branding / Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-100 dark:border-slate-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <span class="p-2.5 rounded-xl bg-[#09422a] text-white flex items-center justify-center w-10 h-10 shadow-md">
                        <i class="fa-solid fa-cow text-lg"></i>
                    </span>
                    <span class="font-extrabold text-base tracking-wider text-[#09422a] dark:text-emerald-400 font-display uppercase">
                        ARI <span class="text-slate-700 dark:text-slate-300">FARM</span>
                    </span>
                </a>
            </div>
            
            <div class="flex-1 flex flex-col justify-between py-6 px-4">
                <!-- Navigation -->
                <nav class="space-y-1.5" x-data="{ openKambing: {{ Route::is('admin.goats.index') || Route::is('admin.goats.show') || Route::is('admin.goats.edit') || Route::is('admin.births*') ? 'true' : 'false' }}, openKesehatan: {{ Route::is('admin.health*') || Route::is('admin.weighings*') ? 'true' : 'false' }} }">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.dashboard') ? 'bg-[#09422a] text-white hover:bg-[#083a25] hover:text-white shadow-md shadow-emerald-900/10' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-chart-pie w-5 mr-3 text-lg"></i> Dashboard
                    </a>

                    <!-- Kelola Kambing (Dropdown) -->
                    <div>
                        <button @click="openKambing = !openKambing" class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 text-slate-600 dark:text-slate-400">
                            <i class="fa-solid fa-cow w-5 mr-3 text-lg"></i> 
                            <span>Kelola Kambing</span>
                            <i class="fa-solid text-xs ml-auto transition-transform duration-200" :class="openKambing ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        
                        <div x-show="openKambing" x-transition:enter="transition-all ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-24" x-transition:leave="transition-all ease-in duration-150" x-transition:leave-start="opacity-100 max-h-24" x-transition:leave-end="opacity-0 max-h-0" class="mt-1.5 pl-9 space-y-1 overflow-hidden">
                            <!-- Data Stok -->
                            <a href="{{ route('admin.goats.index') }}" class="block px-4 py-2.5 text-xs font-bold rounded-xl transition {{ Route::is('admin.goats.index') || Route::is('admin.goats.show') || Route::is('admin.goats.edit') ? 'bg-[#09422a] text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                Data Stok
                            </a>
                            <!-- Data Kelahiran -->
                            <a href="{{ route('admin.births.index') }}" class="block px-4 py-2.5 text-xs font-bold rounded-xl transition {{ Route::is('admin.births*') ? 'bg-[#09422a] text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                Data Kelahiran
                            </a>
                        </div>
                    </div>

                    <!-- Pakan -->
                    <a href="{{ route('admin.feedings.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.feedings*') ? 'bg-[#09422a] text-white hover:bg-[#083a25] hover:text-white shadow-md shadow-emerald-900/10' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-wheat-awn w-5 mr-3 text-lg"></i> Pakan
                    </a>

                    <!-- Kesehatan (Dropdown) -->
                    <div>
                        <button @click="openKesehatan = !openKesehatan" class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 text-slate-600 dark:text-slate-400">
                            <i class="fa-solid fa-stethoscope w-5 mr-3 text-lg"></i> 
                            <span>Kesehatan</span>
                            <i class="fa-solid text-xs ml-auto transition-transform duration-200" :class="openKesehatan ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </button>
                        
                        <div x-show="openKesehatan" x-transition:enter="transition-all ease-out duration-200" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-24" x-transition:leave="transition-all ease-in duration-150" x-transition:leave-start="opacity-100 max-h-24" x-transition:leave-end="opacity-0 max-h-0" class="mt-1.5 pl-9 space-y-1 overflow-hidden">
                            <!-- Rekam Medis -->
                            <a href="{{ route('admin.health.index') }}" class="block px-4 py-2.5 text-xs font-bold rounded-xl transition {{ Route::is('admin.health*') ? 'bg-[#09422a] text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                Rekam Medis
                            </a>
                            <!-- Penimbangan Bobot -->
                            <a href="{{ route('admin.weighings.index') }}" class="block px-4 py-2.5 text-xs font-bold rounded-xl transition {{ Route::is('admin.weighings*') ? 'bg-[#09422a] text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' }}">
                                Penimbangan Bobot
                            </a>
                        </div>
                    </div>

                    <!-- Transaksi -->
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.orders*') || Route::is('admin.expenses*') ? 'bg-[#09422a] text-white hover:bg-[#083a25] hover:text-white shadow-md shadow-emerald-900/10' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-receipt w-5 mr-3 text-lg"></i> Transaksi
                        @php
                            $pendingVerify = \App\Models\Order::where('status', 'pending_verification')->count();
                        @endphp
                        @if($pendingVerify > 0)
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500 text-slate-950">
                                {{ $pendingVerify }}
                            </span>
                        @endif
                    </a>

                    <!-- Pelanggan -->
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.users*') ? 'bg-[#09422a] text-white hover:bg-[#083a25] hover:text-white shadow-md shadow-emerald-900/10' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-users w-5 mr-3 text-lg"></i> Pelanggan
                    </a>

                    <!-- Pembayaran -->
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.payments*') ? 'bg-[#09422a] text-white hover:bg-[#083a25]' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-credit-card w-5 mr-3 text-lg"></i> Pembayaran
                        @php
                            $pendingVerifyPay = \App\Models\Order::where('status', 'pending_verification')->count();
                        @endphp
                        @if($pendingVerifyPay > 0)
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-650 text-white">
                                {{ $pendingVerifyPay }}
                            </span>
                        @endif
                    </a>

                    <!-- Laporan -->
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.reports*') ? 'bg-[#09422a] text-white hover:bg-[#083a25]' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-clipboard-list w-5 mr-3 text-lg"></i> Laporan
                    </a>

                    <!-- Chat Pelanggan -->
                    <a href="{{ route('admin.chats.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 {{ Route::is('admin.chats*') ? 'bg-[#09422a] text-white hover:bg-[#083a25] hover:text-white shadow-md shadow-emerald-900/10' : 'text-slate-600 dark:text-slate-400' }}">
                        <i class="fa-solid fa-comments w-5 mr-3 text-lg"></i> Chat Pelanggan
                        @php
                            $unreadMessages = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadMessages > 0)
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white">
                                {{ $unreadMessages }}
                            </span>
                        @endif
                    </a>
                </nav>

                <!-- Actions / Logout -->
                <div class="space-y-3 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-450 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white transition">
                        <i class="fa-solid fa-arrow-left w-5 mr-3"></i> Kembali Ke Toko
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-bold uppercase tracking-wider text-red-650 hover:text-red-750 dark:text-red-400 dark:hover:text-red-300 transition rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/20">
                            <i class="fa-solid fa-sign-out w-5 mr-3 text-lg"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">
            <!-- Navbar Header -->
            <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-8 z-10">
                <button id="mobile-sidebar-btn" class="md:hidden p-2 rounded-lg text-slate-650 dark:text-slate-350 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="hidden md:block">
                    <h2 class="text-sm font-bold text-slate-450 uppercase tracking-wider">
                        Selamat Datang, {{ Auth::user()->name }} (Administrator)
                    </h2>
                </div>
                
                <div class="flex items-center space-x-4">
                    @php
                        $unreadChatCount = \App\Models\Message::where('receiver_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    <a href="{{ route('admin.chats.index') }}" class="relative p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:text-[#09422a] hover:bg-slate-100 transition-colors" title="Pesan Masuk">
                        <i class="fa-solid fa-bell text-lg"></i>
                        <span id="bell-unread-badge" class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-900 {{ $unreadChatCount > 0 ? '' : 'hidden' }}">
                            {{ $unreadChatCount }}
                        </span>
                    </a>

                    <span class="text-xs px-3.5 py-1.5 font-bold rounded-xl bg-emerald-50 text-[#09422a] border border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-450 dark:border-emerald-900">
                        <i class="fa-solid fa-user-shield mr-1.5"></i> Admin Panel
                    </span>
                </div>
            </header>

            <!-- Notification Alerts inside Admin -->
            <div class="px-8 mt-6">
                @if(session('success'))
                    <div class="flex items-center p-4 mb-4 text-sm text-[#09422a] border border-emerald-200 rounded-2xl bg-emerald-50 dark:bg-slate-900 dark:text-emerald-400 dark:border-emerald-900" role="alert">
                        <i class="fa-solid fa-circle-check text-lg mr-2.5 flex-shrink-0"></i>
                        <span class="font-bold">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-200 rounded-2xl bg-red-50 dark:bg-slate-900 dark:text-red-400 dark:border-red-900" role="alert">
                        <i class="fa-solid fa-triangle-exclamation text-lg mr-2.5 flex-shrink-0"></i>
                        <span class="font-bold">{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="flex items-center p-4 mb-4 text-sm text-amber-800 border border-amber-200 rounded-2xl bg-amber-50 dark:bg-slate-900 dark:text-amber-400 dark:border-amber-900" role="alert">
                        <i class="fa-solid fa-circle-info text-lg mr-2.5 flex-shrink-0"></i>
                        <span class="font-bold">{{ session('warning') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <main class="flex-1 py-8 px-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Drawer Sidebar (Dynamic overlay) -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 flex md:hidden hidden">
        <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        
        <div class="relative flex flex-col w-72 max-w-xs bg-white dark:bg-slate-900 py-6 px-4 border-r border-slate-200 dark:border-slate-800">
            <div class="flex items-center justify-between mb-8 px-2">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="p-2 rounded-lg bg-[#09422a] text-white">
                        <i class="fa-solid fa-cow text-md"></i>
                    </span>
                    <span class="font-bold text-lg tracking-tight text-[#09422a] dark:text-emerald-400">
                        ARI <span class="text-slate-850 dark:text-slate-200">FARM</span>
                    </span>
                </a>
                <button id="mobile-sidebar-close" class="p-1 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-white">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            
            <nav class="space-y-1.5 flex-1" x-data="{ openKambing: {{ Route::is('admin.goats.index') || Route::is('admin.goats.show') || Route::is('admin.goats.edit') || Route::is('admin.births*') ? 'true' : 'false' }}, openKesehatan: {{ Route::is('admin.health*') || Route::is('admin.weighings*') ? 'true' : 'false' }} }">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.dashboard') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-chart-pie w-5 mr-3 text-lg"></i> Dashboard
                </a>
                
                <div>
                    <button @click="openKambing = !openKambing" class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 text-slate-600">
                        <i class="fa-solid fa-cow w-5 mr-3 text-lg"></i> 
                        <span>Kelola Kambing</span>
                        <i class="fa-solid text-xs ml-auto transition-transform duration-200" :class="openKambing ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </button>
                    
                    <div x-show="openKambing" x-collapse x-cloak class="mt-1.5 pl-9 space-y-1">
                        <a href="{{ route('admin.goats.index') }}" class="block px-4 py-2 text-xs font-bold rounded-xl transition {{ Route::is('admin.goats.index') || Route::is('admin.goats.show') || Route::is('admin.goats.edit') ? 'bg-[#09422a] text-white' : 'text-slate-500 hover:text-slate-900' }}">
                            Data Stok
                        </a>
                        <a href="{{ route('admin.births.index') }}" class="block px-4 py-2 text-xs font-bold rounded-xl transition {{ Route::is('admin.births*') ? 'bg-[#09422a] text-white' : 'text-slate-550 hover:text-slate-900' }}">
                            Data Kelahiran
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.feedings.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.feedings*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-wheat-awn w-5 mr-3 text-lg"></i> Pakan
                </a>

                <div>
                    <button @click="openKesehatan = !openKesehatan" class="w-full flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 text-slate-600">
                        <i class="fa-solid fa-stethoscope w-5 mr-3 text-lg"></i> 
                        <span>Kesehatan</span>
                        <i class="fa-solid text-xs ml-auto transition-transform duration-200" :class="openKesehatan ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                    </button>
                    
                    <div x-show="openKesehatan" x-collapse x-cloak class="mt-1.5 pl-9 space-y-1">
                        <a href="{{ route('admin.health.index') }}" class="block px-4 py-2 text-xs font-bold rounded-xl transition {{ Route::is('admin.health*') ? 'bg-[#09422a] text-white' : 'text-slate-550 hover:text-slate-900' }}">
                            Rekam Medis
                        </a>
                        <a href="{{ route('admin.weighings.index') }}" class="block px-4 py-2 text-xs font-bold rounded-xl transition {{ Route::is('admin.weighings*') ? 'bg-[#09422a] text-white' : 'text-slate-550 hover:text-slate-900' }}">
                            Penimbangan Bobot
                        </a>
                    </div>
                </div>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.orders*') || Route::is('admin.expenses*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-receipt w-5 mr-3 text-lg"></i> Transaksi
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.users*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-users w-5 mr-3 text-lg"></i> Pelanggan
                </a>

                <a href="{{ route('admin.payments.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.payments*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-credit-card w-5 mr-3 text-lg"></i> Pembayaran
                </a>

                <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.reports*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-clipboard-list w-5 mr-3 text-lg"></i> Laporan
                </a>

                <a href="{{ route('admin.chats.index') }}" class="flex items-center px-4 py-3 text-sm font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition duration-150 {{ Route::is('admin.chats*') ? 'bg-[#09422a] text-white' : 'text-slate-600' }}">
                    <i class="fa-solid fa-comments w-5 mr-3 text-lg"></i> Chat Pelanggan
                    @if(isset($unreadMessages) && $unreadMessages > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500 text-white">
                            {{ $unreadMessages }}
                        </span>
                    @endif
                </a>
            </nav>

            <div class="space-y-2 border-t border-slate-100 dark:border-slate-800 pt-4 mt-4">
                <a href="{{ route('home') }}" class="flex items-center px-4 py-2 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-800 transition">
                    <i class="fa-solid fa-arrow-left w-5 mr-3"></i> Kembali Ke Toko
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-bold uppercase tracking-wider text-red-650 hover:text-red-750 transition rounded-xl">
                        <i class="fa-solid fa-sign-out w-5 mr-3 text-lg"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toggle sidebar script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('mobile-sidebar-btn');
            const closeBtn = document.getElementById('mobile-sidebar-close');
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');

            function toggleSidebar() {
                if (sidebar) sidebar.classList.toggle('hidden');
            }

            if (openBtn) openBtn.addEventListener('click', toggleSidebar);
            if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
            if (backdrop) backdrop.addEventListener('click', toggleSidebar);
        });
    </script>

    <!-- Real-time notifications polling script -->
    <script>
        function pollUnreadNotificationCount() {
            fetch('{{ route('chats.unread-count') }}')
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('bell-unread-badge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.innerText = data.count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    }
                })
                .catch(err => console.error(err));
        }
        // Poll for notifications every 5 seconds
        setInterval(pollUnreadNotificationCount, 5000);
        document.addEventListener('DOMContentLoaded', pollUnreadNotificationCount);
    </script>
</body>
</html>
