<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peternakan Ari Farm</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            background: #fff;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #09422a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-left .brand {
            font-size: 22px;
            font-weight: 900;
            color: #09422a;
            letter-spacing: 2px;
        }
        .header-left .tagline {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .header-right {
            text-align: right;
        }
        .header-right .report-name {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-right .period {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
            font-style: italic;
        }
        .header-right .print-date {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* ===== MAIN TABLE ===== */
        .master-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .master-table th,
        .master-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
        }

        /* Table Header Row */
        .master-table thead tr {
            background-color: #334155;
            color: #fff;
        }
        .master-table thead th {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-color: #475569;
        }

        /* Section Header Rows */
        .section-i   { background-color: #09422a; color: #fff; }
        .section-ii  { background-color: #1e3a8a; color: #fff; }
        .section-iii { background-color: #7f1d1d; color: #fff; }
        .section-iv  { background-color: #374151; color: #fff; }
        .section-v   { background-color: #145232; color: #fff; }

        .section-row td {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 7px 10px;
            border-color: transparent;
        }

        /* Data rows */
        .master-table tbody tr:nth-child(even):not(.section-row):not(.footer-row) {
            background-color: #f8fafc;
        }

        /* Number column */
        .col-no { text-align: center; font-family: monospace; color: #64748b; width: 28px; }
        .col-date { width: 72px; font-family: monospace; }
        .col-type { width: 88px; }
        .col-ref  { width: 100px; font-weight: 700; }
        .col-desc { }
        .col-debit  { width: 90px; text-align: right; font-weight: 800; color: #15803d; }
        .col-kredit { width: 90px; text-align: right; font-weight: 800; color: #dc2626; }
        .col-saldo  { width: 90px; text-align: right; font-weight: 800; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }
        .font-black { font-weight: 900; }
        .text-muted { color: #94a3b8; }
        .text-green { color: #15803d; }
        .text-red   { color: #dc2626; }
        .text-indigo { color: #4338ca; }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: 0.3px;
        }
        .badge-in  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-out { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Footer summary row */
        .footer-row td {
            background-color: #1e293b;
            color: #fff;
            font-weight: 900;
            font-size: 10px;
            padding: 8px 10px;
        }

        /* Page footer */
        .page-footer {
            margin-top: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    {{-- ===== HEADER ===== --}}
    <div class="header">
        <div class="header-left">
            <div class="brand">ARI FARM</div>
            <div class="tagline">Sistem Informasi Manajemen &amp; Penjualan Kambing Premium</div>
        </div>
        <div class="header-right">
            <div class="report-name">Laporan Operasional &amp; Keuangan</div>
            <div class="period">
                Periode: {{ $dateFrom->format('d M Y') }} s/d {{ $dateTo->format('d M Y') }}
                &nbsp;&mdash;&nbsp; {{ ucfirst(str_replace('_', ' ', $periodType)) }}
            </div>
            <div class="print-date">Dicetak: {{ now()->format('d M Y H:i') }} &bull; oleh {{ Auth::user()->name }}</div>
        </div>
    </div>

    {{-- ===== MASTER TABLE ===== --}}
    @php
        $totalKids = $birthStats['total_male'] + $birthStats['total_female'] + $birthStats['total_dead'];
        $deathRate = $totalKids > 0 ? ($birthStats['total_dead'] / $totalKids) * 100 : 0;

        // Build ledger
        $ledger = collect([]);
        foreach($ordersList as $ord) {
            $ledger->push([
                'date'  => $ord->created_at,
                'type'  => 'pemasukan',
                'label' => $ord->invoice_number,
                'desc'  => 'Penjualan kambing ke ' . ($ord->user->name ?? '-'),
                'in'    => $ord->total_amount,
                'out'   => 0,
            ]);
        }
        foreach($expensesList as $exp) {
            $ledger->push([
                'date'  => $exp->expense_date,
                'type'  => 'pengeluaran',
                'label' => $exp->category_label,
                'desc'  => $exp->title . ($exp->description ? ' (' . $exp->description . ')' : ''),
                'in'    => 0,
                'out'   => $exp->amount,
            ]);
        }
        $ledger      = $ledger->sortBy('date')->values();
        $runningSaldo = 0;
    @endphp

    <table class="master-table">
        <thead>
            <tr>
                <th class="text-center col-no">No</th>
                <th class="col-date">Tanggal / Periode</th>
                <th class="col-type">Kategori / Tipe</th>
                <th class="col-ref">Referensi / Pos</th>
                <th>Keterangan / Rincian Laporan</th>
                <th class="col-debit text-right">Debit (In)</th>
                <th class="col-kredit text-right">Kredit (Out)</th>
                <th class="col-saldo text-right">Saldo / Status</th>
            </tr>
        </thead>
        <tbody>

            {{-- ============ SECTION I: IKHTISAR FINANSIAL ============ --}}
            <tr class="section-row">
                <td colspan="8" class="section-i">I. Ikhtisar Finansial (Financial Summary)</td>
            </tr>
            <tr>
                <td class="col-no">1</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold text-green">FINANSIAL IN</td>
                <td class="col-ref font-bold">Total Pemasukan</td>
                <td>Seluruh kas masuk yang bersumber dari hasil penjualan kambing &amp; domba terverifikasi.</td>
                <td class="col-debit">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo text-muted">—</td>
            </tr>
            <tr>
                <td class="col-no">2</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold text-red">FINANSIAL OUT</td>
                <td class="col-ref font-bold">Total Pengeluaran</td>
                <td>Biaya operasional kandang, logistik pakan, obat-obatan ternak, dan pembelian bibit baru.</td>
                <td class="col-debit text-muted">—</td>
                <td class="col-kredit">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="col-saldo text-muted">—</td>
            </tr>
            <tr style="background:#f0fdf4;">
                <td class="col-no">3</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold text-indigo">FINANSIAL NET</td>
                <td class="col-ref font-black text-green">Laba Bersih (Profit)</td>
                <td>Selisih laba bersih (Pemasukan dikurangi pengeluaran operasional).</td>
                <td class="col-debit text-muted">—</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo {{ $netProfit >= 0 ? 'text-indigo' : 'text-red' }}">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </td>
            </tr>

            {{-- ============ SECTION II: TREN KEUANGAN ============ --}}
            <tr class="section-row">
                <td colspan="8" class="section-ii">II. Tren Keuangan 6 Bulan Terakhir (Monthly Trends)</td>
            </tr>
            @foreach($incomeByMonth as $idx => $inc)
                @php
                    $exp  = $expenseByMonth[$idx];
                    $diff = $inc['total'] - $exp['total'];
                @endphp
                <tr>
                    <td class="col-no">{{ $idx + 1 }}</td>
                    <td class="col-date font-bold">{{ $inc['label'] }}</td>
                    <td class="col-type text-muted">Tren Finansial</td>
                    <td class="col-ref">Bulanan</td>
                    <td>Rekap bulanan debit-kredit-selisih pada periode {{ $inc['label'] }}.</td>
                    <td class="col-debit">Rp {{ number_format($inc['total'], 0, ',', '.') }}</td>
                    <td class="col-kredit">Rp {{ number_format($exp['total'], 0, ',', '.') }}</td>
                    <td class="col-saldo {{ $diff >= 0 ? 'text-indigo' : 'text-red' }}">
                        Rp {{ number_format($diff, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            {{-- ============ SECTION III: ALOKASI ANGGARAN ============ --}}
            <tr class="section-row">
                <td colspan="8" class="section-iii">III. Alokasi Anggaran Pengeluaran (Expense Allocations)</td>
            </tr>
            @forelse($expenseByCategory as $eIdx => $expCat)
                @php
                    $pct   = $totalExpense > 0 ? ($expCat->total / $totalExpense) * 100 : 0;
                    $label = match($expCat->category) {
                        'pakan'           => 'Pakan & Nutrisi',
                        'kesehatan'       => 'Obat & Kesehatan',
                        'operasional'     => 'Operasional Kandang',
                        'pembelian_hewan' => 'Pembelian Indukan/Bibit',
                        default           => 'Lainnya'
                    };
                @endphp
                <tr>
                    <td class="col-no">{{ $eIdx + 1 }}</td>
                    <td class="col-date text-muted">—</td>
                    <td class="col-type font-bold text-red">ANGGARAN OUT</td>
                    <td class="col-ref font-bold">{{ $label }}</td>
                    <td>Distribusi pengeluaran kas operasional untuk kategori {{ $label }}. Persentase: {{ number_format($pct, 1) }}% dari total pengeluaran.</td>
                    <td class="col-debit text-muted">—</td>
                    <td class="col-kredit">Rp {{ number_format($expCat->total, 0, ',', '.') }}</td>
                    <td class="col-saldo">{{ number_format($pct, 1) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:10px;">Tidak ada data pengeluaran dalam periode ini.</td>
                </tr>
            @endforelse

            {{-- ============ SECTION IV: OPERASIONAL KANDANG ============ --}}
            <tr class="section-row">
                <td colspan="8" class="section-iv">IV. Ikhtisar Operasional Kandang (Operational Summaries)</td>
            </tr>
            {{-- 4.1 Populasi --}}
            <tr>
                <td class="col-no">1</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold">POPULASI</td>
                <td class="col-ref font-bold">Stok Kambing</td>
                <td>
                    Tersedia: <strong>{{ $goatStats['available'] }}</strong> |
                    Terjual: <strong>{{ $goatStats['sold'] }}</strong> |
                    Lahir: <strong>{{ $goatStats['by_origin']['kelahiran'] ?? 0 }}</strong> |
                    Beli: <strong>{{ $goatStats['by_origin']['beli'] ?? 0 }}</strong>
                </td>
                <td class="col-debit font-black" style="color:#0f172a;">{{ $goatStats['total'] }} Ekor</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo text-muted">—</td>
            </tr>
            {{-- 4.2 Kelahiran --}}
            <tr>
                <td class="col-no">2</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold">KELAHIRAN</td>
                <td class="col-ref font-bold">Cempe Baru</td>
                <td>
                    Hidup: <strong>{{ $birthStats['total_male'] }} Jantan, {{ $birthStats['total_female'] }} Betina</strong> |
                    Lahir Mati: <strong style="color:#dc2626;">{{ $birthStats['total_dead'] }} cempe</strong>
                </td>
                <td class="col-debit font-black" style="color:#0f172a;">{{ $birthStats['total_births'] }} Indukan</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo text-red">Mati: {{ number_format($deathRate, 1) }}%</td>
            </tr>
            {{-- 4.3 Kesehatan --}}
            <tr>
                <td class="col-no">3</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold">KESEHATAN</td>
                <td class="col-ref font-bold">Medis Ternak</td>
                <td>
                    Vaksin/Vitamin: <strong>{{ $healthStats['by_type']['vaksin'] ?? ($healthStats['by_type']['vaksinasi'] ?? 0) }} kali</strong> |
                    Pengobatan Sakit: <strong>{{ $healthStats['by_type']['pengobatan'] ?? 0 }} kali</strong>
                </td>
                <td class="col-debit font-black" style="color:#0f172a;">{{ $healthStats['total_records'] }} Rekam</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo text-muted">—</td>
            </tr>
            {{-- 4.4 Pakan --}}
            <tr>
                <td class="col-no">4</td>
                <td class="col-date text-muted">—</td>
                <td class="col-type font-bold">PAKAN</td>
                <td class="col-ref font-bold">Gudang Pakan</td>
                <td>
                    Frekuensi Distribusi: <strong>{{ $feedingStats['feeding_count'] }} kali log</strong> |
                    Rata-rata per Log: <strong>{{ $feedingStats['feeding_count'] > 0 ? number_format($feedingStats['total_kg_period'] / $feedingStats['feeding_count'], 1) . ' kg' : '-' }}</strong>
                </td>
                <td class="col-debit font-black" style="color:#0f172a;">{{ number_format($feedingStats['total_kg_period'], 1) }} Kg</td>
                <td class="col-kredit text-muted">—</td>
                <td class="col-saldo text-muted">—</td>
            </tr>

            {{-- ============ SECTION V: BUKU KAS DETAIL ============ --}}
            <tr class="section-row">
                <td colspan="8" class="section-v">V. Buku Kas Detail — Mutasi Kronologis (Detailed Ledger Entries)</td>
            </tr>

            @forelse($ledger as $index => $item)
                @php
                    if ($item['type'] == 'pemasukan') {
                        $runningSaldo += $item['in'];
                    } else {
                        $runningSaldo -= $item['out'];
                    }
                @endphp
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-date">{{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}</td>
                    <td class="col-type text-center">
                        <span class="badge {{ $item['type'] == 'pemasukan' ? 'badge-in' : 'badge-out' }}">
                            {{ $item['type'] == 'pemasukan' ? 'DEBIT' : 'KREDIT' }}
                        </span>
                    </td>
                    <td class="col-ref font-bold">{{ $item['label'] }}</td>
                    <td>{{ $item['desc'] }}</td>
                    <td class="col-debit">{{ $item['in'] > 0 ? 'Rp ' . number_format($item['in'], 0, ',', '.') : '—' }}</td>
                    <td class="col-kredit">{{ $item['out'] > 0 ? 'Rp ' . number_format($item['out'], 0, ',', '.') : '—' }}</td>
                    <td class="col-saldo {{ $runningSaldo >= 0 ? 'text-indigo' : 'text-red' }}">
                        Rp {{ number_format($runningSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted" style="padding:12px;">
                        Tidak ada catatan transaksi keuangan pada periode terpilih.
                    </td>
                </tr>
            @endforelse

            {{-- FOOTER ROW --}}
            @if(count($ledger) > 0)
                <tr class="footer-row">
                    <td colspan="5" class="text-right" style="font-size:9px;letter-spacing:0.5px;">
                        TOTAL AKHIR KAS (MUTASI PERIODE INI)
                    </td>
                    <td class="text-right" style="color:#86efac;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:#fca5a5;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                    <td class="text-right" style="color:{{ $netProfit >= 0 ? '#a5b4fc' : '#fca5a5' }};">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
                </tr>
            @endif

        </tbody>
    </table>

    {{-- ===== PAGE FOOTER ===== --}}
    <div class="page-footer">
        <span>Ari Farm — Sistem Informasi Manajemen Peternakan</span>
        <span>Laporan periode {{ $dateFrom->format('d M Y') }} – {{ $dateTo->format('d M Y') }} &bull; Dicetak {{ now()->format('d M Y H:i:s') }}</span>
    </div>

</body>
</html>
