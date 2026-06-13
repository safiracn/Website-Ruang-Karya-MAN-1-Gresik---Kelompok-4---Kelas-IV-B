<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Exports\LaporanExport;
use App\Imports\LaporanImport;
use App\Helpers\ActivityHelper;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan penjualan
     */
    public function index(Request $request)
    {
        // ─── 1. Rentang Tanggal (pakai created_at) ────────────────────────
        $range = $request->input('range', '30');
        $today = Carbon::today();

        if ($range === 'custom') {
            $startDate = Carbon::parse($request->input('start_date', now()->subDays(29)->toDateString()))->startOfDay();
            $endDate   = Carbon::parse($request->input('end_date', now()->toDateString()))->endOfDay();
        } else {
            $days      = (int) $range;
            $startDate = $today->copy()->subDays($days - 1)->startOfDay();
            $endDate   = $today->copy()->endOfDay();
        }

        // ─── 2. KPI Cards ─────────────────────────────────────────────────
        $totalPendapatan = Pembelian::whereBetween(
                'created_at',
                [$startDate, $endDate]
            )
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->sum('total_harga');

        // Pelanggan adalah pengguna yang pernah melakukan pemesanan dan pesanannya tidak dibatalkan."
        $pelangganAktif = Pembelian::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_pesanan', '!=', 'Dibatalkan')
            ->distinct('id_user')
            ->count('id_user');

        $produkTerjual = PembelianDetail::join(
                'pembelian',
                'pembelian_detail.id_pembelian',
                '=',
                'pembelian.id_pembelian'
            )
            ->whereBetween('pembelian.created_at', [$startDate, $endDate])
            ->where('pembelian.status_pembayaran', 'Sudah Dibayar')
            ->sum('pembelian_detail.jumlah');

        // hitung total nominal yang belum dibayar
        $belumDibayar = DB::table('pembelian')
    ->whereBetween('created_at', [$startDate, $endDate])
    ->whereRaw('LOWER(status_pembayaran) = ?', ['belum dibayar'])
    ->sum('total_harga');
    
        $rataRataTransaksi = Pembelian::whereBetween('created_at', [$startDate, $endDate])
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->avg('total_harga');

        // ─── 3. Chart Bar: Pendapatan Harian ──────────────────────────────
        $pendapatanHarian = Pembelian::selectRaw('DATE(created_at) as tanggal, SUM(total_harga) as total')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labelHarian = [];
        $dataHarian  = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $key           = $current->toDateString();
            $labelHarian[] = $current->format('d/m');
            $dataHarian[]  = (float) ($pendapatanHarian[$key]->total ?? 0);
            $current->addDay();
        }

        // ─── 4. Chart Line: Transaksi Harian ──────────────────────────────
        $produkHarian = PembelianDetail::join(
                'pembelian',
                'pembelian_detail.id_pembelian',
                '=',
                'pembelian.id_pembelian'
            )
            ->whereBetween('pembelian.created_at', [$startDate, $endDate])
            ->where('pembelian.status_pembayaran', 'Sudah Dibayar')
            ->selectRaw('DATE(pembelian.created_at) as tanggal,
                        SUM(pembelian_detail.jumlah) as jumlah')
            ->groupBy(DB::raw('DATE(pembelian.created_at)'))
            ->get()
            ->keyBy('tanggal');

        $dataProdukHarian = [];

        $current = $startDate->copy();

        while ($current <= $endDate) {

            $key = $current->toDateString();

            $dataProdukHarian[] =
                (int) ($produkHarian[$key]->jumlah ?? 0);

            $current->addDay();
        }

        // ─── 5. Chart Donut: Penjualan per Kategori ───────────────────────
        $penjualanKategori = PembelianDetail::join('produk_varian', 'pembelian_detail.id_varian', '=', 'produk_varian.id_varian')
            ->join('produk', 'produk_varian.id_produk', '=', 'produk.id_produk')
            ->join('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
            ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id_pembelian')
            ->whereBetween('pembelian.created_at', [$startDate, $endDate])
            ->where('pembelian.status_pembayaran', 'Sudah Dibayar')
            ->selectRaw('kategori.nama_kategori, SUM(pembelian_detail.subtotal) as total')
            ->groupBy('kategori.id_kategori', 'kategori.nama_kategori')
            ->orderByDesc('total')
            ->get();

        $labelKategori = $penjualanKategori->pluck('nama_kategori')->toArray();
        $dataKategori  = $penjualanKategori->pluck('total')->map(fn($v) => (float)$v)->toArray();

        // ─── 6. Produk Terlaris ────────────────────────────────────────────
        $produkTerlaris = PembelianDetail::join('produk_varian', 'pembelian_detail.id_varian', '=', 'produk_varian.id_varian')
            ->join('produk', 'produk_varian.id_produk', '=', 'produk.id_produk')
            ->join('pembelian', 'pembelian_detail.id_pembelian', '=', 'pembelian.id_pembelian')
            ->whereBetween('pembelian.created_at', [$startDate, $endDate])
            ->where('pembelian.status_pembayaran', 'Sudah Dibayar')
            ->selectRaw('
                produk.nama_produk,
                produk.foto_produk,
                SUM(pembelian_detail.jumlah) as total_terjual,
                SUM(pembelian_detail.subtotal) as total_pendapatan
            ')
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.foto_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $labelProdukTerlaris = $produkTerlaris
            ->pluck('nama_produk')
            ->toArray();

        $dataProdukTerlaris = $produkTerlaris
            ->pluck('total_terjual')
            ->toArray();

        // ─── 7. Tabel Transaksi Terkini (paginate, withQueryString agar filter tetap) ──
        $transaksiTerkini = Pembelian::with('user')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($item) {

            // NORMALISASI STATUS PEMBAYARAN
            $item->status_pembayaran = match (strtolower(trim($item->status_pembayaran))) {
                'sudah dibayar' => 'Sudah Dibayar',
                'sudah dibayar ' => 'Sudah Dibayar',
                'belum dibayar' => 'Belum Dibayar',
                default => $item->status_pembayaran,
            };

            // NORMALISASI STATUS PESANAN
            $item->status_pesanan = match (strtolower(trim($item->status_pesanan))) {
                'pending' => 'Pending',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
                default => $item->status_pesanan,
            };

            // NORMALISASI STATUS PENGIRIMAN
            $item->status_kirim = match (strtolower(trim($item->status_kirim ?? ''))) {
                'belum dikirim' => 'Belum Dikirim',
                'dikirim' => 'Dikirim',
                'diterima' => 'Diterima',
                default => $item->status_kirim ?? '-',
            };

            return $item;
        });
        return view('admin.laporan.index', compact(
            'range', 'startDate', 'endDate',
            'totalPendapatan',
            'produkTerjual',
            'pelangganAktif',
            'belumDibayar',
            'labelHarian',
            'dataHarian',
            'dataProdukHarian',
            'labelKategori',
            'dataKategori',
            'transaksiTerkini',
            'produkTerlaris',
            'labelProdukTerlaris',
            'dataProdukTerlaris',
            'rataRataTransaksi'
        ));
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->resolveRange($request);
        $filename = 'laporan-penjualan-' . now()->format('Ymd-His') . '.xlsx';
        ActivityHelper::log(
            'Export Excel',
            'Export laporan penjualan Excel'
        );

        return Excel::download(
            new LaporanExport($startDate, $endDate),
            $filename
        );
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        [$startDate, $endDate] = $this->resolveRange($request);

        $pesanan = Pembelian::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('created_at')
            ->get();

        $totalPendapatan = $pesanan
            ->where('status_pembayaran', 'Sudah Dibayar')
            ->sum('total_harga');

        $pdf = PDF::loadView('admin.laporan.pdf', [
            'pesanan' => $pesanan,
            'totalPendapatan' => $totalPendapatan,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->setPaper('a4', 'landscape');

        ActivityHelper::log(
            'Export PDF',
            'Export laporan penjualan PDF'
        );

        return $pdf->download('laporan-penjualan-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Import Excel (update status massal)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
        ], [
            'file.required' => 'File Excel wajib dipilih.',
            'file.mimes'    => 'Format file harus .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $import = new LaporanImport;
            Excel::import($import, $request->file('file'));

            ActivityHelper::log(
                'Import Excel',
                $import->updated . ' data pesanan diperbarui'
            );

            $msg = "{$import->updated} pesanan berhasil diperbarui.";

            if (!empty($import->skipped)) {
                $msg .= ' Baris dilewati: ' . implode(' | ', $import->skipped);
                return back()->with('error', $msg);
            }

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Helper: resolve start & end date dari request
     */
    private function resolveRange(Request $request): array
    {
        $range = $request->input('range', '30');
        if ($range === 'custom') {
            $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
            $endDate   = Carbon::parse($request->input('end_date'))->endOfDay();
        } else {
            $startDate = Carbon::today()->subDays((int)$range - 1)->startOfDay();
            $endDate   = Carbon::today()->endOfDay();
        }
        return [$startDate, $endDate];
    }
}