<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pesanan Berhasil - Ruang Karya MAN 1 Gresik</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-navy': '#00266B',
                        'brand-yellow': '#FDC003',
                        'brand-bg': '#F8F9FA',
                        'brand-text-muted': '#6B7280',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .success-icon-shadow {
            box-shadow: 0 10px 25px -5px rgba(253, 192, 3, 0.4);
        }
    </style>
</head>

@php
    // 1. Otomatisasi Kode Pembelian (Menggunakan ID Pembelian dari DB)
    $idPesanan = $pesanan->id_pembelian ?? 0;
    $kodePesanan = 'RK260605-' . sprintf('%04d', $idPesanan);
    
    // 2. Mengambil Nama Akun Pembeli (Siswa yang Login)
    $namaAkunPembeli = Auth::user()->name ?? Auth::user()->nama_lengkap ?? 'Pelanggan';
    
    // 3. Mengambil Nama Penerima (Sesuai kolom 'nama_penerima' di database pembelian kamu)
    $namaPenerimaPaket = $pesanan->nama_penerima ?? 'Tidak Tercatat';
    
    // 4. Ambil total tagihan
    $totalTagihan = $pesanan->total_harga ?? $pesanan->total_final ?? 0;

    // 5. Susun pesan otomatis Broadcast WhatsApp ke Admin (Menampilkan Dua-duanya)
    $pesanWA = urlencode(
        "Halo Admin Ruang Karya MAN 1 Gresik,\n\n" .
        "Saya ingin melakukan konfirmasi pembayaran untuk pesanan berikut:\n" .
        "👉 *Kode Pesanan:* $kodePesanan\n" .
        "👤 *Akun Pemesan:* $namaAkunPembeli\n" .
        "📦 *Nama Penerima:* $namaPenerimaPaket\n" .
        "💰 *Total Tagihan:* Rp " . number_format($totalTagihan, 0, ',', '.') . "\n\n" .
        "Mohon segera diverifikasi dan diproses ya min. Terima kasih!"
    );
@endphp

<body class="font-sans bg-slate-50 text-slate-800">

<div class="bg-gray-50 w-full max-w-6xl mx-auto rounded-xl shadow-2xl overflow-hidden flex flex-col my-10 border border-slate-200">

    {{-- Header Instansi --}}
    <header class="bg-white px-6 py-5 flex items-center gap-4 border-b border-slate-100">
        <img src="{{ asset('image/LOGO.jpeg') }}" alt="Logo" class="h-14 w-14 object-contain">
        <div>
            <h1 class="text-xl font-bold text-blue-900 leading-tight">Ruang Karya</h1>
            <h2 class="text-sm italic font-semibold text-blue-900">MAN 1 Gresik</h2>
            <p class="text-[11px] md:text-sm italic text-blue-900 leading-snug">
                Islami, Cerdas, Unggul, Kompetitif, &amp; Peduli Lingkungan
            </p>
        </div>
    </header>

    <div class="p-6 space-y-8">
        {{-- Status Header Berhasil --}}
        <section class="text-center py-6 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-brand-yellow rounded-full flex items-center justify-center success-icon-shadow">
                    <svg class="h-8 w-8 text-brand-navy" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-black text-blue-900 mb-2">Pesanan Berhasil</h1>
            <p class="text-slate-500 max-w-xl mx-auto text-sm leading-relaxed px-4">
                Pesanan Anda telah kami terima dan sedang menunggu verifikasi pembayaran untuk diproses ke tahap produksi.
            </p>

            {{-- Badge Nomor RK Otomatis --}}
            <div class="mt-4 inline-block bg-blue-900 text-white px-6 py-2.5 rounded-lg font-mono font-bold text-sm tracking-wider shadow-sm">
                KODE PESANAN : {{ $kodePesanan }}
            </div>
        </section>

       @php

$statusTitle = '';
$statusDesc = '';
$statusColor = '';

