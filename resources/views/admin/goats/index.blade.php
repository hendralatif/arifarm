@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Kelola Kambing & Domba</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau ketersediaan ternak, rincian bobot/umur, harga, serta kelola galeri foto.</p>
        </div>
        <a href="{{ route('admin.goats.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md transition">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Kambing
        </a>
    </div>

    {{-- Filter & Search Panel --}}
    <form action="{{ route('admin.goats.index') }}" method="GET" id="filter-form">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-5 shadow-sm space-y-4">

            {{-- Row 1: Search + Sort + Filter Toggle --}}
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center">
                {{-- Search --}}
                <div class="relative flex-1">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                        placeholder="Cari nama kambing atau ras (breed)..."
                        class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition">
                    <i class="fa-solid fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    @if(request('search'))
                        <a href="{{ route('admin.goats.index', request()->except('search', 'page')) }}"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    @endif
                </div>

                {{-- Sort --}}
                <select name="sort" onchange="document.getElementById('filter-form').submit()"
                    class="px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-sm font-semibold focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition shrink-0">
                    <option value="latest"     {{ request('sort','latest')   == 'latest'     ? 'selected' : '' }}>Terbaru</option>
                    <option value="name_asc"   {{ request('sort')            == 'name_asc'   ? 'selected' : '' }}>Nama A–Z</option>
                    <option value="name_desc"  {{ request('sort')            == 'name_desc'  ? 'selected' : '' }}>Nama Z–A</option>
                    <option value="price_asc"  {{ request('sort')            == 'price_asc'  ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="price_desc" {{ request('sort')            == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="weight_desc"{{ request('sort')            == 'weight_desc'? 'selected' : '' }}>Bobot Terberat</option>
                    <option value="weight_asc" {{ request('sort')            == 'weight_asc' ? 'selected' : '' }}>Bobot Teringan</option>
                </select>

                {{-- Filter toggle button --}}
                <button type="button" onclick="toggleFilters()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border {{ $filterCount > 0 ? 'border-emerald-500 bg-emerald-50 text-[#09422a] dark:bg-emerald-950/30 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800' }} text-sm font-bold transition shrink-0">
                    <i class="fa-solid fa-sliders text-xs"></i>
                    Filter
                    @if($filterCount > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#09422a] text-white text-[10px] font-extrabold">{{ $filterCount }}</span>
                    @endif
                </button>

                {{-- Search submit --}}
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#09422a] hover:bg-[#083a25] text-white text-sm font-bold transition shrink-0">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>

            {{-- Row 2: Filter Chips (collapsible) --}}
            <div id="filter-panel" class="{{ $filterCount > 0 ? '' : 'hidden' }} border-t border-slate-100 dark:border-slate-800 pt-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">

                    {{-- Status --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs font-semibold focus:border-emerald-500 outline-none transition">
                            <option value="">Semua</option>
                            <option value="available"    {{ request('status') == 'available'    ? 'selected' : '' }}>Tersedia</option>
                            <option value="sold"         {{ request('status') == 'sold'         ? 'selected' : '' }}>Terjual</option>
                            <option value="not_for_sale" {{ request('status') == 'not_for_sale' ? 'selected' : '' }}>Tidak Dijual</option>
                            <option value="mati"         {{ request('status') == 'mati'         ? 'selected' : '' }}>Mati</option>
                        </select>
                    </div>

                    {{-- Gender --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                        <select name="gender" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs font-semibold focus:border-emerald-500 outline-none transition">
                            <option value="">Semua</option>
                            <option value="male"   {{ request('gender') == 'male'   ? 'selected' : '' }}>Jantan</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Betina</option>
                        </select>
                    </div>

                    {{-- Kategori --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kategori</label>
                        <select name="category" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs font-semibold focus:border-emerald-500 outline-none transition">
                            <option value="">Semua</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Asal Usul --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Asal Usul</label>
                        <select name="acquisition_type" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs font-semibold focus:border-emerald-500 outline-none transition">
                            <option value="">Semua</option>
                            <option value="beli"      {{ request('acquisition_type') == 'beli'      ? 'selected' : '' }}>Pembelian</option>
                            <option value="kelahiran" {{ request('acquisition_type') == 'kelahiran' ? 'selected' : '' }}>Kelahiran</option>
                            <option value="lainnya"   {{ request('acquisition_type') == 'lainnya'   ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>

                    {{-- Kesehatan --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kesehatan</label>
                        <select name="health_status" class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs font-semibold focus:border-emerald-500 outline-none transition">
                            <option value="">Semua</option>
                            <option value="healthy"              {{ request('health_status') == 'healthy'              ? 'selected' : '' }}>Sehat</option>
                            <option value="vaccine_completed"    {{ request('health_status') == 'vaccine_completed'    ? 'selected' : '' }}>Vaksin Lengkap</option>
                            <option value="under_observation"    {{ request('health_status') == 'under_observation'    ? 'selected' : '' }}>Observasi</option>
                        </select>
                    </div>

                    {{-- Harga Min --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Min (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">Rp</span>
                            <input type="number" name="price_min" min="0" step="50000" placeholder="0"
                                value="{{ request('price_min') }}"
                                class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 outline-none transition">
                        </div>
                    </div>

                    {{-- Harga Max --}}
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Harga Maks (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">Rp</span>
                            <input type="number" name="price_max" min="0" step="50000" placeholder="∞"
                                value="{{ request('price_max') }}"
                                class="w-full pl-7 pr-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 outline-none transition">
                        </div>
                    </div>
                </div>

                {{-- Filter action buttons --}}
                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#09422a] hover:bg-[#083a25] text-white text-xs font-bold transition">
                        <i class="fa-solid fa-filter text-[10px]"></i> Terapkan Filter
                    </button>
                    @if($filterCount > 0 || request('search') || request('sort'))
                        <a href="{{ route('admin.goats.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                            <i class="fa-solid fa-xmark text-[10px]"></i> Reset Semua Filter
                        </a>
                    @endif
                    <span class="ml-auto text-xs text-slate-400 font-semibold">
                        {{ $goats->total() }} ternak ditemukan
                    </span>
                </div>
            </div>
        </div>
    </form>

    {{-- Active Filter Chips --}}
    @if($filterCount > 0 || request('search'))
        <div class="flex flex-wrap gap-2">
            @if(request('search'))
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    <i class="fa-solid fa-search text-[10px]"></i> "{{ request('search') }}"
                    <a href="{{ route('admin.goats.index', request()->except('search','page')) }}" class="ml-1 text-slate-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('status'))
                @php $statusLabels = ['available'=>'Tersedia','sold'=>'Terjual','not_for_sale'=>'Tidak Dijual']; @endphp
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/30 text-[#09422a] dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    Status: {{ $statusLabels[request('status')] ?? request('status') }}
                    <a href="{{ route('admin.goats.index', request()->except('status','page')) }}" class="ml-1 text-emerald-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('gender'))
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 border border-blue-200">
                    {{ request('gender') == 'male' ? 'Jantan' : 'Betina' }}
                    <a href="{{ route('admin.goats.index', request()->except('gender','page')) }}" class="ml-1 text-blue-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('category'))
                @php $catName = $categories->firstWhere('id', request('category'))?->name ?? 'Kategori'; @endphp
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200">
                    {{ $catName }}
                    <a href="{{ route('admin.goats.index', request()->except('category','page')) }}" class="ml-1 text-indigo-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('acquisition_type'))
                @php $acqLabels = ['beli'=>'Pembelian','kelahiran'=>'Kelahiran','lainnya'=>'Lainnya']; @endphp
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200">
                    {{ $acqLabels[request('acquisition_type')] ?? request('acquisition_type') }}
                    <a href="{{ route('admin.goats.index', request()->except('acquisition_type','page')) }}" class="ml-1 text-amber-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('health_status'))
                @php $healthLabels = ['healthy'=>'Sehat','vaccine_completed'=>'Vaksin Lengkap','under_observation'=>'Observasi']; @endphp
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-400 border border-rose-200">
                    {{ $healthLabels[request('health_status')] ?? request('health_status') }}
                    <a href="{{ route('admin.goats.index', request()->except('health_status','page')) }}" class="ml-1 text-rose-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
            @if(request('price_min') || request('price_max'))
                <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1 rounded-full text-xs font-bold bg-violet-50 dark:bg-violet-950/30 text-violet-700 dark:text-violet-400 border border-violet-200">
                    Harga: Rp{{ number_format(request('price_min',0)) }} — {{ request('price_max') ? 'Rp'.number_format(request('price_max')) : '∞' }}
                    <a href="{{ route('admin.goats.index', request()->except('price_min','price_max','page')) }}" class="ml-1 text-violet-400 hover:text-rose-500 transition"><i class="fa-solid fa-xmark"></i></a>
                </span>
            @endif
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Gambar</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Nama Hewan</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Ras & Kategori</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Bobot & Umur</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Harga</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Stok</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Status</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($goats as $goat)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors align-middle">
                            {{-- Image --}}
                            <td class="px-5 py-3">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 border border-slate-150 shrink-0">
                                    <img src="{{ asset($goat->first_image) }}" class="w-full h-full object-cover" alt="{{ $goat->name }}">
                                </div>
                            </td>

                            {{-- Name / Gender --}}
                            <td class="px-5 py-3">
                                <div class="font-bold text-slate-900 dark:text-white line-clamp-1">{{ $goat->name }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-extrabold uppercase {{ $goat->gender == 'male' ? 'text-blue-500' : 'text-rose-500' }}">
                                        <i class="fa-solid fa-{{ $goat->gender == 'male' ? 'mars' : 'venus' }} mr-0.5"></i>
                                        {{ $goat->gender == 'male' ? 'Jantan' : 'Betina' }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold border {{ $goat->acquisition_type_badge }}">
                                        {{ $goat->acquisition_type_label }}
                                    </span>
                                </div>
                            </td>

                            {{-- Breed --}}
                            <td class="px-5 py-3 text-xs">
                                <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $goat->breed }}</div>
                                <span class="text-slate-400 block">{{ $goat->category->name }}</span>
                            </td>

                            {{-- Specs --}}
                            <td class="px-5 py-3 text-xs text-slate-500">
                                <div class="font-semibold text-slate-700 dark:text-slate-300">{{ $goat->weight_kg }} kg</div>
                                <span>{{ $goat->age_months }} Bulan</span>
                            </td>

                            {{-- Price --}}
                            <td class="px-5 py-3 text-right font-black text-slate-900 dark:text-white text-sm">
                                {{ $goat->formatted_price }}
                            </td>

                            {{-- Stock --}}
                            <td class="px-5 py-3 text-center font-bold text-xs">{{ $goat->stock }} Ekor</td>

                            {{-- Status --}}
                            <td class="px-5 py-3 text-center">
                                @if($goat->status == 'available')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Tersedia
                                    </span>
                                @elseif($goat->status == 'not_for_sale')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950/40 dark:text-amber-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Tidak Dijual
                                    </span>
                                @elseif($goat->status == 'mati')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-200 text-slate-600 dark:bg-slate-700/60 dark:text-slate-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500 mr-1.5"></span> Mati
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/40 dark:text-rose-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Terjual
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.goats.show', $goat->id) }}"
                                        class="p-2 rounded-xl bg-slate-50 text-[#09422a] hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 transition" title="Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('catalog.show', $goat->slug) }}" target="_blank"
                                        class="p-2 rounded-xl bg-slate-50 text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 transition" title="Lihat Toko">
                                        <i class="fa-solid fa-external-link text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.goats.edit', $goat->id) }}"
                                        class="p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition" title="Edit">
                                        <i class="fa-solid fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.goats.destroy', $goat->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus data kambing ini?')"
                                            class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="fa-solid fa-sheep text-4xl block mb-3 opacity-30"></i>
                                <p class="font-semibold text-base mb-1">Tidak ada ternak ditemukan</p>
                                <p class="text-xs">
                                    @if($filterCount > 0 || request('search'))
                                        Coba ubah atau <a href="{{ route('admin.goats.index') }}" class="text-emerald-600 font-bold underline">reset filter</a>.
                                    @else
                                        Belum ada kambing yang ditambahkan.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($goats->hasPages())
            <div class="px-6 py-5 border-t border-slate-100 dark:border-slate-800">
                {{ $goats->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function toggleFilters() {
    const panel = document.getElementById('filter-panel');
    panel.classList.toggle('hidden');
}
</script>
@endsection
