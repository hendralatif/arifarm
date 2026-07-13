@extends('layouts.public')

@section('title', 'Pembayaran Tagihan Anda - ARI FARM')

@section('content')
<div class="py-12 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Page Header --}}
        <div class="mb-10 text-center sm:text-left">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white font-display">Informasi & Pembayaran Tagihan</h1>
            <p class="text-sm text-slate-500 mt-2">Selesaikan tagihan pemesanan kambing & domba Anda secara amanah.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Column 1 & 2: Tagihan Aktif --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2 mb-6">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Tagihan Pemesanan Aktif
                    </h2>

                    @forelse($orders as $order)
                        <div class="p-5 rounded-2xl border border-slate-100 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-900/60 flex flex-col md:flex-row justify-between gap-5 mb-5 last:mb-0">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-extrabold text-slate-550 dark:text-slate-400">#{{ $order->invoice_number }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-500 dark:text-slate-450 space-y-1">
                                    <p>Tanggal Pesan: <strong>{{ $order->created_at->format('d M Y, H:i') }}</strong></p>
                                    <p>Metode Pengiriman: <strong>{{ $order->shipping_method === 'diambil' ? 'Diambil Sendiri' : 'Diantar' }}</strong></p>
                                    <p>Total Tagihan: <strong class="text-emerald-700 dark:text-emerald-450 text-sm font-extrabold">{{ $order->formatted_total }}</strong> (termasuk ongkir)</p>
                                </div>
                            </div>

                            <div class="flex flex-col justify-center min-w-[200px] border-t md:border-t-0 md:border-l border-slate-150 dark:border-slate-750 pt-4 md:pt-0 md:pl-5 space-y-3">
                                @if($order->status === 'pending_approval')
                                    <div class="text-xs text-slate-500 bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900 rounded-xl p-3 flex gap-2">
                                        <i class="fa-solid fa-clock mt-0.5 text-amber-600 shrink-0"></i>
                                        <span>Menunggu persetujuan admin untuk ongkos kirim. Silakan periksa kembali berkala.</span>
                                    </div>
                                @elseif($order->status === 'pending_payment')
                                    {{-- Payment Method Tabs --}}
                                    <div class="space-y-3">
                                        {{-- Tab Switcher --}}
                                        <div class="flex gap-1.5 p-1 bg-slate-100 dark:bg-slate-950 rounded-xl">
                                            <button type="button" id="tab-auto-btn-{{ $order->id }}" onclick="switchTab('{{ $order->id }}', 'auto')"
                                                class="flex-1 py-2 px-2 rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1 bg-white dark:bg-slate-800 text-[#09422a] dark:text-emerald-400 shadow-sm">
                                                <i class="fa-solid fa-bolt-lightning text-amber-500"></i> Instan
                                            </button>
                                            <button type="button" id="tab-manual-btn-{{ $order->id }}" onclick="switchTab('{{ $order->id }}', 'manual')"
                                                class="flex-1 py-2 px-2 rounded-lg text-[10px] font-medium transition flex items-center justify-center gap-1 text-slate-500 hover:text-slate-700">
                                                <i class="fa-solid fa-file-invoice-dollar"></i> Transfer
                                            </button>
                                        </div>

                                        {{-- Auto / Midtrans Tab --}}
                                        <div id="tab-auto-content-{{ $order->id }}">
                                            @php $midtransKey = config('services.midtrans.client_key'); @endphp
                                            @if(empty($midtransKey))
                                                <div class="text-xs text-slate-500 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-center">
                                                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mr-1"></i>
                                                    Pembayaran otomatis belum dikonfigurasi. Gunakan transfer manual.
                                                </div>
                                            @else
                                                <button type="button" id="pay-btn-{{ $order->id }}" onclick="payWithMidtrans('{{ $order->id }}', '{{ route('orders.snap-token', $order->id) }}')"
                                                    class="w-full py-2.5 bg-[#09422a] hover:bg-[#083a25] text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                                                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang (QRIS / VA / e-Wallet)
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Manual Upload Tab --}}
                                        <div id="tab-manual-content-{{ $order->id }}" class="hidden">
                                            <form method="POST" action="{{ route('orders.upload-receipt', $order->id) }}" enctype="multipart/form-data" class="space-y-2">
                                                @csrf
                                                <input type="file" name="payment_receipt" required accept="image/*"
                                                    class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-slate-800 dark:file:text-slate-300 cursor-pointer">
                                                <button type="submit" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                                                    <i class="fa-solid fa-cloud-arrow-up"></i> Kirim Bukti Transfer
                                                </button>
                                            </form>
                                        </div>

                                        <a href="{{ route('orders.show', $order->id) }}" class="block text-center text-[10px] text-slate-400 hover:text-slate-600 transition">
                                            <i class="fa-solid fa-eye mr-1"></i> Lihat Detail Pesanan Lengkap
                                        </a>
                                    </div>

                                @elseif($order->status === 'pending_verification')
                                    <div class="text-xs text-emerald-800 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-150 dark:border-emerald-900 rounded-xl p-3 flex gap-2">
                                        <i class="fa-solid fa-check-circle mt-0.5 text-emerald-600 shrink-0"></i>
                                        <span>Bukti transfer terkirim. Admin sedang memverifikasi pembayaran Anda.</span>
                                    </div>
                                    <a href="{{ route('orders.show', $order->id) }}" class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5 border border-slate-200/50">
                                        <i class="fa-solid fa-eye"></i> Detail Transaksi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-14 text-slate-400">
                            <i class="fa-solid fa-wallet text-4xl block mb-3 opacity-30"></i>
                            <p class="font-semibold">Tidak ada tagihan pembayaran aktif saat ini.</p>
                            <a href="{{ route('catalog') }}" class="inline-flex items-center gap-1.5 text-xs text-[#09422a] dark:text-emerald-400 font-bold hover:underline mt-2">
                                Mulai belanja di Katalog <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Column 3: Rekening Bank & Panduan --}}
            <div class="space-y-6">
                
                {{-- Bank Accounts --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200/60 dark:border-slate-700/60 rounded-3xl p-6 shadow-sm space-y-5">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-750 pb-3">
                        <i class="fa-solid fa-building-columns text-indigo-500"></i> Rekening Pembayaran
                    </h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Silakan transfer pembayaran pemesanan Anda ke rekening bank resmi Ari Farm berikut:</p>
                    
                    <div class="space-y-4">
                        {{-- BCA --}}
                        <div class="p-3.5 rounded-2xl border border-slate-150 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-900/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold text-blue-650 bg-blue-50 px-2 py-0.5 rounded">BCA</span>
                                <p class="text-sm font-extrabold text-slate-800 dark:text-white mt-1" id="rek-bca">0912-3456-7890</p>
                                <p class="text-[10px] text-slate-400">a.n. PT ARI FARM INDONESIA</p>
                            </div>
                            <button onclick="copyToClipboard('rek-bca', this)" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>

                        {{-- Mandiri --}}
                        <div class="p-3.5 rounded-2xl border border-slate-150 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-900/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">MANDIRI</span>
                                <p class="text-sm font-extrabold text-slate-800 dark:text-white mt-1" id="rek-mandiri">138-00-1234-5678</p>
                                <p class="text-[10px] text-slate-400">a.n. PT ARI FARM INDONESIA</p>
                            </div>
                            <button onclick="copyToClipboard('rek-mandiri', this)" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>

                        {{-- BRI --}}
                        <div class="p-3.5 rounded-2xl border border-slate-150 dark:border-slate-750 bg-slate-50/50 dark:bg-slate-900/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold text-blue-500 bg-blue-50 px-2 py-0.5 rounded">BRI</span>
                                <p class="text-sm font-extrabold text-slate-800 dark:text-white mt-1" id="rek-bri">0021-01-000123-50-4</p>
                                <p class="text-[10px] text-slate-400">a.n. PT ARI FARM INDONESIA</p>
                            </div>
                            <button onclick="copyToClipboard('rek-bri', this)" class="p-2 rounded-xl text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-200/50 dark:hover:bg-slate-800 transition">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Panduan --}}
                <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-900 rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600">
                            <i class="fa-solid fa-circle-question text-sm"></i>
                        </div>
                        <h4 class="font-extrabold text-slate-800 dark:text-white text-sm">Petunjuk Transfer</h4>
                    </div>
                    <ol class="space-y-3.5 text-xs text-slate-650 dark:text-slate-400 list-decimal pl-4">
                        <li>Lakukan transfer sesuai total nominal tagihan (termasuk ongkir jika ada).</li>
                        <li>Gunakan nomor invoice pemesanan sebagai catatan transfer jika memungkinkan.</li>
                        <li>Pastikan bukti transfer dalam format foto/gambar JPG/PNG dengan nominal terbaca jelas.</li>
                        <li>Kirim bukti tersebut ke formulir pesanan aktif di sebelah kiri untuk proses verifikasi kilat admin.</li>
                    </ol>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function copyToClipboard(id, button) {
    const text = document.getElementById(id).textContent;
    navigator.clipboard.writeText(text).then(() => {
        const icon = button.querySelector('i');
        icon.className = 'fa-solid fa-check text-emerald-500';
        setTimeout(() => {
            icon.className = 'fa-solid fa-copy';
        }, 1500);
    });
}

