@extends('layouts.public')

@section('title', $goat->name . ' - Ari Farm')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-xs sm:text-sm text-slate-500 font-semibold" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="hover:text-emerald-600 transition"><i class="fa-solid fa-home mr-1.5"></i> Beranda</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-400 text-[10px] mx-1"></i>
                        <a href="{{ route('catalog') }}" class="hover:text-emerald-600 transition">Katalog</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-slate-400 text-[10px] mx-1"></i>
                        <span class="text-slate-400 dark:text-slate-500 truncate max-w-[180px] sm:max-w-xs">{{ $goat->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-6 sm:p-10 shadow-sm">
            <!-- Left Side: Image Gallery -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-950 aspect-[4/3] border border-slate-200 dark:border-slate-800">
                    <img id="main-image" src="{{ asset($goat->first_image) }}" alt="{{ $goat->name }}" class="w-full h-full object-cover transition-transform duration-300">
                    
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-lg bg-emerald-600 text-white">
                            {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                        </span>
                        @if($goat->vaccine_status)
                            <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-lg bg-blue-600 text-white flex items-center gap-1">
                                <i class="fa-solid fa-shield-virus"></i> Vaksin Lengkap
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails -->
                @if(is_array($goat->images) && count($goat->images) > 1)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($goat->images as $img)
                            <button onclick="changeImage('{{ asset($img) }}')" class="rounded-xl overflow-hidden aspect-square border-2 border-transparent hover:border-emerald-500 focus:border-emerald-500 transition bg-slate-100 dark:bg-slate-950">
                                <img src="{{ asset($img) }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Side: Specs & Actions -->
            <div class="lg:col-span-6 space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <div class="space-y-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $goat->category->name }}
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                            {{ $goat->name }}
                        </h1>
                    </div>

                    <!-- Price and Stock -->
                    <div class="flex items-baseline space-x-3">
                        <span class="text-3xl font-black text-slate-900 dark:text-white">
                            {{ $goat->formatted_price }}
                        </span>
                        <span class="text-sm font-semibold {{ $goat->stock > 0 ? 'text-emerald-600' : 'text-rose-500' }}">
                            {{ $goat->stock > 0 ? 'Stok: ' . $goat->stock . ' Ekor' : 'Habis Terjual' }}
                        </span>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-800">

                    <!-- Specs Sheet Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3">
                            <span class="text-emerald-600 dark:text-emerald-400 text-lg"><i class="fa-solid fa-weight-hanging"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Bobot</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $goat->weight_kg }} kg</span>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3">
                            <span class="text-emerald-600 dark:text-emerald-400 text-lg"><i class="fa-solid fa-calendar"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Umur</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $goat->age_months }} Bulan</span>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3">
                            <span class="text-emerald-600 dark:text-emerald-400 text-lg"><i class="fa-solid fa-dna"></i></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Breeding/Ras</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $goat->breed }}</span>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center space-x-3">
                            <span class="text-emerald-600 dark:text-emerald-400 text-lg">
                                @if($goat->health_status == 'healthy')
                                    <i class="fa-solid fa-heart-pulse text-emerald-500"></i>
                                @elseif($goat->health_status == 'vaccine_completed')
                                    <i class="fa-solid fa-shield-virus text-blue-500"></i>
                                @else
                                    <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
                                @endif
                            </span>
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Kondisi Fisik</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                    @if($goat->health_status == 'healthy')
                                        Sehat Bugar
                                    @elseif($goat->health_status == 'vaccine_completed')
                                        Vaksin Selesai
                                    @else
                                        Dalam Pantauan
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-slate-400">Deskripsi Lengkap</h4>
                        <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $goat->description }}
                        </p>
                    </div>
                </div>

                <!-- Purchase CTAs -->
                <div class="pt-6 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    @if($goat->status == 'not_for_sale')
                        <button disabled class="w-full py-3.5 rounded-2xl text-base font-bold bg-amber-500 text-white cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-minus"></i> Kambing Tidak Dijual
                        </button>
                    @elseif($goat->stock > 0)
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Add to Cart -->
                            <form action="{{ route('cart.add', $goat->id) }}" method="POST" class="sm:flex-1">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/10 hover:shadow-emerald-600/20 transition duration-150">
                                    <i class="fa-solid fa-cart-plus mr-2"></i> Tambah Ke Keranjang
                                </button>
                            </form>

                            <!-- Direct Chat Button -->
                            <button onclick="openChatWithPrefilledMessage('Halo ARI FARM, saya tertarik dengan kambing \'{{ $goat->name }}\' ({{ $goat->formatted_price }}). Apakah masih ready?')" class="flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:border-emerald-500 dark:hover:bg-emerald-950/20 transition duration-150">
                                <i class="fa-solid fa-headset mr-2 text-xl"></i> Chat Admin
                            </button>

                            <!-- Chat Penjual Button -->
                            <form action="{{ route('chats.start', $goat->seller_id ?? 1) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold bg-[#09422a] text-white hover:bg-[#073321] shadow-lg shadow-[#09422a]/10 transition duration-150">
                                    <i class="fa-solid fa-comments mr-2 text-xl"></i> Chat Penjual
                                </button>
                            </form>
                        </div>
                    @else
                        <button disabled class="w-full py-3.5 rounded-2xl text-base font-bold bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-600 cursor-not-allowed">
                            Kambing Habis Terjual
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Goats -->
        @if($relatedGoats->count() > 0)
            <div class="mt-16 space-y-8">
                <h2 class="text-2xl font-bold font-display text-slate-900 dark:text-white">Kambing Sejenis Lainnya</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedGoats as $relGoat)
                        <div class="group bg-white dark:bg-slate-900 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800 hover-scale hover:shadow-lg transition duration-300 flex flex-col h-full">
                            <div class="relative overflow-hidden aspect-square">
                                <img src="{{ asset($relGoat->first_image) }}" alt="{{ $relGoat->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <span class="text-xs font-semibold text-emerald-600 uppercase">{{ $relGoat->category->name }}</span>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white line-clamp-1 group-hover:text-emerald-600 transition">{{ $relGoat->name }}</h3>
                                    <div class="flex items-center space-x-3 text-xs text-slate-500">
                                        <span><i class="fa-solid fa-weight-hanging mr-1"></i> {{ $relGoat->weight_kg }} kg</span>
                                        <span><i class="fa-solid fa-calendar mr-1"></i> {{ $relGoat->age_months }} bln</span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                    <span class="font-extrabold text-slate-900 dark:text-white">{{ $relGoat->formatted_price }}</span>
                                    <a href="{{ route('catalog.show', $relGoat->slug) }}" class="text-xs font-bold text-emerald-600 hover:underline">Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</section>

<!-- Switch image JS script -->
<script>
    function changeImage(src) {
        document.getElementById('main-image').src = src;
    }
</script>
@endsection
