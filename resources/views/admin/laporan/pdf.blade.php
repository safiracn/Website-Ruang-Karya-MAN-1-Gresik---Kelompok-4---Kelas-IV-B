<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
        .header { background: #00266B; color: white; padding: 16px 20px; display: flex; align-items: center; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: 700; }
        .header p  { font-size: 9px; opacity: .75; margin-top: 2px; }
        .meta { padding: 0 20px 12px; display: flex; gap: 24px; }
        .meta div { background: #f1f5f9; padding: 8px 14px; border-radius: 6px; }
        .meta label { font-size: 8px; text-transform: uppercase; color: #64748b; letter-spacing: .05em; display: block; margin-bottom: 2px; }
        .meta span  { font-size: 11px; font-weight: 700; color: #00266B; }
        table { width: 100%; border-collapse: collapse; margin: 0 20px; width: calc(100% - 40px); }
        thead tr th { background: #00266B; color: white; padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
        tbody tr td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9px; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 600; }
        .badge-bayar  { background: #dcfce7; color: #166534; }
        .badge-belum  { background: #fef9c3; color: #854d0e; }
        .badge-selesai   { background: #dcfce7; color: #166534; }
        .badge-diproses  { background: #dbeafe; color: #1e40af; }
        .badge-batal     { background: #fee2e2; color: #991b1b; }
        .badge-pending   { background: #f1f5f9; color: #475569; }
        .total-row td { background: #00266B !important; color: white; font-weight: 700; }
        .footer { margin: 16px 20px 0; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .rp { text-align: right; white-space: nowrap; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>Laporan Penjualan — Ruang Karya MAN 1 Gresik</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}</p>
    </div>
</div>

<div class="meta">
    <div>
        <label>Total Transaksi</label>
        <span>{{ $pesanan->count() }}</span>
    </div>
    <div>
        <label>Total Pendapatan (Lunas)</label>
        <span>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
    </div>
    <div>
        <label>Dicetak</label>
        <span>{{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width:3%">No</th>
            <th style="width:12%">Kode Pesanan</th>
            <th style="width:11%">Tanggal</th>
            <th style="width:14%">Pembeli</th>
            <th style="width:13%">Penerima</th>
            <th style="width:10%">Kota</th>
            <th style="width:9%">Pengiriman</th>
            <th style="width:10%">Status Bayar</th>
            <th style="width:9%">Status Pesanan</th>
            <th style="width:9%">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pesanan as $i => $p)
        @php
            $kodePesanan = 'RK260605-' . sprintf('%04d', $p->id_pembelian);
            $badgeBayar  = $p->status_pembayaran === 'Sudah dibayar' ? 'badge-bayar' : 'badge-belum';
            $badgePesan  = match($p->status_pesanan) {
                'Selesai'    => 'badge-selesai',
                'Diproses'   => 'badge-diproses',
                'Dibatalkan' => 'badge-batal',
                default      => 'badge-pending',
            };
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $kodePesanan }}</td>
            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
            <td>{{ $p->user?->nama_lengkap ?? '-' }}</td>
            <td>{{ $p->nama_penerima }}</td>
            <td>{{ $p->kota_kabupaten }}</td>
            <td>{{ $p->metode_pengiriman }}</td>
            <td><span class="badge {{ $badgeBayar }}">{{ $p->status_pembayaran }}</span></td>
            <td><span class="badge {{ $badgePesan }}">{{ $p->status_pesanan }}</span></td>
            <td class="rp">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
        </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="9" style="text-align:right; padding-right:12px;">TOTAL PENDAPATAN (LUNAS)</td>
            <td class="rp">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    &copy; {{ date('Y') }} Ruang Karya MAN 1 Gresik &bull; Dokumen ini dicetak secara otomatis oleh sistem.
</div>

</body>
</html>