function switchTab(orderId, tab) {
    const autoBtn    = document.getElementById('tab-auto-btn-' + orderId);
    const manualBtn  = document.getElementById('tab-manual-btn-' + orderId);
    const autoContent   = document.getElementById('tab-auto-content-' + orderId);
    const manualContent = document.getElementById('tab-manual-content-' + orderId);

    if (tab === 'auto') {
        autoBtn.className    = 'flex-1 py-2 px-2 rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1 bg-white dark:bg-slate-800 text-[#09422a] dark:text-emerald-400 shadow-sm';
        manualBtn.className  = 'flex-1 py-2 px-2 rounded-lg text-[10px] font-medium transition flex items-center justify-center gap-1 text-slate-500 hover:text-slate-700';
        autoContent.classList.remove('hidden');
        manualContent.classList.add('hidden');
    } else {
        manualBtn.className  = 'flex-1 py-2 px-2 rounded-lg text-[10px] font-bold transition flex items-center justify-center gap-1 bg-white dark:bg-slate-800 text-[#09422a] dark:text-emerald-400 shadow-sm';
        autoBtn.className    = 'flex-1 py-2 px-2 rounded-lg text-[10px] font-medium transition flex items-center justify-center gap-1 text-slate-500 hover:text-slate-700';
        manualContent.classList.remove('hidden');
        autoContent.classList.add('hidden');
    }
}

function payWithMidtrans(orderId, snapUrl) {
    const btn = document.getElementById('pay-btn-' + orderId);
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Menghubungkan...';

    fetch(snapUrl, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (data.error) {
            alert('Gagal: ' + data.error);
            return;
        }
        window.snap.pay(data.snap_token, {
            onSuccess: function(result) {
                window.location.href = '{{ route("payment.midtrans.finish") }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
            },
            onPending: function(result) {
                window.location.href = '{{ route("payment.midtrans.finish") }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
            },
            onError: function(result) {
                window.location.href = '{{ route("payment.midtrans.finish") }}?order_id=' + result.order_id + '&status_code=' + result.status_code + '&transaction_status=' + result.transaction_status;
            },
            onClose: function() { /* user closed popup */ }
        });
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        alert('Koneksi gagal. Coba lagi atau gunakan transfer manual.');
    });
}
</script>

@php
    $hasPendingPayment = $orders->contains('status', 'pending_payment');
    $snapUrl = config('services.midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $clientKey = config('services.midtrans.client_key');
@endphp

@if($hasPendingPayment && !empty($clientKey))
<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
@endif
@endsection

