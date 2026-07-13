<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice_number }} - ARI FARM</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #fff;
            color: #000;
            padding: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            border: 1px dashed #000;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 2px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .item-table th {
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
            padding: 8px 4px;
            text-align: left;
        }
        .item-table td {
            padding: 8px 4px;
            border-bottom: 1px dashed #ccc;
        }
        .text-right {
            text-align: right !important;
        }
        .totals {
            width: 100%;
            margin-bottom: 40px;
        }
        .totals td {
            padding: 4px 0;
        }
        .footer {
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 15px;
            margin-top: 30px;
            font-size: 11px;
        }
        .status-stamp {
            display: inline-block;
            border: 3px double #000;
            padding: 5px 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <!-- Header -->
    <div class="header">
        <div>
            <h1>ARI FARM</h1>
            <span>E-Commerce Kambing & Domba Premium</span><br>
            <span style="font-size: 11px;">Jl. Ari Farm No. 42, Purwokerto Selatan, Banyumas</span>
        </div>
        <div class="text-right">
            <span class="status-stamp">{{ $order->status_label }}</span><br>
            <strong>INVOICE:</strong> {{ $order->invoice_number }}<br>
            <strong>TANGGAL:</strong> {{ $order->created_at->format('d/m/Y H:i') }} WIB
        </div>
    </div>

    <!-- Info -->
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>DITAGIHKAN KEPADA:</strong><br>
                {{ $order->user->name }}<br>
                {{ $order->user->email }}<br>
                Telp: {{ $order->phone_number }}
            </td>
            <td style="width: 50%;" class="text-right">
                <strong>ALAMAT PENGIRIMAN:</strong><br>
                {{ $order->shipping_address }}
            </td>
        </tr>
    </table>

    <!-- Items -->
    <table class="item-table">
        <thead>
            <tr>
                <th>Deskripsi Item</th>
                <th class="text-right" style="width: 15%;">Harga</th>
                <th class="text-right" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->goat->name }}</strong><br>
                        Ras: {{ $item->goat->breed }} | Bobot: {{ $item->goat->weight_kg }} kg | Umur: {{ $item->goat->age_months }} Bln
                    </td>
                    <td class="text-right">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals" style="width: 300px; margin-left: auto;">
        <tr>
            <td>Subtotal:</td>
            <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pengiriman:</td>
            <td class="text-right">Rp 0 (Gratis)</td>
        </tr>
        <tr style="border-top: 1px solid #000; font-weight: bold;">
            <td style="padding-top: 8px;">Total Bayar:</td>
            <td class="text-right" style="padding-top: 8px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($order->notes)
        <div style="margin-bottom: 30px;">
            <strong>Catatan Tambahan:</strong><br>
            <span style="font-style: italic;">{{ $order->notes }}</span>
        </div>
    @endif

    <div class="footer">
        Terima kasih atas kepercayaan Anda bertransaksi di ARI FARM.<br>
        Hewan disembelih & dikirim secara Amanah sesuai Syariat Islam.<br>
        <strong>Layanan Pelanggan: +62 812-3456-7890 | support@ARI FARM.com</strong>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
