@extends('layouts.public')

@section('title', 'ARI FARM - Pusat Penjualan Kambing & Domba Premium')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-emerald-50/60 to-white dark:from-slate-950 dark:to-slate-900 overflow-hidden py-20 sm:py-28">
    <!-- Decorative background blobs -->
    <div class="absolute top-1/4 right-0 w-96 h-96 bg-[#09422a]/5 dark:bg-emerald-500/5 rounded-full blur-3xl -z-10 translate-x-1/2"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-emerald-100/30 dark:bg-emerald-950/10 rounded-full blur-3xl -z-10 -translate-x-1/2"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Text Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-[#09422a] border border-emerald-150 dark:bg-emerald-950/30 dark:text-emerald-450 dark:border-emerald-900">
                    <i class="fa-solid fa-certificate mr-1.5 text-amber-500"></i> Peternakan Amanah & Profesional
                </span>
                
                <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-tight text-slate-900 dark:text-white font-display">
                    Pilih Kambing Terbaik Anda Dengan <span class="text-[#09422a] dark:text-emerald-400">Mudah & Syar'i</span>
                </h1>
                
                <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                    ARI FARM menyediakan kambing Etawa, Boer, Kacang, dan Dombos berkualitas premium langsung dari kandang peternak terbaik. Sehat, bersertifikat sah, dan siap diantar langsung.
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl text-sm font-black bg-[#09422a] hover:bg-[#083a25] text-white shadow-lg shadow-[#09422a]/20 hover:shadow-[#09422a]/30 transition duration-150 group">
                        Cari Kambing Pilihan <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <button onclick="openChatWithPrefilledMessage('Halo ARI FARM, saya tertarik dengan kambing pilihan. Apakah ada yang ready?')" class="inline-flex items-center justify-center px-8 py-4 rounded-2xl text-sm font-bold bg-white hover:bg-slate-50 text-[#09422a] border border-slate-200 shadow-sm transition duration-150 dark:bg-slate-900 dark:border-slate-800 dark:text-emerald-400 dark:hover:bg-slate-850">
                        <i class="fa-solid fa-comments mr-2 text-emerald-600"></i> Tanya Ketersediaan
                    </button>
                </div>
            </div>
            
            <!-- Graphic/Image Mockup -->
            <div class="lg:col-span-5 relative hidden lg:block">
                <!-- Outer Shadow Glow Container -->
                <div class="absolute -inset-1.5 rounded-[2.5rem] bg-gradient-to-tr from-emerald-500 to-[#09422a] opacity-10 blur-xl"></div>
                <!-- Main Mockup Card -->
                <div class="relative bg-white dark:bg-slate-900 p-4 rounded-[2.25rem] border border-slate-250 dark:border-slate-800 shadow-xl">
                    <div class="rounded-3xl overflow-hidden aspect-[4/3] relative">
                        <img src="{{ asset('img/gambar.png') }}" alt="Kambing Pilihan ARI FARM" class="w-full h-full object-cover object-top">
                        <div class="absolute bottom-3 left-3 bg-[#09422a]/90 backdrop-blur-sm text-white px-3 py-1.5 rounded-xl text-xs font-bold">
                            <i class="fa-solid fa-cow mr-1.5"></i> Etawa Senduro Betina
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Banner Section -->
<section class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10">
    <div class="bg-white dark:bg-slate-900 rounded-[2.25rem] shadow-xl border border-slate-200/50 dark:border-slate-800 p-8 grid grid-cols-2 lg:grid-cols-4 gap-6 text-center">
        <div class="space-y-1">
            <span class="block text-3xl sm:text-4xl font-black text-[#09422a] dark:text-emerald-400 font-display">
                {{ $stats['total_goats'] }}+
            </span>
            <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest block">Kambing Tersedia</span>
        </div>
        <div class="space-y-1 border-l border-slate-100 dark:border-slate-800">
            <span class="block text-3xl sm:text-4xl font-black text-[#09422a] dark:text-emerald-400 font-display">
                {{ $stats['sold_goats'] }}+
            </span>
            <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest block">Telah Terjual</span>
        </div>
        <div class="space-y-1 border-l border-slate-100 dark:border-slate-800">
            <span class="block text-3xl sm:text-4xl font-black text-[#09422a] dark:text-emerald-400 font-display">
                {{ $stats['happy_customers'] }}%
            </span>
            <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest block">Kepuasan Pelanggan</span>
        </div>
        <div class="space-y-1 border-l border-slate-100 dark:border-slate-800">
            <span class="block text-3xl sm:text-4xl font-black text-[#09422a] dark:text-emerald-400 font-display">
                {{ $stats['trusted_partners'] }}+
            </span>
            <span class="text-[10px] sm:text-xs font-black text-slate-400 uppercase tracking-widest block">Kandang Mitra</span>
        </div>
    </div>
</section>

<!-- Product Categories Section -->
<section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
        <span class="text-xs font-extrabold uppercase tracking-widest text-[#09422a] dark:text-emerald-400">Layanan Terbaik Kami</span>
        <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 dark:text-white">Jenis Ras Kambing & Layanan</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
            Temukan ras kambing berkualitas tinggi untuk kebutuhan qurban maupun bibit ternak produktif.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
            <div class="group bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                <div class="relative overflow-hidden aspect-[16/10] bg-slate-50 dark:bg-slate-950">
                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-550">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                        <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider bg-[#09422a] border border-emerald-800/20 rounded-lg shadow-sm">
                            {{ $category->goats_count }} Tersedia
                        </span>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-[#09422a] dark:group-hover:text-emerald-400 transition">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                        {{ $category->description }}
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('catalog') }}?category={{ $category->slug }}" class="inline-flex items-center text-sm font-bold text-[#09422a] dark:text-emerald-400 hover:underline group/link">
                            Lihat Selengkapnya <i class="fa-solid fa-arrow-right ml-1.5 text-xs group-hover/link:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-24 bg-slate-50 dark:bg-slate-900/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row items-center justify-between mb-16 gap-6">
            <div class="text-center sm:text-left space-y-2">
                <span class="text-xs font-extrabold uppercase tracking-widest text-[#09422a] dark:text-emerald-400">Pilihan Terpopuler</span>
                <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 dark:text-white">Koleksi Terbaru Kambing Pilihan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Hewan ternak sehat pilihan dengan timbangan jujur dan video rekam fisik lengkap.</p>
            </div>
            <div>
                <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-[#09422a] bg-white border border-slate-200 dark:bg-slate-900 dark:text-emerald-400 dark:border-slate-800 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm transition">
                    Lihat Semua Katalog <i class="fa-solid fa-angle-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredGoats as $goat)
                <div class="group bg-white dark:bg-slate-900 rounded-[2rem] overflow-hidden border border-slate-200/50 dark:border-slate-800 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col h-full">
                    <!-- Image -->
                    <div class="relative overflow-hidden aspect-square bg-slate-50 dark:bg-slate-950">
                        <img src="{{ asset($goat->first_image) }}" alt="{{ $goat->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-550">
                        <div class="absolute top-3 right-3 flex flex-col gap-1.5">
                            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg bg-[#09422a] text-white shadow-sm">
                                {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                            </span>
                            @if($goat->vaccine_status)
                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg bg-blue-600 text-white flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-shield-virus text-[9px]"></i> Vaksin
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block">
                                {{ $goat->category->name }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-[#09422a] dark:group-hover:text-emerald-400 transition">
                                {{ $goat->name }}
                            </h3>
                            <div class="flex items-center space-x-3.5 text-xs text-slate-400 font-semibold pt-1">
                                <span><i class="fa-solid fa-weight-hanging mr-1.5 text-slate-400"></i> {{ $goat->weight_kg }} kg</span>
                                <span><i class="fa-solid fa-calendar mr-1.5 text-slate-400"></i> {{ $goat->age_months }} bln</span>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="text-[9px] text-slate-400 uppercase font-black tracking-wider block">Harga</span>
                                <span class="font-extrabold text-[#09422a] dark:text-emerald-400 text-base">
                                    {{ $goat->formatted_price }}
                                </span>
                            </div>
                            <a href="{{ route('catalog.show', $goat->slug) }}" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-650 hover:bg-[#09422a] hover:text-white dark:text-slate-300 dark:hover:bg-[#09422a] dark:hover:text-white transition shadow-sm border border-slate-100 dark:border-slate-700">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Values Section (Why Choose Us) -->
<section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
        <span class="text-xs font-extrabold uppercase tracking-widest text-[#09422a] dark:text-emerald-400">Keunggulan Layanan</span>
        <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 dark:text-white">Kenapa Memilih ARI FARM?</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Kami menjamin transparansi, keabsahan fiqih syariah, serta kesehatan fisik hewan ternak secara maksimal.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Card 1: Timbangan Jujur -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
            <div class="inline-flex p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-[#09422a] dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Timbangan Jujur</h3>
            <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed">Bobot kambing ditimbang secara berkala dengan dokumentasi transparan, menjamin keadilan penuh dalam transaksi.</p>
        </div>

        <!-- Card 2: Sehat & Bersertifikat -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
            <div class="inline-flex p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-[#09422a] dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-heart-circle-check"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Sehat & Bersertifikat</h3>
            <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed">Hewan telah lolos pemeriksaan kesehatan dinas peternakan secara berkala, bebas dari wabah PMK.</p>
        </div>

        <!-- Card 3: Syarat Qurban Syar'i -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
            <div class="inline-flex p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-[#09422a] dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-star-and-crescent"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Syarat Qurban Syar'i</h3>
            <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed">Pemilihan hewan qurban dipastikan memenuhi ketentuan umur minimal secara fiqih Islam.</p>
        </div>

        <!-- Card 4: Pengiriman Aman -->
        <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 text-center space-y-4 shadow-sm hover:shadow-md transition duration-200">
            <div class="inline-flex p-3.5 rounded-2xl bg-emerald-50 dark:bg-emerald-950/20 text-[#09422a] dark:text-emerald-400 text-2xl">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pengiriman Aman</h3>
            <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed">Kendaraan angkut khusus yang bersih guna menjaga kesehatan fisik dan meminimalkan tingkat stres hewan.</p>
        </div>
    </div>
</section>

<!-- Feeds Used Section -->
<section class="py-24 bg-slate-50 dark:bg-slate-900/30 border-t border-slate-200/50 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-[#09422a] dark:text-emerald-400">Nutrisi & Pakan Berkualitas</span>
            <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 dark:text-white">Pakan Standardisasi Kandang Ari Farm</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Kami berkomitmen menjaga kualitas fisik dan bobot kambing dengan memberikan pakan bernutrisi tinggi, teruji, dan alami.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pakan 1: Konsentrat -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 space-y-5 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 text-2xl">
                    <i class="fa-solid fa-wheat-awn"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Konsentrat Premium</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pemberian asupan konsentrat harian dengan kadar serat, karbohidrat, dan protein seimbang guna mempercepat pembentukan bobot daging kambing secara optimal dan sehat.
                    </p>
                </div>
            </div>

            <!-- Pakan 2: Rumput Pakchong -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 space-y-5 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 text-2xl">
                    <i class="fa-solid fa-seedling"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Rumput Pakchong Organik</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pakan hijauan unggulan segar hasil budidaya mandiri yang kaya akan serat kasar organik. Menjamin pencernaan kambing selalu dalam kondisi prima dan sehat alami.
                    </p>
                </div>
            </div>

            <!-- Pakan 3: Mineral Blok -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] border border-slate-200/60 dark:border-slate-800 space-y-5 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center text-emerald-600 text-2xl">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div class="space-y-2">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Mineral Blok Esensial</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pemberian suplemen mineral blok yang wajib dijadwalkan minimal sekali setiap 2 minggu guna memperkuat sistem metabolisme, imunitas daya tahan tubuh, dan struktur tulang kambing.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps Location Section -->
<section class="py-24 border-t border-slate-200/50 dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-extrabold uppercase tracking-widest text-[#09422a] dark:text-emerald-400">Kunjungi Kandang Kami</span>
            <h2 class="text-3xl sm:text-4xl font-black font-display text-slate-900 dark:text-white">Lokasi Fisik Ari Farm</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                Temukan jalan menuju kandang Ari Farm di Google Maps. Kami mengundang Anda melihat langsung kesehatan dan kualitas ternak kami.
            </p>
        </div>

        <!-- Google Maps Iframe Container -->
        <div class="relative bg-white dark:bg-slate-900 p-4 rounded-[2.5rem] border border-slate-200/60 dark:border-slate-800 shadow-xl overflow-hidden aspect-[16/9] md:aspect-[21/9] w-full">
            <iframe 
                src="https://maps.google.com/maps?q=-7.434107,109.890920&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                class="w-full h-full rounded-[2rem] border-0" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>
@endsection
