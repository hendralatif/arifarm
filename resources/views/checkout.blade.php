@extends('layouts.public')

@section('title', 'Checkout Pembayaran - Ari Farm')

@section('content')
<section class="py-12 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold font-display text-slate-900 dark:text-white">Formulir Checkout</h1>
            <p class="text-slate-500 text-sm mt-1">Lengkapi alamat pengiriman dan informasi detail pemesanan hewan ternak Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Checkout Form & Bank Instructions -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Address Form Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 mr-2.5">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </span> Detail Pengiriman Hewan
                    </h2>

                    <form id="checkout-form" action="{{ route('orders.store') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Phone Number -->
                        <div class="space-y-1">
                            <label for="phone_number" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">No. Telepon / WhatsApp</label>
                            <input type="text" id="phone_number" name="phone_number" required placeholder="Contoh: 081234567890" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <!-- Shipping Method -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Metode Pengiriman</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 cursor-pointer focus:outline-none select-none hover:border-emerald-500/50 transition">
                                    <input type="radio" name="shipping_method" value="diantar" checked class="sr-only" id="shipping-method-diantar">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-xl">
                                            <i class="fa-solid fa-truck text-base"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900 dark:text-white">Diantar Kurir</span>
                                            <span class="block text-xs text-slate-400">Ongkir akan dihitung oleh admin</span>
                                        </div>
                                    </div>
                                    <div class="absolute right-4 top-4 text-emerald-600 select-icon hidden">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </label>
                                
                                <label class="relative flex p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 cursor-pointer focus:outline-none select-none hover:border-emerald-500/50 transition">
                                    <input type="radio" name="shipping_method" value="diambil" class="sr-only" id="shipping-method-diambil">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-xl">
                                            <i class="fa-solid fa-store text-base"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900 dark:text-white">Diambil Sendiri</span>
                                            <span class="block text-xs text-slate-400">Ambil langsung ke Kandang</span>
                                        </div>
                                    </div>
                                    <div class="absolute right-4 top-4 text-emerald-600 select-icon hidden">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Address -->
                        <div id="address-container" class="space-y-1 transition duration-200">
                            <label for="shipping_address" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat Lengkap Pengiriman</label>
                            <textarea id="shipping_address" name="shipping_address" rows="4" required placeholder="Tuliskan alamat lengkap beserta petunjuk jalan jika diperlukan..." class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                        
                        <!-- Pickup Location Note (initially hidden) -->
                        <div id="pickup-note" class="hidden p-4 rounded-2xl bg-[#09422a]/5 text-[#09422a] border border-[#09422a]/20 dark:bg-emerald-950/10 dark:text-emerald-450 dark:border-emerald-900/30 text-xs sm:text-sm space-y-1">
                            <p class="font-bold"><i class="fa-solid fa-circle-info mr-1.5"></i> Informasi Penjemputan:</p>
                            <p>Hewan dapat diambil langsung di Kandang ARI FARM setelah status pesanan disetujui oleh admin.</p>
                            <p class="font-semibold text-slate-700 dark:text-slate-300 mt-1">Alamat Kandang: Jl. Peternak Raya No. 42, Purwokerto Selatan (Banyumas)</p>
                        </div>

                        <!-- Distance & Wonosobo Area (Visible when method is 'diantar') -->
                        <div id="delivery-details-container" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="shipping_distance" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Jarak Pengiriman (km)</label>
                                <input type="number" id="shipping_distance" name="shipping_distance" min="0" max="9999" placeholder="Contoh: 15" class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div class="flex items-center space-x-2.5 pt-6">
                                <input type="checkbox" id="is_wonosobo" name="is_wonosobo" value="1" class="h-4 w-4 rounded border-slate-200 text-[#09422a] focus:ring-[#09422a] dark:border-slate-800 dark:bg-slate-950">
                                <label for="is_wonosobo" class="text-xs font-bold text-slate-600 dark:text-slate-400 cursor-pointer select-none">
                                    Lokasi di Kab. Wonosobo (Diskon 20%)
                                </label>
                            </div>
                        </div>

                        <!-- Payment Type Option -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Tipe Pembayaran</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <label class="relative flex p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 cursor-pointer focus:outline-none select-none hover:border-emerald-500/50 transition" id="pay-type-label-full">
                                    <input type="radio" name="payment_type" value="full" checked class="sr-only" id="payment-type-full">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-xl">
                                            <i class="fa-solid fa-money-bill-wave text-base"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900 dark:text-white">Pelunasan Penuh</span>
                                            <span class="block text-xs text-slate-400">Bayar 100% dari total tagihan</span>
                                        </div>
                                    </div>
                                    <div class="absolute right-4 top-4 text-emerald-600 select-icon" id="pay-type-check-full">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </label>
                                
                                <label class="relative flex p-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950 cursor-pointer focus:outline-none select-none hover:border-emerald-500/50 transition" id="pay-type-label-dp">
                                    <input type="radio" name="payment_type" value="dp" class="sr-only" id="payment-type-dp">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 p-2 rounded-xl">
                                            <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm font-bold text-slate-900 dark:text-white">Uang Muka / DP</span>
                                            <span class="block text-xs text-slate-400">Bayar 30% terlebih dahulu</span>
                                        </div>
                                    </div>
                                    <div class="absolute right-4 top-4 text-emerald-600 select-icon hidden" id="pay-type-check-dp">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="space-y-1">
                            <label for="notes" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                            <textarea id="notes" name="notes" rows="2" placeholder="Catatan untuk pengiriman, penjemputan, dll..." class="w-full py-2.5 px-3.5 rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        </div>
                    </form>
                </div>

                <!-- Bank Instructions Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400 mr-2.5">
                            <i class="fa-solid fa-credit-card"></i>
                        </span> Instruksi Transfer Bank
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Silakan lakukan pembayaran dengan mentransfer ke salah satu rekening resmi Ari Farm berikut setelah Anda melakukan pemesanan. Simpan bukti transfer Anda untuk diunggah pada dashboard akun Anda.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-2xl bg-slate-50 dark:bg-slate-950 space-y-2">
                            <span class="font-bold text-slate-900 dark:text-white text-sm block">Bank Syariah Indonesia (BSI)</span>
                            <span class="text-lg font-black text-emerald-600 tracking-wider block">712-3456-789</span>
                            <span class="text-xs text-slate-400 block">a.n. PT Ari Farm Indonesia</span>
                        </div>
                        <div class="p-4 border border-slate-100 dark:border-slate-800 rounded-2xl bg-slate-50 dark:bg-slate-950 space-y-2">
                            <span class="font-bold text-slate-900 dark:text-white text-sm block">Bank Mandiri</span>
                            <span class="text-lg font-black text-emerald-600 tracking-wider block">139-00-1234-5678</span>
                            <span class="text-xs text-slate-400 block">a.n. PT Ari Farm Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary Side Card -->
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 shadow-sm space-y-6 sticky top-24">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Detail Pesanan</h2>

                    <!-- Items List -->
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 max-h-60 overflow-y-auto pr-2 space-y-3">
                        @foreach($cart as $id => $item)
                            <div class="flex items-center space-x-3 pt-3 first:pt-0">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-950 flex-shrink-0">
                                    <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $item['name'] }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="border-slate-100 dark:border-slate-800">

                    <!-- Totals -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500" id="shipping-cost-row">
                            <span>Biaya Pengiriman <span id="shipping-distance-label" class="text-xs text-slate-400"></span></span>
                            <span class="font-semibold text-emerald-600 animate-pulse-soft" id="shipping-cost-display">Rp 0</span>
                        </div>
                        <hr class="border-slate-100 dark:border-slate-800">
                        <div class="flex justify-between" id="total-amount-row">
                            <span class="font-bold text-slate-900 dark:text-white">Total Tagihan</span>
                            <span class="font-black text-[#09422a] dark:text-emerald-450 text-base" id="total-amount-display">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between hidden" id="dp-amount-row">
                            <span class="font-bold text-rose-600 dark:text-rose-400">Uang Muka / DP (30%)</span>
                            <span class="font-black text-rose-600 dark:text-rose-400 text-base" id="dp-amount-display">Rp 0</span>
                        </div>
                        <div class="flex justify-between hidden" id="remaining-balance-row">
                            <span class="text-xs font-semibold text-slate-500">Sisa Pelunasan</span>
                            <span class="text-xs font-semibold text-slate-700 dark:text-slate-350" id="remaining-balance-display">Rp 0</span>
                        </div>
                    </div>

                    <!-- Warning for out of range shipping -->
                    <div id="outside-range-warning" class="hidden p-3 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/20 dark:text-amber-300 dark:border-amber-900/40 text-[10px] sm:text-xs leading-relaxed space-y-1">
                        <p class="font-bold"><i class="fa-solid fa-circle-exclamation mr-1"></i> Jarak di Luar Jangkauan ( > 200 km):</p>
                        <p>Biaya pengiriman akan ditentukan manual oleh admin setelah Anda membuat pesanan.</p>
                    </div>

                    <!-- Submit CTA -->
                    <button type="submit" form="checkout-form" class="w-full flex items-center justify-center px-6 py-3.5 rounded-2xl text-base font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-600/10 transition">
                        <i class="fa-solid fa-lock mr-2"></i> Buat Pesanan Sekarang
                    </button>

                    <a href="{{ route('cart.index') }}" class="block text-center text-xs font-semibold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                        Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subtotal = {{ $total }};
        
        // Shipping method elements
        const diantarRadio = document.getElementById('shipping-method-diantar');
        const diambilRadio = document.getElementById('shipping-method-diambil');
        const addressContainer = document.getElementById('address-container');
        const shippingAddressInput = document.getElementById('shipping_address');
        const pickupNote = document.getElementById('pickup-note');
        
        // Distance & Wonosobo inputs
        const deliveryDetailsContainer = document.getElementById('delivery-details-container');
        const shippingDistanceInput = document.getElementById('shipping_distance');
        const isWonosoboCheckbox = document.getElementById('is_wonosobo');
        
        // Payment type elements
        const payTypeFullRadio = document.getElementById('payment-type-full');
        const payTypeDpRadio = document.getElementById('payment-type-dp');
        const payTypeLabelFull = document.getElementById('pay-type-label-full');
        const payTypeLabelDp = document.getElementById('pay-type-label-dp');
        const payTypeCheckFull = document.getElementById('pay-type-check-full');
        const payTypeCheckDp = document.getElementById('pay-type-check-dp');

        // Output display elements
        const shippingDistanceLabel = document.getElementById('shipping-distance-label');
        const shippingCostDisplay = document.getElementById('shipping-cost-display');
        const totalAmountDisplay = document.getElementById('total-amount-display');
        const dpAmountRow = document.getElementById('dp-amount-row');
        const dpAmountDisplay = document.getElementById('dp-amount-display');
        const remainingBalanceRow = document.getElementById('remaining-balance-row');
        const remainingBalanceDisplay = document.getElementById('remaining-balance-display');
        const outsideRangeWarning = document.getElementById('outside-range-warning');

        function formatRupiah(value) {
            return 'Rp ' + value.toLocaleString('id-ID');
        }

        function calculateShippingCost(distance, isWonosobo) {
            let cost = 0;
            let outsideRange = false;

            if (distance >= 0 && distance <= 25) {
                cost = 0;
            } else if (distance >= 26 && distance <= 45) {
                cost = 200000;
            } else if (distance >= 46 && distance <= 65) {
                cost = 250000;
            } else if (distance >= 66 && distance <= 85) {
                cost = 300000;
            } else if (distance >= 86 && distance <= 100) {
                cost = 400000;
            } else if (distance >= 101 && distance <= 200) {
                cost = 500000;
            } else {
                cost = 0;
                outsideRange = true;
            }

            if (isWonosobo && !outsideRange) {
                cost = cost * 0.8; // 20% discount
            }

            return { cost, outsideRange };
        }

        function updateSummary() {
            let shippingCost = 0;
            let totalAmount = subtotal;
            let isOutsideRange = false;

            if (diantarRadio.checked) {
                const distanceVal = parseInt(shippingDistanceInput.value) || 0;
                const isWonosoboVal = isWonosoboCheckbox.checked;

                const calc = calculateShippingCost(distanceVal, isWonosoboVal);
                shippingCost = calc.cost;
                isOutsideRange = calc.outsideRange;

                if (distanceVal > 0) {
                    shippingDistanceLabel.innerText = `(${distanceVal} km${isWonosoboVal ? ', WSB -20%' : ''})`;
                } else {
                    shippingDistanceLabel.innerText = '';
                }

                if (isOutsideRange) {
                    shippingCostDisplay.innerText = 'Dihitung Manual';
                    shippingCostDisplay.className = 'font-semibold text-amber-600 animate-pulse-soft';
                    outsideRangeWarning.classList.remove('hidden');
                } else if (shippingCost === 0) {
                    shippingCostDisplay.innerText = 'Gratis';
                    shippingCostDisplay.className = 'font-semibold text-emerald-600';
                    outsideRangeWarning.classList.add('hidden');
                } else {
                    shippingCostDisplay.innerText = formatRupiah(shippingCost);
                    shippingCostDisplay.className = 'font-semibold text-slate-800 dark:text-slate-200';
                    outsideRangeWarning.classList.add('hidden');
                }
            } else {
                shippingDistanceLabel.innerText = '';
                shippingCostDisplay.innerText = 'Rp 0 (Ambil Sendiri)';
                shippingCostDisplay.className = 'font-semibold text-slate-500';
                outsideRangeWarning.classList.add('hidden');
            }

            // Total Amount
            totalAmount = subtotal + shippingCost;
            totalAmountDisplay.innerText = formatRupiah(totalAmount);

            // DP calculations
            if (payTypeDpRadio.checked) {
                const dpVal = totalAmount * 0.30;
                const remVal = totalAmount - dpVal;

                dpAmountDisplay.innerText = formatRupiah(dpVal);
                remainingBalanceDisplay.innerText = formatRupiah(remVal);

                dpAmountRow.classList.remove('hidden');
                remainingBalanceRow.classList.remove('hidden');
            } else {
                dpAmountRow.classList.add('hidden');
                remainingBalanceRow.classList.add('hidden');
            }
        }

        function toggleShippingMethod() {
            if (diantarRadio.checked) {
                diantarRadio.closest('label').classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                diantarRadio.closest('label').querySelector('.select-icon').classList.remove('hidden');
                
                diambilRadio.closest('label').classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                diambilRadio.closest('label').querySelector('.select-icon').classList.add('hidden');

                addressContainer.classList.remove('hidden');
                shippingAddressInput.setAttribute('required', 'required');
                deliveryDetailsContainer.classList.remove('hidden');
                shippingDistanceInput.setAttribute('required', 'required');
                pickupNote.classList.add('hidden');
            } else {
                diambilRadio.closest('label').classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                diambilRadio.closest('label').querySelector('.select-icon').classList.remove('hidden');
                
                diantarRadio.closest('label').classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                diantarRadio.closest('label').querySelector('.select-icon').classList.add('hidden');

                addressContainer.classList.add('hidden');
                shippingAddressInput.removeAttribute('required');
                deliveryDetailsContainer.classList.add('hidden');
                shippingDistanceInput.removeAttribute('required');
                pickupNote.classList.remove('hidden');
            }
            updateSummary();
        }

        function togglePaymentType() {
            if (payTypeFullRadio.checked) {
                payTypeLabelFull.classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                payTypeCheckFull.classList.remove('hidden');
                
                payTypeLabelDp.classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                payTypeCheckDp.classList.add('hidden');
            } else {
                payTypeLabelDp.classList.add('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                payTypeCheckDp.classList.remove('hidden');
                
                payTypeLabelFull.classList.remove('border-emerald-600', 'ring-2', 'ring-emerald-500/20');
                payTypeCheckFull.classList.add('hidden');
            }
            updateSummary();
        }

        // Listeners for shipping method
        diantarRadio.addEventListener('change', toggleShippingMethod);
        diambilRadio.addEventListener('change', toggleShippingMethod);

        // Listeners for shipping inputs
        shippingDistanceInput.addEventListener('input', updateSummary);
        isWonosoboCheckbox.addEventListener('change', updateSummary);

        // Listeners for payment type
        payTypeFullRadio.addEventListener('change', togglePaymentType);
        payTypeDpRadio.addEventListener('change', togglePaymentType);
        payTypeLabelFull.addEventListener('click', () => { payTypeFullRadio.checked = true; togglePaymentType(); });
        payTypeLabelDp.addEventListener('click', () => { payTypeDpRadio.checked = true; togglePaymentType(); });
        
        // Initial setup
        toggleShippingMethod();
        togglePaymentType();
    });
</script>
@endsection
