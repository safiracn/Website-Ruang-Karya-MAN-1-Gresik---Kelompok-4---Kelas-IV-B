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

                if (!preg_match('/(\d+)$/', $kode, $matches)) {
                    $this->skipped[] = "Baris ".($i+2)." - format kode '$kode' tidak dikenali";
                    continue;
                }

                $pembelian = Pembelian::find((int) $matches[1]);
                if (!$pembelian) {
                    $this->skipped[] = "Kode $kode tidak ditemukan";
                    continue;
                }

                $statusPembayaran = $this->normalizePembayaran($row['status_pembayaran'] ?? null);
                $statusPesanan    = $this->normalizePesanan($row['status_pesanan'] ?? null);
                $statusKirim      = $this->normalizeKirim($row['status_pengiriman'] ?? null);

                // ── Hanya simpan yang benar-benar berubah ──────────────────
                $updateData = [];

                if ($statusPembayaran && $statusPembayaran !== $pembelian->status_pembayaran) {
                    $updateData['status_pembayaran'] = $statusPembayaran;
                }
                if ($statusPesanan && $statusPesanan !== $pembelian->status_pesanan) {
                    $updateData['status_pesanan'] = $statusPesanan;
                }
                if ($statusKirim && $statusKirim !== $pembelian->status_kirim) {
                    $updateData['status_kirim'] = $statusKirim;
                }

                // Hanya update & hitung jika ada yang benar-benar berubah
                if (!empty($updateData)) {
                    $pembelian->update($updateData);
                    $this->updated++;
                }
            }
        });
    }

    private function normalizePembayaran($value)
    {
        $v = strtolower(trim($value ?? ''));
        return match ($v) {
            'sudah dibayar', 'lunas' => 'Sudah dibayar',   // d kecil
            'belum dibayar', 'belum' => 'Belum Dibayar',   // D kapital
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
        $v = strtolower(trim($value ?? ''));
        return match ($v) {
            'belum dikirim', 'belum' => 'Belum dikirim',  // d kecil
            'dikirim'                => 'Dikirim',
            'diterima'               => 'Diterima',
            default => null
        };
    }
}