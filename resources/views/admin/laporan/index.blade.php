@extends('layouts.admin')

@section('title', 'Laporan Penjualan')
@section('pageDesc', 'Analisis performa transaksi Ruang Karya MAN 1 Gresik.')

@section('activeMenu', 'laporan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    .kpi-card { transition: transform .15s, box-shadow .15s; }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px -4px rgba(0,0,0,.10); }
    .chart-wrap { position: relative; }
    .tab-btn { transition: all .15s; }
    .tab-btn.active { background: #00266B !important; color: #fff !important; border-color: #00266B !important; }
    .tbl-wrap::-webkit-scrollbar { height: 5px; }
    .tbl-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9px; }
</style>
@endpush

@section('content')

{{-- ── Flash Messages ─────────────────────────────────────────────────── --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-3 text-sm font-medium mb-6">
    <i class="fa-solid fa-circle-check text-green-500"></i>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-3 text-sm font-medium mb-6">
    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
    {{ session('error') }}
</div>
@endif

<div class="space-y-6">

    {{-- ── Tombol Aksi (Export / Import) ──────────────────────────────── --}}
    <div class="flex items-center gap-2 flex-wrap">

        {{-- IMPORT --}}
        <button onclick="document.getElementById('modalImport').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-slate-400 text-slate-700 rounded-lg text-sm font-semibold shadow-sm transition">
            <i class="fa-solid fa-file-arrow-up text-blue-600"></i>
            Import Excel
        </button>

        {{-- EXPORT EXCEL --}}
        <a href="{{ route('admin.laporan.export.excel', array_merge(['range' => $range], request()->only('start_date','end_date'))) }}"
        class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 hover:border-slate-400 text-slate-700 rounded-lg text-sm font-semibold shadow-sm transition">
            <i class="fa-solid fa-file-excel text-green-600"></i>
            Export Excel
        </a>

        {{-- EXPORT PDF (FIX DI SINI) --}}
        <a href="{{ route('admin.laporan.export.pdf', array_merge(['range' => $range], request()->only('start_date','end_date'))) }}"
        class="flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">

            <i class="fa-solid fa-file-lines text-white"></i>
            Export PDF
        </a>

    </div>

    {{-- ── Filter Bar ──────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.laporan.index') }}" id="filterForm"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col sm:flex-row gap-4 items-end">

        <div class="flex-1 min-w-0">
            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Rentang Waktu</label>
            <div class="flex gap-2 flex-wrap">
                @foreach(['7' => '7 Hari', '30' => '30 Hari', 'custom' => 'Custom'] as $val => $label)
                <button type="button"
                        onclick="setRange('{{ $val }}')"
                        class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold border border-slate-200 bg-slate-50 text-slate-600 {{ $range === $val ? 'active' : '' }}"
                        data-range="{{ $val }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>
            <input type="hidden" name="range" id="rangeInput" value="{{ $range }}">
        </div>

        <div id="customDates" class="{{ $range !== 'custom' ? 'hidden' : '' }} flex gap-3 items-end">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date', now()->subDays(29)->toDateString()) }}"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date', now()->toDateString()) }}"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>

        <button type="submit"
                class="px-6 py-2.5 bg-blue-900 text-white rounded-lg text-sm font-bold shadow-sm hover:bg-blue-800 transition whitespace-nowrap">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Terapkan
        </button>
    </form>

    {{-- ── KPI Cards ───────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
        @php
        $cards = [
            [
                'icon'=>'fa-money-bill-wave',
                'color'=>'text-green-500',
                'bg'=>'bg-green-50',
                'label'=>'Total Pendapatan',
                'value'=>'Rp '.number_format($totalPendapatan,0,',','.')
            ],
            [
                'icon'=>'fa-box',
                'color'=>'text-blue-500',
                'bg'=>'bg-blue-50',
                'label'=>'Produk Terjual',
                'value'=>number_format($produkTerjual)
            ],
            [
                'icon'=>'fa-users',
                'color'=>'text-violet-500',
                'bg'=>'bg-violet-50',
                'label'=>'Pelanggan',
                'value'=>number_format($pelangganAktif)
            ],
            [
                'icon'=>'fa-hourglass-half',
                'color'=>'text-red-500',
                'bg'=>'bg-red-50',
                'label'=>'Belum Dibayar',
                'value'=>'Rp '.number_format($belumDibayar,0,',','.')
            ],
            [
                'icon'=>'fa-receipt',
                'color'=>'text-amber-500',
                'bg'=>'bg-amber-50',
                'label'=>'Rata-rata Transaksi',
                'value'=>'Rp '.number_format($rataRataTransaksi,0,',','.')
            ],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="kpi-card bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-3">
            <div class="w-9 h-9 {{ $card['bg'] }} rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $card['icon'] }} {{ $card['color'] }} text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">{{ $card['label'] }}</p>
                <p class="text-lg font-black text-slate-900 leading-tight truncate">{{ $card['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Charts: Bar (2/3) + Donut (1/3) ──────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Bar Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tren Pendapatan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pendapatan harian (pesanan lunas)</p>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Bar Chart</span>
            </div>
            <div class="chart-wrap" style="height:260px;">
                <canvas id="chartBar"></canvas>
            </div>
        </div>

        {{-- Donut Chart --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Distribusi Penjualan</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Per Kategori</p>
                </div>
                <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">Donut Chart</span>
            </div>
            <div class="chart-wrap" style="height:200px;">
                <canvas id="chartDonut"></canvas>
            </div>
            <div class="mt-4 space-y-1.5" id="donutLegend"></div>
        </div>
    </div>

    {{-- Line Chart --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Produk Terjual Harian --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Produk Terjual Harian
                </h2>
                <p class="text-xs text-slate-400">
                    Total unit produk terjual per hari
                </p>
            </div>
            <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">
                Line Chart
            </span>
        </div>

        <div class="chart-wrap" style="height:250px;">
            <canvas id="chartLine"></canvas>
        </div>
    </div>

    {{-- Produk Terlaris --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">
                    Produk Terlaris
                </h2>
                <p class="text-xs text-slate-400">
                    Berdasarkan jumlah unit terjual
                </p>
            </div>
            <span class="text-[10px] uppercase tracking-widest font-bold text-slate-400">
                Horizontal Bar
            </span>
        </div>

        <div class="chart-wrap" style="height:250px;">
            <canvas id="chartProdukTerlaris"></canvas>
        </div>
    </div>
</div>

 {{-- Tabel Transaksi Terkini --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Transaksi Terkini</h2>
                <span class="text-xs text-slate-400">{{ $transaksiTerkini->total() }} total</span>
            </div>
            <div class="tbl-wrap overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-400 font-bold">
                            <th class="px-5 py-3 text-left">Kode</th>
                            <th class="px-5 py-3 text-left">Tanggal</th>
                            <th class="px-5 py-3 text-left">Pembeli</th>
                            <th class="px-5 py-3 text-left">Status Pembayaran</th>
                            <th class="px-5 py-3 text-left">Status Pesanan</th>
                            <th class="px-5 py-3 text-left">Status Pengiriman</th>
                            <th class="px-5 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                     @forelse($transaksiTerkini as $trx)
                    @php
                        $kode = 'RK260605-' . sprintf('%04d', $trx->id_pembelian);

                        $badgeBayar = $trx->status_pembayaran === 'Sudah Dibayar'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700';

                        $badgePesan = match($trx->status_pesanan) {
                            'Selesai'    => 'bg-green-100 text-green-700',
                            'Diproses'   => 'bg-blue-100 text-blue-700',
                            'Pending'    => 'bg-slate-100 text-slate-600',
                            'Dibatalkan' => 'bg-red-100 text-red-700',
                            default      => 'bg-slate-100 text-slate-600',
                        };

                        $badgeKirim = match($trx->status_kirim ?? null) {
                            'Diterima'     => 'bg-green-100 text-green-700',
                            'Dikirim'      => 'bg-blue-100 text-blue-700',
                            'Belum Dikirim'=> 'bg-slate-100 text-slate-600',
                            default        => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-mono font-bold text-blue-900 text-xs">{{ $kode }}</td>
                            <td class="px-5 py-3 text-slate-500 text-xs">
                                {{ \Carbon\Carbon::parse($trx->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-700">
                                {{ $trx->user?->nama_lengkap ?? '-' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badgeBayar }}">
                                    {{ $trx->status_pembayaran }}
                                </span>
                            </td>

                            <td class="px-5 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badgePesan }}">
                                    {{ $trx->status_pesanan }}
                                </span>
                            </td>

                            <td class="px-5 py-3">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $badgeKirim }}">
                                    {{ $trx->status_kirim ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-slate-800">
                                Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-sm italic">
                                Tidak ada transaksi dalam rentang ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>  
            {{-- Pagination --}}
            @if($transaksiTerkini->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $transaksiTerkini->firstItem() }}–{{ $transaksiTerkini->lastItem() }}
                    dari {{ $transaksiTerkini->total() }} transaksi
                </p>
                <div class="flex gap-1">
                    @if($transaksiTerkini->onFirstPage())
                        <span class="px-3 py-1.5 text-xs rounded-lg bg-slate-100 text-slate-400 cursor-default">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $transaksiTerkini->previousPageUrl() }}"
                           class="px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif
                    @if($transaksiTerkini->hasMorePages())
                        <a href="{{ $transaksiTerkini->nextPageUrl() }}"
                           class="px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-xs rounded-lg bg-slate-100 text-slate-400 cursor-default">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>{{-- end space-y-6 --}}

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- MODAL IMPORT                                                            --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div id="modalImport" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-7">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-black text-slate-900">Import Data Excel</h3>
            <button onclick="document.getElementById('modalImport').classList.add('hidden')"
                    class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition text-slate-500">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-5 text-xs text-amber-800 leading-relaxed">
            <strong>Format kolom:</strong><br>
            <code class="font-mono">kode_pesanan | status_pembayaran | status_pesanan | status_pengiriman</code>

            <ul class="mt-2 list-disc pl-4">
                <li><strong>Status Pembayaran:</strong> Belum Dibayar, Sudah dibayar, Dana dikembalikan</li>
                <li><strong>Status Pesanan:</strong> Pending, Diproses, Menunggu Konfirmasi Pembatalan, Selesai, Dibatalkan</li>
                <li><strong>Status Pengiriman:</strong> Belum dikirim, Dikirim, Diterima</li>
            </ul>
        </div>
        <form method="POST" action="{{ route('admin.laporan.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Pilih File (.xlsx / .xls)</label>
                <input type="file" name="file" accept=".xlsx,.xls" required
                       class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-blue-900 file:text-white hover:file:bg-blue-800 cursor-pointer border border-slate-200 rounded-xl p-1">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button"
                        onclick="document.getElementById('modalImport').classList.add('hidden')"
                        class="flex-1 py-2.5 border border-slate-300 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-blue-900 text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-upload"></i> Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- SCRIPTS                                                                 --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
// ─── Data dari PHP ────────────────────────────────────────────────────────
const labelHarian   = @json($labelHarian);
const dataHarian    = @json($dataHarian);
const dataTransaksi = @json($dataProdukHarian);
const labelKategori = @json($labelKategori ?? []);
const dataKategori  = @json($dataKategori  ?? []);
const labelProdukTerlaris = @json($labelProdukTerlaris ?? []);
const dataProdukTerlaris  = @json($dataProdukTerlaris ?? []);

const NAVY   = '#00266B';
const YELLOW = '#FDC003';
const DONUT_COLORS = [
    '#00266B','#FDC003','#3B82F6','#10B981','#F59E0B',
    '#8B5CF6','#EC4899','#14B8A6','#F97316','#6366F1',
];
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
};

// ─── 1. Bar Chart: Pendapatan Harian ──────────────────────────────────────
new Chart(document.getElementById('chartBar'), {
    type: 'bar',
    data: {
        labels: labelHarian,
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: dataHarian,
            backgroundColor: dataHarian.map((_, i) =>
                i === dataHarian.indexOf(Math.max(...dataHarian)) ? YELLOW : NAVY + '99'
            ),
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        ...chartDefaults,
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: {
                    font: { size: 10 }, color: '#94a3b8',
                    callback: v => 'Rp ' + (v >= 1e6 ? (v/1e6).toFixed(1)+'jt' : v.toLocaleString('id-ID')),
                }
            },
        },
        plugins: {
            ...chartDefaults.plugins,
            tooltip: { callbacks: { label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID') } }
        }
    }
});

// ─── 2. Donut Chart: Per Kategori ─────────────────────────────────────────
if (dataKategori.length > 0) {
    new Chart(document.getElementById('chartDonut'), {
        type: 'doughnut',
        data: {
            labels: labelKategori,
            datasets: [{
                data: dataKategori,
                backgroundColor: DONUT_COLORS.slice(0, dataKategori.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            ...chartDefaults,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.label + ': Rp ' + ctx.raw.toLocaleString('id-ID') } }
            }
        }
    });

    const legendEl = document.getElementById('donutLegend');
    const total = dataKategori.reduce((a, b) => a + b, 0);
    labelKategori.forEach((label, i) => {
        const pct = total > 0 ? ((dataKategori[i] / total) * 100).toFixed(1) : 0;
        legendEl.innerHTML += `
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:${DONUT_COLORS[i] ?? '#ccc'}"></span>
                    <span class="text-xs text-slate-600 truncate">${label}</span>
                </div>
                <span class="text-xs font-bold text-slate-700 whitespace-nowrap">${pct}%</span>
            </div>`;
    });
} else {
    const wrap = document.getElementById('chartDonut').closest('.chart-wrap');
    if (wrap) wrap.innerHTML = '<p class="text-center text-sm text-slate-400 italic pt-16">Belum ada data kategori.</p>';
}

// ─── 3. Line Chart: Volume Transaksi ──────────────────────────────────────
new Chart(document.getElementById('chartLine'), {
    type: 'line',
    data: {
        labels: labelHarian,
        datasets: [{
            label: 'Jumlah Transaksi',
            data: dataTransaksi,
            borderColor: NAVY,
            backgroundColor: NAVY + '18',
            pointBackgroundColor: YELLOW,
            pointBorderColor: NAVY,
            pointRadius: 4,
            pointHoverRadius: 6,
            tension: 0.4,
            fill: true,
        }]
    },
    options: {
        ...chartDefaults,
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94a3b8' } },
            y: {
                grid: { color: '#f1f5f9' },
                ticks: { font: { size: 10 }, color: '#94a3b8', stepSize: 1, precision: 0 },
                beginAtZero: true,
            },
        },
        plugins: {
            ...chartDefaults.plugins,
            tooltip: { callbacks: { label: ctx => ctx.raw + ' produk' } }
        }
    }
});

// 4. Produk Terlaris
if (labelProdukTerlaris.length > 0) {
    new Chart(document.getElementById('chartProdukTerlaris'), {
        type: 'bar',
        data: {
            labels: labelProdukTerlaris,
            datasets: [{
                label: 'Unit Terjual',
                data: dataProdukTerlaris,
                backgroundColor: NAVY,
                borderRadius: 6,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Filter Tab Logic 
function setRange(val) {
    document.getElementById('rangeInput').value = val;
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.range === val);
    });
    const customDates = document.getElementById('customDates');
    if (val === 'custom') {
        customDates.classList.remove('hidden');
    } else {
        customDates.classList.add('hidden');
        document.getElementById('filterForm').submit();
    }
}
</script>
@endpush