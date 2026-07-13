@extends('layouts.public')

@section('title', 'Detail Pesanan ' . $order->invoice_number . ' - ARI FARM')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row items-center justify-between mb-10 gap-4">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 mb-2">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-3xl font-extrabold font-display text-slate-900 dark:text-white flex items-center gap-2">
                    Pesanan #{{ $order->invoice_number }}
                </h1>
                <span class="text-xs text-slate-400 block mt-0.5">Dipesan pada {{ $order->created_at->format('d F Y, H:i') }} WIB</span>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto">
                {{-- Tombol Batalkan Pesanan: hanya muncul saat pending_approval atau pending_payment --}}
                @if(in_array($order->status, ['pending_approval', 'pending_payment']))
                    <button type="button" onclick="openCancelModal()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 dark:border-rose-900 text-sm font-bold text-rose-600 dark:text-rose-400 transition">
                        <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                    </button>
                @endif

                <a href="{{ route('orders.invoice', $order->id) }}" target="_blank"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-sm font-semibold text-slate-700 dark:text-slate-300 bg-white hover:bg-slate-50 transition">
                    <i class="fa-solid fa-print mr-2 text-slate-400"></i> Cetak Invoice
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Side: Order Status Timeline, Receipt Upload & Items -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Timeline status bar -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">Status Pemesanan</h2>
                    
                    <!-- Progress Timeline (Indonesian) -->
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 relative">
                        <!-- Step 1: Dibuat -->
                        <div class="text-center space-y-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 text-sm font-bold ring-4 ring-emerald-50 dark:ring-emerald-900/30">1</span>
                            <h4 class="text-xs font-bold text-slate-900 dark:text-white">Pesanan Dibuat</h4>
                            <span class="text-[10px] text-slate-400 block">Menunggu Persetujuan</span>
                        </div>

                        <!-- Step 2: Bayar -->
                        @php
                            $step2Active = in_array($order->status, ['pending_payment', 'pending_verification', 'processing', 'shipped', 'completed']);
                        @endphp
                        <div class="text-center space-y-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step2Active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 ring-4 ring-emerald-50 dark:ring-emerald-900/30' : 'bg-slate-100 text-slate-400 dark:bg-slate-850 dark:text-slate-650' }}">2</span>
                            <h4 class="text-xs font-bold {{ $step2Active ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">Menunggu Pembayaran</h4>
                            <span class="text-[10px] text-slate-400 block">Pembayaran Tagihan</span>
                        </div>

                        <!-- Step 3: Verifikasi -->
                        @php
                            $step3Active = in_array($order->status, ['pending_verification', 'processing', 'shipped', 'completed']);
                        @endphp
                        <div class="text-center space-y-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step3Active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 ring-4 ring-emerald-50 dark:ring-emerald-900/30' : 'bg-slate-100 text-slate-400 dark:bg-slate-850 dark:text-slate-650' }}">3</span>
                            <h4 class="text-xs font-bold {{ $step3Active ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">Verifikasi Transfer</h4>
                            <span class="text-[10px] text-slate-400 block">Pengecekan Bukti</span>
                        </div>

                        <!-- Step 4: Diproses -->
                        @php
                            $step4Active = in_array($order->status, ['processing', 'shipped', 'completed']);
                            $step4Label = $order->shipping_method === 'diambil' ? 'Siap Diambil' : 'Dalam Pengiriman';
                            $step4Sub = $order->shipping_method === 'diambil' ? 'Silakan Datang' : 'Menuju Alamat';
                        @endphp
                        <div class="text-center space-y-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step4Active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 ring-4 ring-emerald-50 dark:ring-emerald-900/30' : 'bg-slate-100 text-slate-400 dark:bg-slate-850 dark:text-slate-650' }}">4</span>
                            <h4 class="text-xs font-bold {{ $step4Active ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">{{ $step4Label }}</h4>
                            <span class="text-[10px] text-slate-400 block">{{ $step4Sub }}</span>
                        </div>

                        <!-- Step 5: Selesai -->
                        @php
                            $step5Active = $order->status === 'completed';
                        @endphp
                        <div class="text-center space-y-2">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step5Active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 ring-4 ring-emerald-50 dark:ring-emerald-900/30' : 'bg-slate-100 text-slate-400 dark:bg-slate-850 dark:text-slate-650' }}">5</span>
                            <h4 class="text-xs font-bold {{ $step5Active ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">Selesai</h4>
                            <span class="text-[10px] text-slate-400 block">Transaksi Selesai</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Receipt Upload / Receipt Preview Box -->
                <!-- Payment Receipt Upload / Receipt Preview Box -->
                    @if($order->status === 'pending_approval')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-[#09422a] dark:text-emerald-400 flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#09422a] dark:bg-emerald-950/40 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-clock fa-spin"></i>
                            </span> Menunggu Persetujuan Transaksi
                        </h2>
                        <p class="text-sm text-slate-500">
                            Pesanan Anda telah masuk sistem. Saat ini admin sedang memeriksa pesanan Anda. Kami akan menentukan biaya pengiriman (ongkir) jika Anda memilih opsi Diantar Kurir dan menyetujui transaksi Anda.
                        </p>
                        <div class="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100/60 text-xs sm:text-sm text-slate-600 dark:text-slate-400 space-y-1.5">
                            <p class="font-bold text-slate-800 dark:text-slate-200">Rincian Rencana Pesanan:</p>
                            <p>Metode Pengiriman: <strong>{{ $order->shipping_method === 'diambil' ? 'Diambil Sendiri' : 'Diantar Kurir' }}</strong></p>
                            <p>Subtotal Item: <strong>{{ $order->formatted_subtotal }}</strong></p>
                            @if($order->shipping_method === 'diantar')
                                <p class="text-amber-600 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-exclamation"></i> Menunggu penentuan ongkos kirim oleh admin.
                                </p>
                            @else
                                <p class="text-emerald-600 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check"></i> Bebas biaya ongkir (diambil ke kandang).
                                </p>
                            @endif
                        </div>

                        {{-- Tombol Batalkan di dalam section --}}
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="openCancelModal()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 dark:border-rose-900 text-sm font-bold text-rose-600 dark:text-rose-400 transition">
                                <i class="fa-solid fa-ban"></i> Batalkan Pesanan Ini
                            </button>
                            <p class="text-xs text-slate-400 mt-2">
                                <i class="fa-solid fa-circle-info mr-1"></i>
                                Pembatalan dapat dilakukan selama pesanan belum diproses admin.
                            </p>
                        </div>
                    </div>
                @elseif($order->status === 'cancelled')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-rose-600 flex items-center">
                            <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-455 mr-2.5">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </span> Pesanan Dibatalkan
                        </h2>
                        <p class="text-sm text-slate-500">Pesanan ini telah ditolak atau dibatalkan oleh admin ARI FARM. Silakan hubungi tim kami jika Anda merasa ini adalah kekeliruan.</p>
                    </div>
                @elseif($order->status === 'pending_payment')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center">
                                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 mr-2.5">
                                    <i class="fa-solid fa-wallet"></i>
                                </span> Selesaikan Pembayaran
                            </h2>
                            <p class="text-xs text-slate-400 mt-1">Silakan pilih metode pembayaran otomatis yang instan atau transfer manual.</p>
                        </div>

                        <!-- Amount Display Card -->
                        <div class="bg-[#09422a]/5 dark:bg-emerald-950/10 border border-[#09422a]/10 dark:border-emerald-900/30 p-5 rounded-2xl">
                            @if($order->payment_type === 'dp')
                                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Uang Muka / DP (30%) yang Harus Ditransfer</span>
                                <span class="text-3xl font-black text-rose-600 dark:text-rose-400 block mt-1">{{ $order->formatted_dp_amount }}</span>
                                <span class="text-[10px] text-slate-400 block mt-1.5">(Total tagihan pesanan: {{ $order->formatted_total }}, sisa pelunasan: {{ $order->formatted_remaining_balance }})</span>
                            @else
                                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wider">Total Tagihan (100% Lunas)</span>
                                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400 block mt-1">{{ $order->formatted_total }}</span>
                            @endif
                            <span class="text-[10px] text-slate-400 block mt-1">(Sudah termasuk biaya pengiriman: {{ $order->formatted_shipping_cost }})</span>
                        </div>

                        <!-- Payment Method Toggle Tabs -->
                        <div class="flex border-b border-slate-150 dark:border-slate-800 gap-2 p-1 bg-slate-50 dark:bg-slate-950 rounded-2xl">
                            <button type="button" id="tab-automatic-btn" onclick="switchPaymentTab('automatic')"
                                    class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-[#09422a] dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-850">
                                <i class="fa-solid fa-bolt-lightning text-amber-500"></i> Bayar Otomatis (Instan)
                            </button>
                            <button type="button" id="tab-manual-btn" onclick="switchPaymentTab('manual')"
                                    class="flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-medium transition flex items-center justify-center gap-2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                                <i class="fa-solid fa-file-invoice-dollar"></i> Transfer Manual (Upload Bukti)
                            </button>
                        </div>

                        <!-- Tab Content: Automatic / Midtrans -->
                        <div id="tab-automatic-content" class="space-y-6">
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/40 dark:border-amber-900/40 rounded-2xl text-xs sm:text-sm text-amber-800 dark:text-amber-300">
                                <p class="flex items-start gap-2">
                                    <i class="fa-solid fa-circle-info mt-0.5 text-amber-500 shrink-0"></i>
                                    <span>
                                        <strong>Rekomendasi</strong>: Bayar instan menggunakan QRIS, GoPay, ShopeePay, Virtual Account (BCA, Mandiri, BNI, BRI), atau Kartu Kredit. Transaksi Anda akan diverifikasi otomatis oleh sistem dalam hitungan detik.
                                    </span>
                                </p>
                            </div>

                            <div class="flex flex-col items-center justify-center py-6 px-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                                <!-- Midtrans Merchant Logos for premium feel -->
                                <div class="flex flex-wrap items-center justify-center gap-4 opacity-75 mb-6">
                                    <span class="px-2 py-1 bg-white dark:bg-slate-900 rounded-lg text-[9px] font-bold border border-slate-150 dark:border-slate-800">QRIS</span>
                                    <span class="px-2 py-1 bg-white dark:bg-slate-900 rounded-lg text-[9px] font-bold border border-slate-150 dark:border-slate-800">GOPAY</span>
                                    <span class="px-2 py-1 bg-white dark:bg-slate-900 rounded-lg text-[9px] font-bold border border-slate-150 dark:border-slate-800">SHOPEEPAY</span>
                                    <span class="px-2 py-1 bg-white dark:bg-slate-900 rounded-lg text-[9px] font-bold border border-slate-150 dark:border-slate-800">VIRTUAL ACCOUNT</span>
                                    <span class="px-2 py-1 bg-white dark:bg-slate-900 rounded-lg text-[9px] font-bold border border-slate-150 dark:border-slate-800">CREDIT CARD</span>
                                </div>

                                <button type="button" id="pay-button" onclick="payWithMidtrans()"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-xl bg-[#09422a] hover:bg-[#073521] text-white font-bold text-base shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition active:scale-95">
                                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                                </button>
                                
                                <p class="text-xs text-slate-400 mt-3 text-center">
                                    <i class="fa-solid fa-lock mr-1 text-emerald-600"></i> Pembayaran dienkripsi secara aman oleh Midtrans.
                                </p>
                            </div>
                        </div>

                        <!-- Tab Content: Manual -->
                        <div id="tab-manual-content" class="hidden space-y-6">
                            <!-- Info instructions -->
                            <div class="bg-slate-50 dark:bg-slate-950 border border-slate-150 dark:border-slate-800/40 p-4 rounded-2xl text-xs sm:text-sm text-slate-600 dark:text-slate-400 space-y-2">
                                <p class="font-bold text-slate-800 dark:text-slate-200">Panduan Transfer Manual:</p>
                                <p>Transfer ke salah satu rekening PT ARI FARM Indonesia berikut:</p>
                                <ul class="list-disc list-inside mt-1.5 space-y-1 text-slate-700 dark:text-slate-300 font-medium">
                                    <li>BSI: <strong class="text-[#09422a] dark:text-emerald-400">712-3456-789</strong></li>
                                    <li>Mandiri: <strong class="text-[#09422a] dark:text-emerald-400">139-00-1234-5678</strong></li>
                                </ul>
                                <p class="text-xs text-slate-450 italic mt-2">
                                    Catatan: Verifikasi transfer manual memerlukan waktu 1x24 jam kerja karena diperiksa secara manual oleh tim admin.
                                </p>
                            </div>

                            <!-- Upload Form -->
                            <form action="{{ route('orders.upload-receipt', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="space-y-2">
                                    <label for="payment_receipt" class="text-xs font-bold text-slate-500 block uppercase tracking-wider">File Gambar Bukti Transfer (JPG, PNG, WEBP, Maks 3MB)</label>
                                    <input type="file" id="payment_receipt" name="payment_receipt" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 dark:border-slate-800 rounded-xl p-2 bg-slate-50/50">
                                </div>
                                <button type="submit" class="inline-flex px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/10 transition">
                                    Kirim Bukti Transfer
                                </button>
                            </form>
                        </div>

                        {{-- Divider & tombol batalkan --}}
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-500 mb-0.5">Ingin membatalkan pesanan?</p>
                                <p class="text-xs text-slate-400">Pembatalan masih bisa dilakukan sebelum pembayaran diverifikasi.</p>
                            </div>
                            <button type="button" onclick="openCancelModal()"
                                    class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 dark:bg-rose-950/30 dark:hover:bg-rose-950/50 dark:border-rose-900 text-sm font-bold text-rose-600 dark:text-rose-400 transition">
                                <i class="fa-solid fa-ban"></i> Batalkan Pesanan
                            </button>
                        </div>
                    </div>
                @elseif($order->status === 'pending_verification')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="p-1.5 rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400 mr-2.5">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span> Menunggu Verifikasi Pembayaran
                        </h2>
                        <p class="text-sm text-slate-500">Bukti pembayaran Anda sudah dikirim dan sedang diperiksa oleh admin kami. Kami akan memperbarui status pemesanan Anda sesegera mungkin.</p>
                        
                        <!-- Uploaded Preview -->
                        <div class="space-y-2 pt-2">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Gambar Bukti Anda</span>
                            <div class="max-w-[200px] border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
                                <a href="{{ asset($order->payment_receipt) }}" target="_blank">
                                    <img src="{{ asset($order->payment_receipt) }}" class="w-full object-cover">
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($order->status === 'processing')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-circle-check"></i>
                            </span> Pembayaran Terverifikasi
                        </h2>
                        <p class="text-sm text-slate-500">Pembayaran untuk order ini telah disetujui. Admin sedang memproses pesanan dan mempersiapkan hewan untuk dikirim atau diambil.</p>
                    </div>
                @elseif($order->status === 'shipped')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-[#09422a] dark:text-emerald-400 flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#09422a] dark:bg-emerald-950/40 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-truck-fast"></i>
                            </span> Pesanan Sedang Dikirim
                        </h2>
                        <p class="text-sm text-slate-500">
                            Hewan pesanan Anda saat ini sedang dalam perjalanan menuju alamat pengiriman. Jika hewan pesanan Anda sudah sampai dengan selamat, mohon lakukan konfirmasi penerimaan di bawah ini.
                        </p>
                        @if($order->tracking_number)
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-100/60 text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                Info Pengiriman / No. Kurir: <strong>{{ $order->tracking_number }}</strong>
                            </div>
                        @endif

                        <form action="{{ route('orders.confirm', $order->id) }}" method="POST" class="pt-2 border-t border-slate-100 dark:border-slate-800">
                            @csrf
                            <button type="submit" class="inline-flex px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/10 transition">
                                <i class="fa-solid fa-circle-check mr-2"></i> Konfirmasi Kambing Sudah Sampai
                            </button>
                        </form>
                    </div>
                @elseif($order->status === 'completed')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-[#09422a] dark:text-emerald-400 flex items-center">
                            <span class="p-1.5 rounded-lg bg-emerald-50 text-[#09422a] dark:bg-emerald-950/40 dark:text-emerald-400 mr-2.5">
                                <i class="fa-solid fa-handshake"></i>
                            </span> Pesanan Selesai
                        </h2>
                        <p class="text-sm text-slate-500">Terima kasih banyak telah mempercayakan kebutuhan ternak Anda kepada **ARI FARM**. Transaksi ini telah dinyatakan selesai karena hewan pesanan Anda telah sampai/diambil.</p>
                    </div>
                @endif

                <!-- Items Ordered List -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider">Item Dipesan</h2>
                    
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($order->items as $item)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 first:pt-0 last:pb-0 gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-950 flex-shrink-0">
                                        @if($item->goat)
                                            <img src="{{ asset($item->goat->first_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300 text-2xl">
                                                <i class="fa-solid fa-sheep"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $item->goat?->name ?? '(Data kambing telah dihapus)' }}
                                        </h3>
                                        <span class="text-xs text-slate-400">
                                            Kategori: {{ $item->goat?->category?->name ?? '-' }} | Umur: {{ $item->goat?->age_months ?? '-' }} Bln | Bobot: {{ $item->goat?->weight_kg ?? '-' }} kg
                                        </span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <span class="text-xs text-slate-400 block">{{ $item->quantity }} x Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</span>
                                    <span class="font-bold text-sm text-slate-900 dark:text-white">
                                        {{ $item->formatted_subtotal }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-sheep text-slate-300 dark:text-slate-600 text-2xl"></i>
                                </div>
                                <p class="font-semibold text-slate-500 dark:text-slate-400 text-sm">Data item pesanan tidak tersedia</p>
                                <p class="text-xs text-slate-400 mt-1">Detail item untuk transaksi ini tidak ditemukan dalam sistem.<br>Silakan hubungi admin untuk informasi lebih lanjut.</p>
                                <div class="mt-4 px-4 py-2.5 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/40 rounded-xl text-xs text-amber-700 dark:text-amber-400 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-info shrink-0"></i>
                                    <span>Total tagihan: <strong>{{ $order->formatted_total }}</strong></span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Shipping & Cost Summaries -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Shipping Address Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pengiriman & Penerima</h3>
                    <div class="space-y-3 text-sm">
                        <div class="space-y-0.5">
                            <span class="text-xs text-slate-400 block">Metode Pengiriman</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">
                                {{ $order->shipping_method === 'diambil' ? 'Diambil Sendiri (Pick Up)' : 'Diantar Kurir (Delivery)' }}
                            </span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs text-slate-400 block">Nama Penerima</span>
                            <span class="font-bold text-slate-850 dark:text-slate-200">{{ $order->user?->name ?? 'Pelanggan' }}</span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs text-slate-400 block">No. Telepon / WA</span>
                            <span class="font-semibold text-slate-850 dark:text-slate-200">{{ $order->phone_number }}</span>
                        </div>
                        <div class="space-y-0.5">
                            <span class="text-xs text-slate-400 block">Alamat / Lokasi Penyerahan</span>
                            <span class="text-slate-650 dark:text-slate-400 leading-relaxed">{{ $order->shipping_address }}</span>
                        </div>
                        @if($order->notes)
                            <div class="space-y-0.5 pt-2 border-t border-slate-100 dark:border-slate-800">
                                <span class="text-xs text-slate-400 block">Catatan Pesanan</span>
                                <span class="text-xs italic text-slate-500">{{ $order->notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Logistics / Delivery Details -->
                @if($order->status === 'shipped' || $order->status === 'completed')
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Informasi Logistik</h3>
                        <div class="space-y-3 text-sm">
                            <div class="space-y-0.5">
                                <span class="text-xs text-slate-400 block">Metode Pengiriman</span>
                                <span class="font-bold text-slate-850 dark:text-slate-200">
                                    {{ $order->shipping_method === 'diambil' ? 'Diambil Langsung ke Kandang' : 'Armada Pengiriman ARI FARM' }}
                                </span>
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-xs text-slate-400 block">No. Resi Pelacakan / Kurir</span>
                                <span class="font-black text-emerald-600 select-all">{{ $order->tracking_number ?: 'Telah di perjalanan' }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Payment Summary Box -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal Item</span>
                            <span class="font-semibold text-slate-850 dark:text-slate-200">{{ $order->formatted_subtotal }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">
                                Biaya Pengiriman
                                @if($order->shipping_method === 'diantar' && !is_null($order->shipping_distance))
                                    <span class="text-xs text-slate-400 block">
                                        Jarak: {{ $order->shipping_distance }} km
                                        @if($order->is_wonosobo)
                                            (Diskon Wonosobo 20% aktif)
                                        @endif
                                    </span>
                                @endif
                            </span>
                            <span class="font-semibold text-slate-850 dark:text-slate-200">
                                {{ $order->formatted_shipping_cost }}
                            </span>
                        </div>
                        <hr class="border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between items-baseline pt-2">
                            <span class="font-bold text-slate-900 dark:text-white">Total Tagihan</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-lg">
                                {{ $order->formatted_total }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-xs pt-1">
                            <span class="text-slate-400">Tipe Pembayaran</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">
                                {{ $order->payment_type === 'dp' ? 'Uang Muka / DP (30%)' : 'Lunas (100%)' }}
                            </span>
                        </div>
                        @if($order->payment_type === 'dp')
                            <div class="flex justify-between items-baseline text-xs text-rose-600 dark:text-rose-400">
                                <span>Nominal Uang Muka (DP)</span>
                                <span class="font-bold text-sm">{{ $order->formatted_dp_amount }}</span>
                            </div>
                            <div class="flex justify-between items-baseline text-xs text-slate-500">
                                <span>Sisa Pelunasan</span>
                                <span class="font-bold">{{ $order->formatted_remaining_balance }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- Cancel Confirmation Modal --}}
@if(in_array($order->status, ['pending_approval', 'pending_payment']))
<div id="cancel-modal" class="fixed inset-0 z-[100] flex items-center justify-center hidden">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCancelModal()"></div>
    <div class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 w-full max-w-md mx-4 border border-slate-200 dark:border-slate-800 z-10">

        {{-- Icon + Title --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-rose-100 dark:bg-rose-950/40 flex items-center justify-center text-rose-600 text-2xl">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Batalkan Pesanan?</h3>
                <p class="text-sm text-slate-500 mt-0.5">Tindakan ini tidak dapat diurungkan.</p>
            </div>
        </div>

        {{-- Warning Box --}}
        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900 rounded-2xl px-5 py-4 mb-6 space-y-2">
            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                Pesanan <span class="text-rose-600 dark:text-rose-400">{{ $order->invoice_number }}</span> akan dibatalkan.
            </p>
            <ul class="text-xs text-slate-600 dark:text-slate-400 space-y-1.5">
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                    Stok kambing yang dipesan akan dikembalikan ke inventaris.
                </li>
                @if($order->status === 'pending_payment')
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500 mt-0.5 shrink-0"></i>
                    Bukti pembayaran yang sudah diunggah (jika ada) akan dihapus.
                </li>
                @endif
                <li class="flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
                    Pembatalan <strong>tidak dapat diurungkan</strong> setelah dikonfirmasi.
                </li>
            </ul>
        </div>

        {{-- Action Buttons --}}
        <form method="POST" action="{{ route('orders.cancel', $order->id) }}">
            @csrf
            <div class="flex gap-3">
                <button type="button" onclick="closeCancelModal()"
                        class="flex-1 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                    Tidak, Kembali
                </button>
                <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-md shadow-rose-900/20 transition">
                    <i class="fa-solid fa-ban mr-1.5"></i> Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelModal() {
    document.getElementById('cancel-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeCancelModal() {
    document.getElementById('cancel-modal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeCancelModal();
});

// Switch Payment Tabs
function switchPaymentTab(tab) {
    const autoBtn = document.getElementById('tab-automatic-btn');
    const manualBtn = document.getElementById('tab-manual-btn');
    const autoContent = document.getElementById('tab-automatic-content');
    const manualContent = document.getElementById('tab-manual-content');

    if (tab === 'automatic') {
        autoBtn.className = "flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-[#09422a] dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-850";
        manualBtn.className = "flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-medium transition flex items-center justify-center gap-2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300";
        autoContent.classList.remove('hidden');
        manualContent.classList.add('hidden');
    } else {
        manualBtn.className = "flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 bg-white dark:bg-slate-900 text-[#09422a] dark:text-emerald-400 shadow-sm border border-slate-200/50 dark:border-slate-850";
        autoBtn.className = "flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-medium transition flex items-center justify-center gap-2 text-slate-500 hover:text-slate-700 dark:hover:text-slate-300";
        manualContent.classList.remove('hidden');
        autoContent.classList.add('hidden');
    }
}
</script>

@if($order->status === 'pending_payment')
<!-- Load Midtrans Snap JS -->
@php
    $snapUrl = config('services.midtrans.is_production') 
        ? 'https://app.midtrans.com/snap/snap.js' 
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key');
@endphp
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script>
function payWithMidtrans() {
    const payBtn = document.getElementById('pay-button');
    const originalText = payBtn.innerHTML;
    
    // Disable button and show loading state
    payBtn.disabled = true;
    payBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Menghubungkan...';

    // Retrieve Snap Token from backend AJAX endpoint
    fetch("{{ route('orders.snap-token', $order->id) }}")
        .then(response => response.json())
        .then(data => {
            payBtn.disabled = false;
            payBtn.innerHTML = originalText;

            if (data.error) {
                alert(data.error);
                return;
            }

            // Trigger Midtrans Snap Popup
            window.snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    window.location.href = "{{ route('payment.midtrans.finish') }}?order_id=" + result.order_id + "&status_code=" + result.status_code + "&transaction_status=" + result.transaction_status;
                },
                onPending: function(result) {
                    window.location.href = "{{ route('payment.midtrans.finish') }}?order_id=" + result.order_id + "&status_code=" + result.status_code + "&transaction_status=" + result.transaction_status;
                },
                onError: function(result) {
                    window.location.href = "{{ route('payment.midtrans.finish') }}?order_id=" + result.order_id + "&status_code=" + result.status_code + "&transaction_status=" + result.transaction_status;
                },
                onClose: function() {
                    // Let user trigger payment popup again if they close it
                }
            });
        })
        .catch(error => {
            payBtn.disabled = false;
            payBtn.innerHTML = originalText;
            console.error('Error fetching snap token:', error);
            alert('Gagal memicu pembayaran. Silakan coba beberapa saat lagi atau hubungi admin.');
        });
}
</script>
@endif
@endif
@endsection
