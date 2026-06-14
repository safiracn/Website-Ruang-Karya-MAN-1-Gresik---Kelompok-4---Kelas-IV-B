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

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        return Pembelian::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Kolom export DISAMAKAN dengan tabel "Transaksi Terkini" di halaman Laporan:
     * Order ID | Tanggal Order | Customer | Status Pembayaran | Status Pesanan | Status Pengiriman | Total
     */
    public function headings(): array
    {
        return [
            'Order ID',
            'Tanggal Order',
            'Customer',
            'Status Pembayaran',
            'Status Pesanan',
            'Status Pengiriman',
            'Total',
        ];
    }

    public function map($row): array
    {
        $orderId = 'RK' . str_pad($row->id_pembelian, 5, '0', STR_PAD_LEFT);

        return [
            $orderId,
            Carbon::parse($row->created_at)->format('d/m/Y'),
            $row->nama_penerima,           // ← Customer = nama_penerima, BUKAN nama akun
            $row->status_pembayaran,
            $row->status_pesanan,
            $row->status_kirim,
            (float) $row->total_harga,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function styles(Worksheet $sheet): array
    {
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF00266B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ];

        // Kolom D, E, F = Status Pembayaran, Status Pesanan, Status Pengiriman
        // ditandai kuning sebagai "kolom yang bisa diubah saat import"
        $editableStyle = [
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF9C4'],
            ],
        ];

        $sheet->getStyle('D2:F' . $sheet->getHighestRow())
              ->applyFromArray($editableStyle);

        return [
            1 => $headerStyle,
        ];
    }
}