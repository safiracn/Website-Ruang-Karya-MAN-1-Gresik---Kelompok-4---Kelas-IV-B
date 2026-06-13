<?php

namespace App\Imports;

use App\Models\Pembelian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LaporanImport implements ToCollection, WithHeadingRow
{
    public $updated = 0;
    public $skipped = [];

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $i => $row) {

                $kode = $row['kode_pesanan'] ?? null;

                if (!$kode) {
                    $this->skipped[] = "Baris ".($i+2)." (kode kosong)";
                    continue;
                }

                $pembelian = Pembelian::where('id_pembelian', $kode)
                    ->orWhere('kode_pesanan', $kode)
                    ->first();

                if (!$pembelian) {
                    $this->skipped[] = "Kode $kode tidak ditemukan";
                    continue;
                }

                $statusPembayaran = $this->normalizePembayaran($row['status_pembayaran'] ?? null);
                $statusPesanan    = $this->normalizePesanan($row['status_pesanan'] ?? null);
                $statusKirim      = $this->normalizeKirim($row['status_pengiriman'] ?? null);

                $pembelian->update([
                    'status_pembayaran' => $statusPembayaran,
                    'status_pesanan'    => $statusPesanan,
                    'status_kirim'      => $statusKirim,
                ]);

                $this->updated++;
            }
        });
    }

    private function normalizePembayaran($value)
    {
        $v = strtolower(trim($value));

        return match ($v) {
            'sudah dibayar' => 'Sudah Dibayar',
            'belum dibayar' => 'Belum Dibayar',
            default => null
        };
    }

    private function normalizePesanan($value)
    {
        $v = strtolower(trim($value));

        return match ($v) {
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => null
        };
    }

    private function normalizeKirim($value)
    {
        $v = strtolower(trim($value));

        return match ($v) {
            'belum dikirim' => 'Belum Dikirim',
            'dikirim' => 'Dikirim',
            'diterima' => 'Diterima',
            default => null
        };
    }
}