@extends('layouts.app')

@section('title', 'Riwayat Pesanan - Ruang Karya')

@section('content')

<main class="max-w-5xl mx-auto px-6 py-12 min-h-screen">
    <h2 class="text-4xl font-serif-heading font-bold text-blue-900 mb-2">Riwayat Pesanan</h2>

    {{-- Tab Filter --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
        <div class="flex bg-slate-200 p-1 rounded-xl">
            <a href="{{ route('riwayat', ['tab' => 'sedang-dipesan']) }}"
               class="px-6 py-2 rounded-lg font-bold transition {{ $tab !== 'selesai' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-500' }}">
                Sedang Dipesan
            </a>
            <a href="{{ route('riwayat', ['tab' => 'selesai']) }}"
               class="px-6 py-2 rounded-lg font-bold transition {{ $tab === 'selesai' ? 'bg-white text-blue-900 shadow-sm' : 'text-slate-500' }}">
                Selesai
            </a>
        </div>
    </div>

    {{-- List Pesanan --}}
    <div class="space-y-4">
        @forelse($pesanan as $row)
            <div class="order-item bg-white p-5 rounded-2xl border border-slate-200 flex flex-col md:flex-row items-center gap-6">
                <img src="{{ asset('image/' . ($row->foto_produk ?? 'default.jpg')) }}"
                     class="w-20 h-20 object-cover rounded-lg">
                <div class="flex-1 text-center md:text-left">
                    <h4 class="font-bold text-blue-900">{{ $row->nama_produk ?? 'Produk tidak ditemukan' }}</h4>
                    <p class="text-blue-900 font-black text-lg">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</p>
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">
                        {{ $row->status_pesanan }}
                    </span>
                </div>
                <div class="flex gap-2">
                    {{-- NYAMBUNG DI SINI: Mengarahkan href ke halaman sukses/detail pembayarannya --}}
                    <a href="{{ route('order.sukses', $row->id_pembelian) }}" 
                       class="text-xs px-4 py-2 border border-blue-900 text-blue-900 rounded-lg font-bold hover:bg-slate-50 transition">
                        Detail
                    </a>
                    
                    @if($row->status_pesanan === 'Selesai')
                        <a href="{{ route('produk.detail', $row->id_produk) }}"
                           class="text-xs px-4 py-2 bg-yellow-500 text-blue-900 rounded-lg font-bold">Beli Lagi</a>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 py-10 italic">Belum ada pesanan di kategori ini.</p>
        @endforelse
    </div>
</main>

@endsection