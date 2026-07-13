<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ARI FARM - Sistem Manajemen Ternak</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" defer></script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .farm-pattern {
            background-color: #09422a;
            background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.05) 0%, transparent 50%),
                              radial-gradient(circle at 80% 70%, rgba(255,255,255,0.04) 0%, transparent 40%);
        }
    </style>
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex">
        <!-- Left Panel: Brand Hero -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 farm-pattern flex-col justify-between p-12 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white/[0.03] translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 rounded-full bg-white/[0.03] -translate-x-1/3 translate-y-1/3"></div>

            <!-- Logo top -->
            <div class="relative z-10">
                <a href="/" class="flex items-center space-x-3">
                    <span class="p-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                        <i class="fa-solid fa-cow text-white text-2xl"></i>
                    </span>
                    <span class="font-black text-2xl tracking-wider text-white uppercase">
                        ARI <span class="text-emerald-300">FARM</span>
                    </span>
                </a>
            </div>

            <!-- Center content -->
            <div class="relative z-10 space-y-6">
                <div class="space-y-4">
                    <span class="inline-block px-3 py-1 rounded-lg bg-white/10 text-emerald-300 text-xs font-bold uppercase tracking-wider border border-white/10">
                        <i class="fa-solid fa-shield-check mr-1"></i> Sistem Manajemen Ternak
                    </span>
                    <h1 class="text-4xl xl:text-5xl font-black text-white leading-tight">
                        Kelola Ternak<br>Lebih <span class="text-emerald-300">Cerdas</span> &<br><span class="text-emerald-300">Efisien</span>
                    </h1>
                    <p class="text-emerald-100/70 text-base font-medium leading-relaxed">
                        Platform terintegrasi untuk monitoring kesehatan, pakan, keuangan, dan produktivitas ternak Anda.
                    </p>
                </div>

                <!-- Feature list -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    @foreach([
                        ['icon' => 'fa-chart-line', 'text' => 'Analisis Real-time'],
                        ['icon' => 'fa-heart-pulse', 'text' => 'Monitor Kesehatan'],
                        ['icon' => 'fa-wheat-awn', 'text' => 'Kelola Pakan'],
                        ['icon' => 'fa-file-invoice', 'text' => 'Laporan Keuangan'],
                    ] as $feat)
                        <div class="flex items-center space-x-2 bg-white/5 rounded-xl px-3 py-2 border border-white/10">
                            <i class="fa-solid {{ $feat['icon'] }} text-emerald-300 text-sm"></i>
                            <span class="text-white/80 text-xs font-semibold">{{ $feat['text'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bottom credits -->
            <div class="relative z-10">
                <p class="text-emerald-100/40 text-xs font-medium">
                    &copy; {{ date('Y') }} ARI FARM. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full lg:w-1/2 xl:w-3/5 flex items-center justify-center p-8 sm:p-12 lg:p-16 bg-slate-50">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="flex lg:hidden items-center space-x-3 mb-10">
                    <span class="p-2.5 rounded-xl bg-[#09422a] text-white">
                        <i class="fa-solid fa-cow text-lg"></i>
                    </span>
                    <span class="font-black text-xl tracking-wider text-[#09422a] uppercase">
                        ARI <span class="text-slate-700">FARM</span>
                    </span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
