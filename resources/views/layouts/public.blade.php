<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ARI FARM - Pusat Penjualan Kambing & Domba Premium')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Heroicons / FontAwesome for ease -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" defer></script>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 w-full bg-white/95 backdrop-blur-md border-b border-slate-200/70 shadow-sm dark:bg-slate-950/95 dark:border-slate-800 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                        <span class="p-2.5 rounded-xl bg-[#09422a] text-white shadow-md shadow-[#09422a]/20">
                            <i class="fa-solid fa-cow text-xl"></i>
                        </span>
                        <span class="font-black text-xl tracking-tight">
                            <span class="text-[#09422a] dark:text-emerald-400">ARI</span>
                            <span class="text-slate-700 dark:text-slate-200"> FARM</span>
                        </span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ Route::is('home') ? 'bg-[#09422a]/8 text-[#09422a] dark:text-emerald-400' : 'text-slate-600 hover:text-[#09422a] hover:bg-slate-50 dark:text-slate-300 dark:hover:text-emerald-400' }}">Beranda</a>
                    <a href="{{ route('catalog') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ Route::is('catalog*') ? 'bg-[#09422a]/8 text-[#09422a] dark:text-emerald-400' : 'text-slate-600 hover:text-[#09422a] hover:bg-slate-50 dark:text-slate-300 dark:hover:text-emerald-400' }}">Katalog Kambing</a>
                    <a href="{{ route('pembayaran') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ Route::is('pembayaran') ? 'bg-[#09422a]/8 text-[#09422a] dark:text-emerald-400' : 'text-slate-600 hover:text-[#09422a] hover:bg-slate-50 dark:text-slate-300 dark:hover:text-emerald-400' }}">Pembayaran</a>
                    <a href="{{ route('histori') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors {{ Route::is('histori') ? 'bg-[#09422a]/8 text-[#09422a] dark:text-emerald-400' : 'text-slate-600 hover:text-[#09422a] hover:bg-slate-50 dark:text-slate-300 dark:hover:text-emerald-400' }}">Histori</a>
                    <button onclick="openChatWithPrefilledMessage('Halo ARI FARM, saya ingin konsultasi mengenai hewan ternak.')" class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 hover:text-[#09422a] hover:bg-slate-50 dark:text-slate-300 dark:hover:text-emerald-400 transition-colors text-left">Tanya Ahli</button>
                </nav>

                <!-- Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Shopping Cart -->
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:text-[#09422a] hover:bg-slate-50 dark:hover:text-emerald-400 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700" title="Keranjang Belanja">
                        <i class="fa-solid fa-shopping-basket text-lg"></i>
                        @php
                            $cartCount = count(session()->get('cart', []));
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-950">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Notification Bell (Admin Only) -->
                    @auth
                        @if(Auth::user()->isAdmin())
                            @php
                                $unreadChatCount = \App\Models\Message::where('receiver_id', Auth::id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            <a href="{{ route('admin.chats.index') }}" class="relative p-2.5 rounded-xl text-slate-650 dark:text-slate-300 hover:text-[#09422a] hover:bg-slate-50 dark:hover:text-emerald-455 dark:hover:bg-slate-800 transition-colors border border-transparent hover:border-slate-200 dark:hover:border-slate-700" title="Pesan Masuk">
                                <i class="fa-solid fa-bell text-lg"></i>
                                <span id="bell-unread-badge" class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-950 {{ $unreadChatCount > 0 ? '' : 'hidden' }}">
                                    {{ $unreadChatCount }}
                                </span>
                            </a>
                        @endif
                    @endauth

                    <!-- Auth Actions -->
                    @auth
                        <div class="hidden sm:flex items-center space-x-2">
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-[#09422a] bg-emerald-50 dark:bg-emerald-950/30 dark:text-emerald-400 rounded-xl hover:bg-emerald-100 border border-emerald-100 dark:border-emerald-900 transition duration-150">
                                    <i class="fa-solid fa-gauge mr-1.5"></i> Admin
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-bold text-white bg-[#09422a] hover:bg-[#083a25] rounded-xl shadow-md shadow-[#09422a]/20 transition duration-150">
                                <i class="fa-solid fa-user-circle mr-1.5"></i> Akun Saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3.5 py-2 text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/20 dark:text-rose-400 rounded-xl transition duration-150" title="Keluar">
                                    <i class="fa-solid fa-sign-out"></i> Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="hidden sm:flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-[#09422a] transition duration-150">Masuk</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold text-white bg-[#09422a] hover:bg-[#083a25] rounded-xl shadow-md shadow-[#09422a]/20 transition duration-150">Daftar</a>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700 transition">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Drawer) -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 py-4 px-6 space-y-3">
            <a href="{{ route('home') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600">Beranda</a>
            <a href="{{ route('catalog') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600">Katalog Kambing</a>
            <a href="{{ route('pembayaran') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600">Pembayaran</a>
            <a href="{{ route('histori') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600">Histori</a>
            <button onclick="openChatWithPrefilledMessage('Halo ARI FARM, saya ingin konsultasi mengenai hewan ternak.'); document.getElementById('mobile-menu').classList.add('hidden');" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600 text-left">
                <i class="fa-solid fa-comments mr-2"></i> Tanya Ahli
            </button>
            <hr class="border-slate-200 dark:border-slate-800 my-2">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block text-base font-semibold text-emerald-600 hover:text-emerald-700">
                        <i class="fa-solid fa-gauge mr-2"></i> Admin Panel
                    </a>
                @endif
                <a href="{{ route('dashboard') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300 hover:text-emerald-600">
                    <i class="fa-solid fa-user-circle mr-2"></i> Akun Saya
                </a>
                <form method="POST" action="{{ route('logout') }}" class="block pt-2">
                    @csrf
                    <button type="submit" class="w-full text-left text-base font-semibold text-rose-600 hover:text-rose-700">
                        <i class="fa-solid fa-sign-out mr-2"></i> Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block text-base font-semibold text-slate-700 dark:text-slate-300">Masuk</a>
                <a href="{{ route('register') }}" class="block text-emerald-600 text-base font-semibold">Daftar</a>
            @endauth
        </div>
    </header>

    <!-- Notifications Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-emerald-800 border border-emerald-300 rounded-xl bg-emerald-50 dark:bg-slate-900 dark:text-emerald-400 dark:border-emerald-800" role="alert">
                <i class="fa-solid fa-circle-check text-lg mr-2 flex-shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-300 rounded-xl bg-red-50 dark:bg-slate-900 dark:text-red-400 dark:border-red-800" role="alert">
                <i class="fa-solid fa-triangle-exclamation text-lg mr-2 flex-shrink-0"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center p-4 mb-4 text-sm text-amber-800 border border-amber-300 rounded-xl bg-amber-50 dark:bg-slate-900 dark:text-amber-400 dark:border-amber-800" role="alert">
                <i class="fa-solid fa-circle-info text-lg mr-2 flex-shrink-0"></i>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="min-h-[70vh]">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#09422a] text-emerald-100 pt-16 pb-8 border-t border-[#083a25]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand Column -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2.5">
                    <span class="p-2 rounded-xl bg-white/10 text-white border border-white/20">
                        <i class="fa-solid fa-cow text-lg"></i>
                    </span>
                    <span class="font-black text-xl tracking-tight text-white">
                        ARI <span class="text-emerald-300">FARM</span>
                    </span>
                </div>
                <p class="text-sm text-emerald-200/70 leading-relaxed">
                    Penyedia kambing qurban, aqiqah, dan bibit ternak berkualitas tinggi di Indonesia. Transaksi mudah, aman, dan amanah secara syar'i.
                </p>
                <div class="flex space-x-3">
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/10"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/10"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/10"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-white transition border border-white/10"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Categories Links -->
            <div>
                <h4 class="font-black text-white mb-4 uppercase tracking-wider text-xs">Kategori Produk</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('catalog') }}?category=kambing-etawa" class="text-emerald-200/70 hover:text-white transition">Kambing Etawa</a></li>
                    <li><a href="{{ route('catalog') }}?category=kambing-boer" class="text-emerald-200/70 hover:text-white transition">Kambing Boer</a></li>
                    <li><a href="{{ route('catalog') }}?category=kambing-kacang" class="text-emerald-200/70 hover:text-white transition">Kambing Kacang</a></li>
                    <li><a href="{{ route('catalog') }}?category=domba-texel" class="text-emerald-200/70 hover:text-white transition">Domba Texel</a></li>
                    <li><a href="{{ route('catalog') }}?category=domba-dombos" class="text-emerald-200/70 hover:text-white transition">Domba Dombos</a></li>
                    <li><a href="{{ route('pembayaran') }}" class="text-emerald-200/70 hover:text-white transition">Pembayaran</a></li>
                    <li><a href="{{ route('histori') }}" class="text-emerald-200/70 hover:text-white transition">Histori Pesanan</a></li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-black text-white mb-4 uppercase tracking-wider text-xs">Navigasi</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="text-emerald-200/70 hover:text-white transition">Beranda</a></li>
                    <li><a href="{{ route('catalog') }}" class="text-emerald-200/70 hover:text-white transition">Katalog Hewan</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-emerald-200/70 hover:text-white transition">Keranjang Belanja</a></li>
                    <li><a href="{{ route('login') }}" class="text-emerald-200/70 hover:text-white transition">Masuk Akun</a></li>
                    <li><a href="javascript:void(0)" onclick="openChatWithPrefilledMessage('Halo ARI FARM, saya ingin bertanya.')" class="text-emerald-200/70 hover:text-white transition">Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Contact/Address -->
            <div class="space-y-3.5 text-sm">
                <h4 class="font-black text-white uppercase tracking-wider text-xs">Kontak & Lokasi</h4>
                <div class="flex items-start space-x-3 text-emerald-200/70">
                    <i class="fa-solid fa-map-marker-alt mt-0.5 text-emerald-400 shrink-0"></i>
                    <span class="leading-relaxed">Kaliputih, Selomerto, Wonosobo, Jawa Tengah </span>
                </div>
                <div class="flex items-center space-x-3 text-emerald-200/70">
                    <i class="fa-solid fa-phone text-emerald-400 shrink-0"></i>
                    <span>+62 888-2595-663</span>
                </div>
                <div class="flex items-center space-x-3 text-emerald-200/70">
                    <i class="fa-solid fa-envelope text-emerald-400 shrink-0"></i>
                    <span>mroemar190@gmail.com</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-white/10 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-emerald-200/40">
            <p>&copy; {{ date('Y') }} ARI FARM. All Rights Reserved. Dibuat dengan Laravel & dedikasi.</p>
            <div class="flex space-x-4 mt-4 sm:mt-0">
                <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-white transition">Panduan Syar'i</a>
            </div>
        </div>
    </footer>

    <!-- Floating Live Chat CSS -->
    <style>
        #live-chat-box {
            width: 380px;
            height: 520px;
            max-width: 95vw;
            max-height: 85vh;
            transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1), height 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s ease, opacity 0.3s ease;
        }
        #live-chat-box.chat-maximized {
            width: 50vw;
            height: 85vh;
            max-height: 750px;
        }
        @media (max-width: 1024px) {
            #live-chat-box.chat-maximized {
                width: 70vw;
            }
        }
        @media (max-width: 768px) {
            #live-chat-box {
                width: 340px;
                height: 480px;
            }
            #live-chat-box.chat-maximized {
                width: 95vw;
                height: 80vh;
            }
        }
    </style>

    <!-- Floating Live Chat Action -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end pointer-events-none">
        <!-- Chat Box Window -->
        <div id="live-chat-box" class="mb-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-2xl flex flex-col overflow-hidden transition-all duration-300 transform translate-y-4 opacity-0 pointer-events-none pointer-events-auto">
            @auth
                <!-- ================= CHAT HEADER (UNIFIED) ================= -->
                <div class="bg-[#09422a] p-4 flex items-center justify-between text-white" id="chat-box-header">
                    <div class="flex items-center space-x-3">
                        <!-- Back button: shown only inside active conversation -->
                        <button onclick="backToConversationsList()" class="hidden mr-1 text-emerald-100 hover:text-white transition" id="chat-back-btn">
                            <i class="fa-solid fa-arrow-left text-base"></i>
                        </button>
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-lg" id="chat-header-avatar">C</div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-450 border-2 border-white rounded-full" id="chat-header-online-dot"></span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-tight" id="chat-header-name">Obrolan ARI FARM</h4>
                            <p class="text-[10px] text-emerald-200/90 font-medium" id="chat-header-status">Daftar Obrolan</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="toggleChatSize()" class="text-emerald-150 hover:text-white transition" title="Perbesar / Perkecil Chat">
                            <i class="fa-solid fa-expand text-base" id="chat-expand-icon"></i>
                        </button>
                        <button onclick="toggleLiveChat()" class="text-emerald-150 hover:text-white transition" title="Tutup Chat">
                            <i class="fa-solid fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- ================= LIST CHAT VIEW (CONVERSATIONS LIST) ================= -->
                <div id="chat-conversations-view" class="flex-grow flex flex-col overflow-hidden">
                    <!-- Search Bar -->
                    <div class="p-3 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
                        <div class="relative">
                            <input type="text" id="chat-list-search" placeholder="Cari obrolan..." class="w-full pl-8 pr-4 py-1.5 text-xs rounded-xl border border-slate-200 dark:border-slate-850 bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-200">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>
                    <!-- Conversations List container -->
                    <div id="chat-conversations-list" class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-slate-850">
                        <div class="p-6 text-center text-slate-450 space-y-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-lg text-[#09422a]"></i>
                            <p class="text-xs">Memuat obrolan...</p>
                        </div>
                    </div>
                </div>

                <!-- ================= ACTIVE CONVERSATION VIEW ================= -->
                <div id="chat-messages-view" class="flex-grow flex flex-col overflow-hidden hidden">
                    <!-- Message stream -->
                    <div id="chat-messages-container" class="flex-grow p-4 overflow-y-auto space-y-3 bg-slate-50 dark:bg-slate-950 flex flex-col"></div>
                    <!-- Chat input footer -->
                    <div class="p-3 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                        <form id="chat-message-form" onsubmit="sendChatMessage(event)" class="flex items-center gap-2">
                            <input type="text" id="chat-message-input" placeholder="Tulis pesan..." class="flex-1 px-4 py-2 text-xs rounded-xl border border-slate-250 dark:border-slate-855 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-200" required autocomplete="off">
                            <button type="submit" class="h-9 w-9 flex items-center justify-center rounded-xl bg-[#09422a] text-white hover:bg-[#073321] transition shrink-0 shadow-md shadow-[#09422a]/10">
                                <i class="fa-solid fa-paper-plane text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- ================= GUEST LOGIN PROMPT ================= --}}
                <!-- Header -->
                <div class="bg-[#09422a] p-4 flex items-center justify-between text-white">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-lg">A</div>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold tracking-tight">Admin ARI FARM</h4>
                            <p class="text-[10px] text-emerald-200/90 font-medium">Online (Respon Cepat)</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="toggleChatSize()" class="text-emerald-150 hover:text-white transition">
                            <i class="fa-solid fa-expand text-base" id="chat-expand-icon"></i>
                        </button>
                        <button onclick="toggleLiveChat()" class="text-emerald-150 hover:text-white transition">
                            <i class="fa-solid fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <!-- Chat Body -->
                <div class="flex-grow p-4 bg-slate-50 dark:bg-slate-950 flex flex-col justify-center">
                    <div class="flex flex-col items-center justify-center text-center p-6 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-850 flex items-center justify-center text-slate-400 text-2xl">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-700 dark:text-slate-200">Silakan Masuk Untuk Chat</h5>
                            <p class="text-xs text-slate-400 mt-1">Anda harus login terlebih dahulu untuk berkonsultasi langsung dengan admin ARI FARM.</p>
                        </div>
                        <div class="flex gap-2 w-full pt-2">
                            <a href="{{ route('login') }}" class="flex-1 text-center py-2 rounded-xl text-xs font-bold bg-[#09422a] text-white hover:bg-[#073321] transition shadow-md shadow-[#09422a]/10">Masuk</a>
                            <a href="{{ route('register') }}" class="flex-1 text-center py-2 rounded-xl text-xs font-bold bg-white text-[#09422a] border border-[#09422a] hover:bg-slate-50 transition">Daftar</a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>

        <!-- Floating Chat Icon -->
        <button id="live-chat-btn" onclick="toggleLiveChat()" class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-xl hover:bg-emerald-700 transition-all duration-300 hover:scale-110 active:scale-95 group relative pointer-events-auto" title="Chat dengan Admin">
            <i class="fa-solid fa-comments text-2xl group-hover:scale-110 transition duration-300"></i>
            <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-600 border-2 border-white rounded-full hidden" id="chat-notification-badge"></span>
            <span class="absolute right-16 scale-0 bg-emerald-600 text-white text-xs font-semibold py-1.5 px-3 rounded-lg shadow-md transition-all duration-200 origin-right group-hover:scale-100 whitespace-nowrap">
                Live Chat Admin!
            </span>
        </button>
    </div>

    <!-- Live Chat Widget Scripts -->
    <script>
        const isUserAdmin = {{ (Auth::user() && Auth::user()->isAdmin()) ? 'true' : 'false' }};
        let chatOpen = false;
        let pollingInterval = null;
        let activeCustomerId = null;
        let lastMessageCount = 0;
        let conversationsList = [];
        let chatMaximized = false;

        function toggleChatSize() {
            const chatBox = document.getElementById('live-chat-box');
            const expandIcon = document.getElementById('chat-expand-icon');
            
            chatMaximized = !chatMaximized;
            
            if (chatMaximized) {
                chatBox.classList.add('chat-maximized');
                expandIcon.classList.remove('fa-expand');
                expandIcon.classList.add('fa-compress');
            } else {
                chatBox.classList.remove('chat-maximized');
                expandIcon.classList.remove('fa-compress');
                expandIcon.classList.add('fa-expand');
            }
            
            // Scroll to bottom after layout shift
            const messagesContainer = document.getElementById('chat-messages-container');
            if (messagesContainer) {
                setTimeout(() => {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }, 310);
            }
        }

        function toggleLiveChat() {
            const chatBox = document.getElementById('live-chat-box');
            
            chatOpen = !chatOpen;
            
            if (chatOpen) {
                chatBox.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                chatBox.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
                
                @auth
                    startChatPolling();
                @endauth
            } else {
                chatBox.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                chatBox.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
                
                @auth
                    stopChatPolling();
                @endauth
            }
        }

        @auth
        function startChatPolling() {
            loadConversations();
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(() => {
                loadConversations();
                if (activeCustomerId) {
                    loadActiveChatMessages();
                }
            }, 3000);
        }

        function stopChatPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        function loadConversations() {
            if (isUserAdmin) {
                fetch('{{ route('admin.chats.conversations') }}')
                    .then(res => res.json())
                    .then(data => {
                        conversationsList = data;
                        renderConversationsList();
                    })
                    .catch(err => console.error(err));
            } else {
                fetch('{{ route('chat.messages') }}')
                    .then(res => res.json())
                    .then(data => {
                        // For customer, they only have 1 chat room (with Admin)
                        let lastMsg = null;
                        if (Array.isArray(data) && data.length > 0) {
                            const actualMsgs = data.filter(m => m.message);
                            if (actualMsgs.length > 0) {
                                lastMsg = actualMsgs[actualMsgs.length - 1];
                            }
                        }
                        
                        conversationsList = [{
                            id: 'admin',
                            name: 'Admin ARI FARM',
                            email: 'Online (Respon Cepat)',
                            last_message: lastMsg ? lastMsg.message : 'Mulai obrolan baru dengan admin',
                            last_message_time: lastMsg ? lastMsg.time : '',
                            unread_count: 0
                        }];
                        renderConversationsList();
                    })
                    .catch(err => console.error(err));
            }
        }

        function renderConversationsList() {
            const list = document.getElementById('chat-conversations-list');
            if (!list) return;

            const searchQuery = document.getElementById('chat-list-search').value.toLowerCase();
            let html = '';
            let count = 0;

            conversationsList.forEach(c => {
                if (searchQuery && !c.name.toLowerCase().includes(searchQuery)) return;
                count++;

                const initials = c.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
                let badge = '';
                if (c.unread_count > 0) {
                    badge = `<span class="bg-rose-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shrink-0">${c.unread_count}</span>`;
                } else if (c.id === 'admin') {
                    badge = `<span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shrink-0"></span>`;
                }

                const clickAction = c.id === 'admin' 
                    ? `selectCustomerConversation()` 
                    : `selectAdminCustomer(${c.id}, '${c.name}', '${c.email}')`;

                html += `
                    <div onclick="${clickAction}" class="p-3.5 flex items-center justify-between cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-850">
                        <div class="flex items-center space-x-3 min-w-0 flex-grow">
                            <div class="w-10 h-10 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-extrabold text-xs shrink-0">
                                ${initials}
                            </div>
                            <div class="min-w-0 flex-grow">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate pr-2">${c.name}</h4>
                                    <span class="text-[9px] text-slate-400 whitespace-nowrap">${c.last_message_time}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">${c.last_message}</p>
                            </div>
                        </div>
                        <div class="ml-2 shrink-0">
                            ${badge}
                        </div>
                    </div>
                `;
            });

            if (count === 0) {
                list.innerHTML = `
                    <div class="p-6 text-center text-slate-400">
                        <i class="fa-solid fa-message-slash text-xl mb-2 opacity-30"></i>
                        <p class="text-xs">Belum ada obrolan.</p>
                    </div>
                `;
            } else {
                list.innerHTML = html;
            }
        }

        // Set up search filter listener
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('chat-list-search');
            if (search) {
                search.addEventListener('input', renderConversationsList);
            }
        });

        // Open chat window for Customer
        function selectCustomerConversation() {
            activeCustomerId = 'admin';
            lastMessageCount = 0;

            // Toggle screens
            document.getElementById('chat-conversations-view').classList.add('hidden');
            document.getElementById('chat-messages-view').classList.remove('hidden');
            document.getElementById('chat-back-btn').classList.remove('hidden');

            // Header labels
            document.getElementById('chat-header-name').innerText = 'Admin ARI FARM';
            document.getElementById('chat-header-status').innerText = 'Online (Respon Cepat)';
            document.getElementById('chat-header-avatar').innerText = 'A';
            document.getElementById('chat-header-online-dot').classList.remove('hidden');

            loadActiveChatMessages();
        }

        // Open chat window for Admin
        function selectAdminCustomer(customerId, name, email) {
            activeCustomerId = customerId;
            lastMessageCount = 0;

            // Toggle screens
            document.getElementById('chat-conversations-view').classList.add('hidden');
            document.getElementById('chat-messages-view').classList.remove('hidden');
            document.getElementById('chat-back-btn').classList.remove('hidden');

            // Header labels
            document.getElementById('chat-header-name').innerText = name;
            document.getElementById('chat-header-status').innerText = email;
            const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            document.getElementById('chat-header-avatar').innerText = initials;
            document.getElementById('chat-header-online-dot').classList.add('hidden');

            loadActiveChatMessages();
        }

        // Go back to the listing screen
        function backToConversationsList() {
            activeCustomerId = null;
            document.getElementById('chat-conversations-view').classList.remove('hidden');
            document.getElementById('chat-messages-view').classList.add('hidden');
            document.getElementById('chat-back-btn').classList.add('hidden');

            document.getElementById('chat-header-name').innerText = 'Obrolan ARI FARM';
            document.getElementById('chat-header-status').innerText = 'Daftar Obrolan';
            document.getElementById('chat-header-avatar').innerText = 'C';
            document.getElementById('chat-header-online-dot').classList.add('hidden');

            loadConversations();
        }

        function loadActiveChatMessages() {
            if (!activeCustomerId) return;

            const url = isUserAdmin 
                ? `/admin/chats/messages/${activeCustomerId}`
                : '{{ route('chat.messages') }}';

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('chat-messages-container');
                    if (!container) return;

                    if (Array.isArray(data)) {
                        let html = '';
                        let currentDate = '';

                        data.forEach(msg => {
                            if (msg.date !== currentDate) {
                                currentDate = msg.date;
                                html += `<div class="text-center my-3"><span class="bg-slate-250 text-slate-650 dark:bg-slate-800 dark:text-slate-400 text-[10px] px-2.5 py-1 rounded-full font-bold">${msg.date}</span></div>`;
                            }

                            if (msg.is_sender) {
                                html += `
                                    <div class="flex justify-end items-end space-x-1 max-w-[85%] self-end ml-auto">
                                        <div class="bg-[#09422a] text-white text-xs px-3.5 py-2 rounded-2xl rounded-tr-none shadow-sm font-medium">
                                            <p>${msg.message}</p>
                                            <span class="block text-[8px] text-emerald-200/80 text-right mt-1 font-mono">${msg.time}</span>
                                        </div>
                                    </div>
                                `;
                            } else {
                                const avatarInit = isUserAdmin ? document.getElementById('chat-header-avatar').innerText : 'A';
                                html += `
                                    <div class="flex justify-start items-end space-x-2 max-w-[85%]">
                                        <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-850 flex items-center justify-center font-bold text-xs shrink-0 text-slate-600 dark:text-slate-350">
                                            ${avatarInit}
                                        </div>
                                        <div class="bg-white dark:bg-slate-850 text-slate-800 dark:text-slate-100 border border-slate-100 dark:border-slate-800 text-xs px-3.5 py-2 rounded-2xl rounded-tl-none shadow-sm font-medium">
                                            <p>${msg.message}</p>
                                            <span class="block text-[8px] text-slate-450 text-right mt-1 font-mono">${msg.time}</span>
                                        </div>
                                    </div>
                                `;
                            }
                        });

                        container.innerHTML = html;

                        if (data.length > lastMessageCount) {
                            container.scrollTop = container.scrollHeight;
                            lastMessageCount = data.length;
                        }
                    }
                })
                .catch(err => console.error(err));
        }

        function sendChatMessage(e) {
            e.preventDefault();
            if (!activeCustomerId) return;

            const input = document.getElementById('chat-message-input');
            const message = input.value.trim();
            if (!message) return;

            input.value = '';

            const url = isUserAdmin 
                ? `/admin/chats/messages/${activeCustomerId}`
                : '{{ route('chat.messages.send') }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadActiveChatMessages();
                    loadConversations();
                }
            })
            .catch(err => console.error(err));
        }
        @endauth

        // Prefill message function (used from catalog show details button)
        window.openChatWithPrefilledMessage = function(message) {
            // Open the chat box
            if (!chatOpen) {
                toggleLiveChat();
            }
            
            @auth
                if (isUserAdmin) {
                    const input = document.getElementById('chat-message-input');
                    if (input) {
                        input.value = message;
                        input.focus();
                    }
                } else {
                    selectCustomerConversation();
                    const input = document.getElementById('chat-message-input');
                    if (input) {
                        input.value = message;
                        input.focus();
                    }
                }
            @else
                window.location.href = "{{ route('login') }}";
            @endauth
        }

        @auth
            @if(Auth::user()->isAdmin())
            function pollUnreadNotificationCount() {
                fetch('{{ route('admin.chats.conversations') }}')
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('bell-unread-badge');
                        if (badge) {
                            const unread = Array.isArray(data) ? data.filter(c => c.unread_count > 0).length : 0;
                            if (unread > 0) {
                                badge.innerText = unread;
                                badge.classList.remove('hidden');
                            } else {
                                badge.classList.add('hidden');
                            }
                        }
                    })
                    .catch(err => console.error(err));
            }
            // Poll for admin notifications every 5 seconds
            setInterval(pollUnreadNotificationCount, 5000);
            document.addEventListener('DOMContentLoaded', pollUnreadNotificationCount);
            @endif
        @endauth
    </script>

    <!-- Simple Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');

            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
