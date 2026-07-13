@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('admin.goats.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 mb-2">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Kambing
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Tambah Kambing Pilihan</h1>
        <p class="text-sm text-slate-500">Isi rincian lengkap hewan ternak, berat timbangan, umur, serta unggah galeri foto fisiknya.</p>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
        <form action="{{ route('admin.goats.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Category & Name Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label for="category_id" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Kategori / Layanan</label>
                    <select id="category_id" name="category_id" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="name" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nama Kambing / Paket</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Contoh: Etawa Kaligesing Super A" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('name')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Price & Stock Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label for="price" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Harga Jual (Rupiah)</label>
                    <input type="number" id="price" name="price" required value="{{ old('price') }}" placeholder="Contoh: 3500000" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('price')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="stock" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Stok Hewan (Ekor)</label>
                    <input type="number" id="stock" name="stock" required value="{{ old('stock', 1) }}" placeholder="Contoh: 1" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('stock')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Specs details: Weight, Age, breed, Gender -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                <div class="space-y-1">
                    <label for="weight_kg" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Bobot (kg)</label>
                    <input type="number" step="0.01" id="weight_kg" name="weight_kg" required value="{{ old('weight_kg') }}" placeholder="Contoh: 45.5" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('weight_kg')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="age_months" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Umur (Bulan)</label>
                    <input type="number" id="age_months" name="age_months" required value="{{ old('age_months') }}" placeholder="Contoh: 14" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('age_months')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="breed" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Ras Kambing</label>
                    <input type="text" id="breed" name="breed" required value="{{ old('breed') }}" placeholder="Contoh: Etawa Senduro" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('breed')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="gender" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Jenis Kelamin</label>
                    <select id="gender" name="gender" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Jantan</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Betina</option>
                    </select>
                    @error('gender')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Health & Vaccines Row -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
                <div class="space-y-1">
                    <label for="acquisition_type" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Asal Kambing</label>
                    <select id="acquisition_type" name="acquisition_type" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="beli" {{ old('acquisition_type') == 'beli' ? 'selected' : '' }}>Pembelian</option>
                        <option value="kelahiran" {{ old('acquisition_type') == 'kelahiran' ? 'selected' : '' }}>Kelahiran</option>
                        <option value="lainnya" {{ old('acquisition_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('acquisition_type')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="health_status" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Kondisi Kesehatan</label>
                    <select id="health_status" name="health_status" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="healthy" {{ old('health_status') == 'healthy' ? 'selected' : '' }}>Sehat Walafiat</option>
                        <option value="vaccine_completed" {{ old('health_status') == 'vaccine_completed' ? 'selected' : '' }}>Sertifikasi Vaksin Selesai</option>
                        <option value="under_observation" {{ old('health_status') == 'under_observation' ? 'selected' : '' }}>Dalam Pemantauan Medis</option>
                    </select>
                    @error('health_status')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="vaccine_status" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Status Vaksin PMK</label>
                    <select id="vaccine_status" name="vaccine_status" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="1" {{ old('vaccine_status') == '1' ? 'selected' : '' }}>Sudah Vaksin</option>
                        <option value="0" {{ old('vaccine_status') == '0' ? 'selected' : '' }}>Belum Vaksin</option>
                    </select>
                    @error('vaccine_status')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="status" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Ketersediaan Kambing</label>
                    <select id="status" name="status" required class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Tersedia (Ready)</option>
                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Terjual (Sold)</option>
                        <option value="not_for_sale" {{ old('status') == 'not_for_sale' ? 'selected' : '' }}>Tidak Dijual</option>
                    </select>
                    @error('status')
                        <span class="text-xs text-rose-500 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Biaya Pembelian (Tampil jika Asal Kambing = Pembelian) -->
            <div id="purchase_price_wrapper" class="space-y-1 hidden">
                <label for="purchase_price" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Biaya Pembelian (Rupiah)</label>
                <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" placeholder="Contoh: 2500000" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                <span class="text-[10px] text-slate-400 block">Informasi biaya ini akan langsung dicatat ke transaksi pengeluaran (pembelian hewan).</span>
                @error('purchase_price')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="space-y-1">
                <label for="description" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Deskripsi Detail</label>
                <textarea id="description" name="description" rows="5" placeholder="Tuliskan detail mengenai pakan harian, asal usul kandang, temperamen, kesiapan kawin, atau paket olahan daging jika produk adalah paket aqiqah..." class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Multi Images File -->
            <div class="space-y-1">
                <label for="images" class="text-xs font-bold text-slate-500 uppercase tracking-wider block font-display">Unggah Foto-foto Kambing (Multi-Upload)</label>
                <input type="file" id="images" name="images[]" multiple class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                <span class="text-[10px] text-slate-400 block mt-1">Anda dapat menyeleksi beberapa foto sekaligus.</span>
                @error('images')
                    <span class="text-xs text-rose-500 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('admin.goats.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-650 bg-slate-100 hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/10 transition">
                    Simpan Hewan Ternak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const acquisitionType = document.getElementById('acquisition_type');
        const purchasePriceWrapper = document.getElementById('purchase_price_wrapper');
        const purchasePriceInput = document.getElementById('purchase_price');

        function togglePurchasePrice() {
            if (acquisitionType.value === 'beli') {
                purchasePriceWrapper.classList.remove('hidden');
                purchasePriceInput.setAttribute('required', 'required');
            } else {
                purchasePriceWrapper.classList.add('hidden');
                purchasePriceInput.removeAttribute('required');
            }
        }

        acquisitionType.addEventListener('change', togglePurchasePrice);
        togglePurchasePrice();
    });
</script>
@endsection
