@extends('layouts.public')

@section('title', 'Katalog Kambing & Domba Pilihan - Ari Farm')

@section('content')
<section class="section-py bg-slate-50">
    <div class="container-xl section-px">

        {{-- Page Header --}}
        <div class="mb-10 text-center md:text-left animate-fade-in-up">
            <span class="section-tag"><i class="fa-solid fa-list"></i> Semua Produk</span>
            <h1 class="section-title">Katalog Kambing <span class="text-gradient-green">&amp; Domba</span></h1>
            <p class="section-subtitle md:mx-0 mt-2">Temukan hewan ternak terbaik yang siap dipinang langsung dari kandang kami.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- ── Filter Sidebar ── --}}
            <aside class="lg:col-span-3 space-y-4">
                <form action="{{ route('catalog') }}" method="GET" class="card p-6 space-y-5 !overflow-visible">
                    <h3 class="font-display font-bold text-sm uppercase tracking-wider text-slate-400">Filter Pencarian</h3>

                    {{-- Search --}}
                    <div class="form-group">
                        <label for="search" class="form-label">Kata Kunci</label>
                        <div class="input-icon-wrapper">
                            <span class="icon"><i class="fa-solid fa-search text-xs"></i></span>
                            <input type="text" id="search" name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari kambing..."
                                   class="form-input pl-9">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="form-group">
                        <label for="category" class="form-label">Kategori / Ras</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Gender --}}
                    <div class="form-group">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select name="gender" id="gender" class="form-select">
                            <option value="">Semua Jenis Kelamin</option>
                            <option value="male"   {{ request('gender') == 'male'   ? 'selected' : '' }}>Jantan</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Betina</option>
                        </select>
                    </div>

                    {{-- Weight Range --}}
                    <div class="form-group">
                        <label class="form-label">Bobot Badan (kg)</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="weight_min" value="{{ request('weight_min') }}"
                                   placeholder="Min" class="form-input text-center">
                            <input type="number" name="weight_max" value="{{ request('weight_max') }}"
                                   placeholder="Max" class="form-input text-center">
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="form-group">
                        <label class="form-label">Rentang Harga (Rp)</label>
                        <div class="space-y-2">
                            <input type="number" name="price_min" value="{{ request('price_min') }}"
                                   placeholder="Min Harga" class="form-input">
                            <input type="number" name="price_max" value="{{ request('price_max') }}"
                                   placeholder="Max Harga" class="form-input">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="form-group">
                        <label for="sort" class="form-label">Urutkan</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="latest"     {{ request('sort') == 'latest'     ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="weight_desc"{{ request('sort') == 'weight_desc'? 'selected' : '' }}>Bobot Terberat</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-2 flex gap-2">
                        <a href="{{ route('catalog') }}" class="btn btn-ghost w-1/2 !text-xs border border-slate-200">Reset</a>
                        <button type="submit" class="btn-primary w-1/2 !text-xs">
                            <i class="fa-solid fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </aside>

            {{-- ── Goat Grid ── --}}
            <div class="lg:col-span-9 space-y-6">

                @if($goats->count() > 0)
                    {{-- Result count --}}
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500">
                            Menampilkan <span class="font-bold text-slate-700">{{ $goats->total() }}</span> produk
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($goats as $goat)
                        <article class="product-card group animate-fade-in-up" style="animation-delay: {{ $loop->index * 50 }}ms">

                            {{-- Image --}}
                            <div class="relative overflow-hidden aspect-square bg-slate-100">
                                <img src="{{ asset($goat->first_image) }}"
                                     alt="{{ $goat->name }}"
                                     class="product-card-img aspect-square"
                                     loading="lazy">

                                {{-- Overlay badges --}}
                                <div class="absolute top-3 right-3 flex flex-col gap-1.5">
                                    <span class="badge {{ $goat->gender == 'male' ? 'bg-blue-600 text-white' : 'bg-pink-500 text-white' }} !rounded-lg !text-[10px] uppercase tracking-wider">
                                        <i class="fa-solid {{ $goat->gender == 'male' ? 'fa-mars' : 'fa-venus' }}"></i>
                                        {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                                    </span>
                                    @if($goat->vaccine_status)
                                    <span class="badge bg-primary-600 text-white !rounded-lg !text-[10px] uppercase tracking-wider">
                                        <i class="fa-solid fa-shield-virus"></i> Vaksin
                                    </span>
                                    @endif
                                </div>

                                 {{-- Status sold/not_for_sale overlay --}}
                                 @if($goat->status == 'sold')
                                 <div class="absolute inset-0 bg-slate-900/60 flex items-center justify-center">
                                     <span class="badge bg-white text-slate-700 !px-4 !py-2 !text-sm !rounded-xl font-bold shadow">
                                         <i class="fa-solid fa-check-circle text-primary-600"></i> Terjual
                                     </span>
                                 </div>
                                 @elseif($goat->status == 'not_for_sale')
                                 <div class="absolute inset-0 bg-slate-900/65 flex items-center justify-center">
                                     <span class="badge bg-amber-500 text-white !px-4 !py-2 !text-sm !rounded-xl font-bold shadow flex items-center gap-1.5 border border-amber-400">
                                         <i class="fa-solid fa-circle-minus"></i> Tidak Dijual
                                     </span>
                                 </div>
                                 @endif
                            </div>

                            {{-- Card Body --}}
                            <div class="product-card-body">
                                <div class="space-y-1.5">
                                    <span class="section-tag !mb-0 !text-[10px]">{{ $goat->category->name }}</span>
                                    <h3 class="product-card-title">{{ $goat->name }}</h3>

                                    {{-- Meta --}}
                                    <div class="flex items-center gap-4">
                                        <span class="product-card-meta">
                                            <i class="fa-solid fa-weight-hanging text-primary-400"></i>
                                            {{ $goat->weight_kg }} kg
                                        </span>
                                        <span class="product-card-meta">
                                            <i class="fa-solid fa-calendar-days text-primary-400"></i>
                                            {{ $goat->age_months }} bln
                                        </span>
                                    </div>
                                </div>

                                {{-- Footer --}}
                                <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider block">Harga</span>
                                        <span class="product-card-price">{{ $goat->formatted_price }}</span>
                                    </div>
                                    <a href="{{ route('catalog.show', $goat->slug) }}"
                                       class="btn-primary btn-sm whitespace-nowrap">
                                        Detail <i class="fa-solid fa-angle-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="pagination">
                        {{ $goats->links() }}
                    </div>

                @else
                    {{-- Empty State --}}
                    <div class="card p-16 text-center space-y-4 flex flex-col items-center">
                        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-4xl text-slate-300 animate-float">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 font-display">Kambing Tidak Ditemukan</h3>
                        <p class="text-slate-400 max-w-sm text-sm">
                            Kami tidak menemukan hewan sesuai filter Anda. Coba reset pencarian untuk melihat semua produk.
                        </p>
                        <a href="{{ route('catalog') }}" class="btn-primary">
                            <i class="fa-solid fa-rotate-left"></i> Reset Pencarian
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
