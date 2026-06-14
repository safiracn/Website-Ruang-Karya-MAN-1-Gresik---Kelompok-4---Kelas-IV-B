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

                $arr = $row->toArray();

                // ── Normalisasi key: lowercase, trim, spasi/dash -> underscore ──
                $normalized = [];
                foreach ($arr as $key => $value) {
                    $normKey = strtolower(trim(str_replace([' ', '-'], '_', (string) $key)));
                    $normalized[$normKey] = $value;
                }

                // Reset array ke index 0,1,2,... untuk fallback posisi kolom
                $byPosition = array_values($arr);

                // ── Order ID: coba beberapa kemungkinan key, fallback ke kolom ke-1 (index 0) ──
                $kode = $normalized['order_id']
                    ?? $normalized['orderid']
                    ?? $normalized['order_i_d']
                    ?? $normalized['kode_pesanan']
                    ?? ($byPosition[0] ?? null);

                $kode = is_string($kode) ? trim($kode) : $kode;

                if (empty($kode)) {
                    $foundKeys = implode(', ', array_keys($normalized));
                    $this->skipped[] = "Baris ".($i+2)." - Order ID kosong (kolom terbaca: $foundKeys)";
                    continue;
                }

                // Ekstrak angka dari kode: RK00008 -> 8
                if (!preg_match('/(\d+)$/', $kode, $matches)) {
                    $this->skipped[] = "Baris ".($i+2)." - format Order ID '$kode' tidak dikenali";
                    continue;
                }

                $pembelian = Pembelian::find((int) $matches[1]);

                if (!$pembelian) {
                    $this->skipped[] = "Order ID $kode tidak ditemukan";
                    continue;
                }

                // ── Status: coba key, fallback ke posisi kolom (3, 4, 5 = index ke-4,5,6) ──
                $statusPembayaranRaw = $normalized['status_pembayaran'] ?? ($byPosition[3] ?? null);
                $statusPesananRaw    = $normalized['status_pesanan']    ?? ($byPosition[4] ?? null);
                $statusKirimRaw      = $normalized['status_pengiriman'] ?? ($byPosition[5] ?? null);

                $statusPembayaran = $this->normalizePembayaran($statusPembayaranRaw);
                $statusPesanan    = $this->normalizePesanan($statusPesananRaw);
                $statusKirim      = $this->normalizeKirim($statusKirimRaw);

                // Hanya simpan & hitung yang BENAR-BENAR berubah
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
            'sudah dibayar', 'lunas'        => 'Sudah dibayar',
            'belum dibayar', 'belum'        => 'Belum Dibayar',
            'dana dikembalikan', 'refund'   => 'Dana dikembalikan',
            default => null
        };
    }

    private function normalizePesanan($value)
    {
        $v = strtolower(trim($value ?? ''));
        return match ($v) {
            'pending'                          => 'Pending',
            'diproses'                         => 'Diproses',
            'selesai'                          => 'Selesai',
            'dibatalkan', 'batal'              => 'Dibatalkan',
            'menunggu konfirmasi pembatalan'   => 'Menunggu Konfirmasi Pembatalan',
            default => null
        };
    }

    private function normalizeKirim($value)
    {
        $v = strtolower(trim($value ?? ''));
        return match ($v) {
            'belum dikirim', 'belum' => 'Belum dikirim',
            'dikirim'                 => 'Dikirim',
            'diterima'                => 'Diterima',
            default => null
        };
    }
}