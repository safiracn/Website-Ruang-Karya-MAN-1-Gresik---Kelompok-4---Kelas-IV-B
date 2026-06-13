<?php

namespace App\Exports;

use App\Models\Pembelian;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected int    $rowNumber = 0;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        return Pembelian::with(['user', 'details.varian.produk'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Pesanan',
            'Tanggal Pembelian',
            'Nama Akun Pembeli',
            'Nama Penerima',
            'No. Telp Penerima',
            'Kota/Kabupaten',
            'Metode Pengiriman',
            'Status Pembayaran',
            'Status Pesanan',
            'Status Pengiriman',
            'Produk (Ringkasan)',
            'Total Harga (Rp)',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $kodePesanan = 'RK' . str_pad($row->id_pembelian, 5, '0', STR_PAD_LEFT);

        // Rangkum nama produk dari detail
        $produkList = $row->details->map(function ($d) {
            $namaProduk = $d->varian?->produk?->nama_produk ?? 'Produk';
            $namaVarian = $d->varian?->nama_varian ?? '-';
            return "{$namaProduk} ({$namaVarian}) x{$d->jumlah}";
        })->implode('; ');

        return [
            $this->rowNumber,
            $kodePesanan,
            Carbon::parse($row->created_at)->format('d/m/Y H:i'),
            $row->user?->nama_lengkap ?? '-',
            $row->nama_penerima,
            $row->no_telp_penerima,
            $row->kota_kabupaten,
            $row->metode_pengiriman,
            $row->status_pembayaran,
            $row->status_pesanan,
            $row->status_kirim,
            $produkList,
            (float) $row->total_harga,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00266B'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}