if($pesanan->status_pesanan == 'Dibatalkan'){

    $statusTitle = 'Pesanan Dibatalkan';
    $statusDesc  = 'Pesanan telah dibatalkan dan tidak akan diproses lebih lanjut.';
    $statusColor = 'red';

}elseif($pesanan->status_pesanan == 'Menunggu Konfirmasi Pembatalan'){

    $statusTitle = 'Menunggu Konfirmasi Pembatalan';
    $statusDesc  = 'Permintaan pembatalan telah dikirim ke admin. Mohon tunggu proses verifikasi.';
    $statusColor = 'orange';

}elseif($pesanan->status_pembayaran == 'Belum Dibayar'){

    $statusTitle = 'Menunggu Pembayaran';
    $statusDesc  = 'Silakan kirim bukti pembayaran ke WhatsApp admin.';
    $statusColor = 'yellow';

}elseif($pesanan->status_kirim == 'Diterima'){

    $statusTitle = 'Pesanan Selesai';
    $statusDesc  = 'Pesanan telah diterima. Terima kasih telah berbelanja.';
    $statusColor = 'green';

}elseif($pesanan->status_kirim == 'Dikirim'){

    $statusTitle = 'Pesanan Dikirim';
    $statusDesc  = 'Pesanan sedang dalam perjalanan menuju alamat Anda.';
    $statusColor = 'blue';

}elseif($pesanan->status_pesanan == 'Diproses'){

    $statusTitle = 'Sedang Diproses';
    $statusDesc  = 'Pembayaran telah dikonfirmasi dan pesanan sedang diproses.';
    $statusColor = 'blue';

}else{

    $statusTitle = 'Menunggu Konfirmasi';
    $statusDesc  = 'Admin sedang memverifikasi pembayaran Anda.';
    $statusColor = 'orange';

}

@endphp
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

    <div class="flex items-center gap-4">

        <div class="
            w-16 h-16 rounded-full flex items-center justify-center

            @if($statusColor=='green')
                bg-green-100 text-green-600
            @elseif($statusColor=='blue')
                bg-blue-100 text-blue-600
            @elseif($statusColor=='yellow')
                bg-yellow-100 text-yellow-600
            @else
                bg-orange-100 text-orange-600
            @endif
        ">
            <i class="fa-solid fa-box text-xl"></i>
        </div>

        <div>
            <h3 class="text-2xl font-bold text-blue-900">
                {{ $statusTitle }}
            </h3>

            <p class="text-slate-500 mt-1">
                {{ $statusDesc }}
            </p>
        </div>

    </div>

</div>

<div class="grid md:grid-cols-3 gap-4 mt-5">

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-xs text-slate-400 uppercase">
            Pembayaran
        </p>

        <p class="font-semibold mt-1">
            {{ $pesanan->status_pembayaran }}
        </p>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-xs text-slate-400 uppercase">
            Pengiriman
        </p>

        <p class="font-semibold mt-1">
            {{ $pesanan->status_kirim }}
        </p>
    </div>

    <div class="bg-white p-4 rounded-xl border">
        <p class="text-xs text-slate-400 uppercase">
            Tanggal Pembelian
        </p>

        <p class="font-semibold mt-1">
            {{ \Carbon\Carbon::parse($pesanan->created_at)->format('d M Y, H:i') }}
        </p>
    </div>

