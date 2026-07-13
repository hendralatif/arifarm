@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 mb-2">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Riwayat Pesanan
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Review Pesanan #{{ $order->invoice_number }}</h1>
            <p class="text-xs text-slate-500">Dibuat tanggal {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
        </div>
        
        <!-- Status indicator badge -->
        <div>
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-sm font-bold border {{ $order->status_badge_class }}">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: Items and Payment Proof Review -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Items ordered -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">Item Dipesan</h3>
                
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-3.5 first:pt-0 last:pb-0">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 flex-shrink-0">
                                    @if($item->goat)
                                        <img src="{{ asset($item->goat->first_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-xl">
                                            <i class="fa-solid fa-sheep"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">
                                        {{ $item->goat?->name ?? '(Data kambing telah dihapus)' }}
                                    </h4>
                                    <span class="text-xs text-slate-400">
                                        Breed: {{ $item->goat?->breed ?? '-' }} | Bobot: {{ $item->goat?->weight_kg ?? '-' }} kg | Qty: {{ $item->quantity }} ekor
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                                <span class="font-bold text-sm text-slate-900 dark:text-white">
                                    {{ $item->formatted_subtotal }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Persetujuan Transaksi & Ongkos Kirim (Hanya untuk pending_approval) -->
            @if($order->status === 'pending_approval')
                <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">Persetujuan Transaksi & Ongkos Kirim</h3>
                    <p class="text-sm text-slate-500">Silakan tinjau pesanan ini. Anda dapat menyetujui transaksi dan menambahkan biaya pengiriman (ongkir) jika pengiriman menggunakan kurir.</p>

                    <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        @if($order->shipping_method === 'diantar')
                            <!-- Delivery logistics calculation details -->
                            <div class="p-4 bg-[#09422a]/5 dark:bg-emerald-950/10 border border-[#09422a]/10 dark:border-emerald-900/30 rounded-2xl text-xs space-y-1.5 text-slate-700 dark:text-slate-300">
                                <p class="font-bold text-[#09422a] dark:text-emerald-450 text-sm flex items-center gap-1">
                                    <i class="fa-solid fa-map-location-dot"></i> Estimasi Pengiriman Kurir:
                                </p>
                                <p>Jarak Pengiriman: <strong class="text-slate-900 dark:text-white">{{ $order->shipping_distance }} km</strong></p>
                                <p>Tujuan di Kab. Wonosobo: <strong class="text-slate-900 dark:text-white">{{ $order->is_wonosobo ? 'Ya (Diskon 20% aktif)' : 'Tidak' }}</strong></p>
                                @php
                                    $shippingCalc = \App\Models\Order::calculateShippingCost($order->shipping_distance, $order->is_wonosobo);
                                @endphp
                                @if($shippingCalc['outside_range'])
                                    <p class="text-amber-600 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-circle-exclamation"></i> Jarak di luar jangkauan (>200 km). Ongkos kirim harus disepakati secara manual.
                                    </p>
                                @else
                                    <p class="text-emerald-600 dark:text-emerald-400 font-semibold">
                                        Rekomendasi Sistem (Terhitung Diskon): Rp {{ number_format($shippingCalc['cost'], 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            @if($shippingCalc['outside_range'])
                                <div class="space-y-1.5 mt-3">
                                    <label for="shipping_cost" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Biaya Pengiriman (Ongkos Kirim)</label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 text-xs">Rp</span>
                                        </div>
                                        <input type="number" name="shipping_cost" id="shipping_cost" required min="0" value="{{ (int)$order->shipping_cost }}" placeholder="Masukkan nominal ongkir manual" class="w-full pl-9 py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>
                                    <span class="text-[10px] text-slate-400">Jarak di luar jangkauan. Tuliskan nominal biaya pengiriman manual hasil kesepakatan dengan pelanggan.</span>
                                </div>
                            @else
                                <input type="hidden" name="shipping_cost" value="{{ (int)$shippingCalc['cost'] }}">
                                <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl dark:bg-emerald-950/10 dark:border-emerald-900/30 text-xs mt-3">
                                    <span class="font-bold text-[#09422a] dark:text-emerald-450 flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check"></i> Biaya Pengiriman Otomatis: Rp {{ number_format($shippingCalc['cost'], 0, ',', '.') }}
                                    </span>
                                    <p class="text-slate-550 dark:text-slate-400 mt-1">Sistem mendeteksi jarak dalam jangkauan ({{ $order->shipping_distance }} km). Biaya kirim diset secara otomatis tanpa perlu diisi manual.</p>
                                </div>
                            @endif
                        @else
                            <input type="hidden" name="shipping_cost" value="0">
                            <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl dark:bg-emerald-950/10 dark:border-emerald-900/30 text-xs">
                                <span class="font-bold text-emerald-800 dark:text-emerald-450 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Bebas Biaya Kirim
                                </span>
                                <p class="text-slate-550 dark:text-slate-400 mt-1">Pelanggan memilih metode **Diambil Sendiri** langsung ke kandang. Ongkos kirim otomatis diset Rp 0.</p>
                            </div>
                        @endif

                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md shadow-[#09422a]/10 transition">
                                <i class="fa-solid fa-circle-check mr-1.5"></i> Setujui Transaksi
                            </button>
                            
                            <button type="button" onclick="document.getElementById('reject-form').submit();" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                <i class="fa-solid fa-circle-xmark mr-1.5"></i> Tolak & Batalkan
                            </button>
                        </div>
                    </form>

                    <form id="reject-form" action="{{ route('admin.orders.reject', $order->id) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @endif

            <!-- Payment proof review -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-5">
                <h3 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">Verifikasi Bukti Pembayaran</h3>

                @if($order->payment_receipt)
                    <div class="space-y-4">
                        <p class="text-sm text-slate-500">Silakan periksa keaslian bukti transfer yang diunggah pelanggan berikut.</p>
                        
                        <div class="max-w-md rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm bg-slate-50">
                            <a href="{{ asset($order->payment_receipt) }}" target="_blank">
                                <img src="{{ asset($order->payment_receipt) }}" class="w-full object-cover">
                            </a>
                        </div>

                        <!-- Approve / Reject Form Actions -->
                        @if($order->status === 'pending_verification')
                            <div class="flex gap-3 pt-2">
                                <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-md shadow-emerald-600/10 transition">
                                        <i class="fa-solid fa-check mr-1.5"></i> Setujui Pembayaran
                                    </button>
                                </form>

                                <form action="{{ route('admin.orders.verify', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menolak pembayaran ini? Bukti foto akan dihapus agar pelanggan mengunggah ulang.')">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                        <i class="fa-solid fa-times mr-1.5"></i> Tolak Pembayaran
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-xl text-xs text-slate-400">
                                Pembayaran ini sudah diproses dan tidak berada dalam antrean peninjauan verifikasi.
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 text-center text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl">
                        <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300"></i>
                        <p class="text-sm font-semibold">Bukti Pembayaran Belum Diunggah</p>
                        <p class="text-xs text-slate-400 mt-0.5">Pelanggan belum mengunggah gambar bukti transfer bank.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Shipping Details & Status Update Form -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Financial Summary Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Rincian Keuangan</h3>
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Subtotal Item</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $order->formatted_subtotal }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Biaya Pengiriman</span>
                        <span class="font-semibold text-slate-900 dark:text-white">
                            {{ $order->formatted_shipping_cost }}
                        </span>
                    </div>
                    @if($order->shipping_method === 'diantar' && !is_null($order->shipping_distance))
                        <div class="text-[10px] text-slate-400 -mt-1 text-right">
                            Jarak: {{ $order->shipping_distance }} km 
                            @if($order->is_wonosobo)
                                (Kab. Wonosobo -20%)
                            @endif
                        </div>
                    @endif
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-baseline pt-1">
                        <span class="font-bold text-slate-900 dark:text-white">Total Tagihan</span>
                        <span class="font-black text-[#09422a] dark:text-emerald-450 text-base">
                            {{ $order->formatted_total }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs pt-1">
                        <span class="text-slate-400">Tipe Pembayaran</span>
                        <span class="font-bold text-slate-700 dark:text-slate-350">
                            {{ $order->payment_type === 'dp' ? 'Uang Muka / DP (30%)' : 'Lunas (100%)' }}
                        </span>
                    </div>
                    @if($order->payment_type === 'dp')
                        <div class="flex justify-between items-baseline text-xs text-rose-600 dark:text-rose-400">
                            <span>Nominal DP (30%)</span>
                            <span class="font-bold text-sm">{{ $order->formatted_dp_amount }}</span>
                        </div>
                        <div class="flex justify-between items-baseline text-xs text-slate-500">
                            <span>Sisa Pelunasan</span>
                            <span class="font-bold">{{ $order->formatted_remaining_balance }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Info Card -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Detail Logistik & Penerima</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-slate-450 block font-semibold">Metode Pengiriman</span>
                        <span class="font-black text-emerald-700 dark:text-emerald-400">
                            {{ $order->shipping_method === 'diambil' ? 'Diambil Sendiri (Pick Up)' : 'Diantar Kurir (Delivery)' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-450 block font-semibold">Nama Pelanggan</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $order->user?->name ?? '(Akun dihapus)' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-450 block font-semibold">Kontak WA / Telpon</span>
                        <span class="font-bold text-slate-900 dark:text-white select-all">{{ $order->phone_number }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-450 block font-semibold">Alamat Tujuan / Kandang Ambil</span>
                        <span class="text-slate-650 dark:text-slate-400 font-semibold leading-relaxed">{{ $order->shipping_address }}</span>
                    </div>
                    @if($order->notes)
                        <div>
                            <span class="text-xs text-slate-450 block font-semibold">Catatan Khusus</span>
                            <span class="text-xs italic text-slate-500">{{ $order->notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Status Changer Control Card -->
            @if($order->status !== 'pending_approval' && $order->status !== 'cancelled')
                <div class="bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Perbarui Status Pesanan</h3>
                    
                    <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="space-y-1.5">
                            <label for="status" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Status Logistik</label>
                            <select id="status" name="status" required class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses (Hewan Disiapkan)</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim (Dalam Perjalanan)</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai (Sampai Tujuan)</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Batalkan Pesanan</option>
                            </select>
                        </div>

                        <!-- Tracking Number (Resi) -->
                        <div class="space-y-1.5">
                            <label for="tracking_number" class="text-xs font-bold text-slate-500 uppercase tracking-wider block">No. Resi Armada (Jika Ada)</label>
                            <input type="text" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Contoh: ARMADA-BANYUMAS-01" class="w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-xs focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-2xl text-xs font-bold bg-[#09422a] hover:bg-[#083a25] text-white shadow-md transition">
                            Perbarui Status Logistik
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