</div>

        

        {{-- Grid Utama: Rincian Produk & Ringkasan Harga --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Bagian Kiri: Rincian Produk --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-blue-900 text-white flex justify-between items-center">
                    <h2 class="font-bold uppercase tracking-wider text-sm">Rincian Produk</h2>
                    <span class="text-xs bg-white/20 px-2 py-0.5 rounded font-mono">{{ $kodePesanan }}</span>
                </div>
                
                <div class="p-5 space-y-4 max-h-[380px] overflow-y-auto">
                    @forelse($detailPesanan ?? [] as $item)
                    <div class="flex gap-4 border-b border-slate-100 pb-4 last:border-b-0 last:pb-0 items-center">
                        <div class="w-20 h-20 flex-shrink-0 bg-slate-50 rounded-lg overflow-hidden border border-slate-200">
                            <img alt="{{ $item->nama_produk ?? 'Produk' }}" 
                                 class="w-full h-full object-cover" 
                                 src="{{ asset('image/' . ($item->foto_produk ?? 'default.jpg')) }}"/>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-base font-bold text-black mb-0.5">{{ $item->nama_produk ?? 'Nama Produk' }}</h3>
                            @if(isset($item->nama_varian) || isset($item->varian))
                                <p class="text-xs text-slate-400 mb-1">Varian: {{ $item->nama_varian ?? $item->varian }}</p>
                            @endif
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded font-semibold">{{ $item->jumlah ?? 1 }}x Unit</span>
                                <span class="font-bold text-yellow-600">
                                    Rp {{ number_format(($item->harga_satuan ?? $item->subtotal ?? 0) * ($item->jumlah ?? 1), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-sm text-slate-400 py-6 italic">Belum ada rincian produk.</p>
                    @endforelse
                </div>
            </div>

            {{-- Bagian Kanan: Ringkasan Harga & Tombol WA Besar --}}
            <div class="flex flex-col gap-6">
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                    <h2 class="text-blue-900 uppercase font-bold mb-4 tracking-wider text-sm">Ringkasan Harga</h2>
                    <div class="flex justify-between items-center mb-3 text-sm">
                        <span class="text-slate-500">Subtotal Produk</span>
                        <span class="font-semibold text-slate-700">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-300 pt-4 flex justify-between items-center">
                        <span class="text-blue-900 font-bold">Total Tagihan</span>
                        <span class="text-2xl font-black text-blue-900">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                {{-- Tombol Utama WhatsApp Berukuran Penuh Sesuai Form --}}
                <div class="bg-blue-900 text-white p-5 rounded-xl shadow-lg border border-blue-950">
                    <p class="text-xs md:text-sm text-center leading-relaxed mb-4 opacity-90">
                        Silakan lakukan konfirmasi bukti transfer Anda kepada admin melalui tautan WhatsApp di bawah ini.
                    </p>
                    <a class="flex items-center justify-center gap-3 w-full bg-yellow-500 hover:bg-yellow-400 transition-all py-3.5 px-4 rounded-xl text-blue-900 font-black tracking-wide shadow-md hover:scale-[1.01] active:scale-[0.99] uppercase text-sm" 
                       href="https://wa.me/6285859249749?text={{ $pesanWA }}" target="_blank">
                        <i class="fab fa-whatsapp text-lg"></i>
                        Konfirmasi via WhatsApp
                    </a>
                </div>
            </div>
        </section>

        @if($pesanan->status_kirim == 'Dikirim')

        <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl p-4 text-sm text-center">

            Pesanan sedang dalam proses pengiriman dan tidak dapat dibatalkan.

        </div>

        @endif

        {{-- Navigasi Bawah Kembali/Riwayat --}}
       {{-- Navigasi Bawah --}}
<section class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-xl mx-auto pt-4 border-t border-slate-200">

    <a href="{{ route('riwayat') }}"
       class="flex items-center justify-center w-full sm:w-1/2 border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 py-3 rounded-xl font-bold text-xs text-center shadow-sm transition">
        Kembali ke Riwayat
    </a>

    @if(
        $pesanan->status_kirim == 'Belum dikirim'
        &&
        $pesanan->status_pesanan != 'Dibatalkan'
    )

    <form action="{{ route('pesanan.batal',$pesanan->id_pembelian) }}"
          method="POST"
          class="w-full sm:w-1/2"
          onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">

        @csrf

        <button
            type="submit"
            class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-xl font-bold text-xs shadow-sm transition">

            Batalkan Pesanan

        </button>

    </form>

    @endif

</section>
    </div>

    {{-- Footer Instansi --}}
    <footer class="p-4 text-center border-t border-slate-200 bg-white">
        <p class="text-gray-400 text-xs tracking-widest uppercase">&copy; 2026 Ruang Karya MAN 1 Gresik</p>
    </footer>
</div>

</body>
</html>