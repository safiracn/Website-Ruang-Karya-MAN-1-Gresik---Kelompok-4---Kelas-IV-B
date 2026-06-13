@extends('layouts.admin')

@section('title','Manajemen Pesanan')
@section('activeMenu','pesanan')
@section('pageDesc','Kelola seluruh transaksi pelanggan.')

@section('content')

@if(session('success'))
<div class="mb-4 bg-green-100 text-green-700 p-4 rounded-xl">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-2 gap-5 mb-6">

    <div class="bg-white rounded-2xl p-5 shadow">
        <p class="text-slate-500">Total Pesanan</p>
        <h2 class="text-3xl font-bold text-blue-900">
            {{ $totalPesanan }}
        </h2>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow">
        <p class="text-slate-500">Belum Dibayar</p>
        <h2 class="text-3xl font-bold text-red-500">
            {{ $belumDibayar }}
        </h2>
    </div>

</div>

<div class="bg-white rounded-2xl p-6 shadow">

 {{-- FILTER --}}
<div class="bg-white rounded-2xl px-6 py-5 border border-slate-200 mb-4">

    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-filter text-slate-400"></i>
            <span class="text-sm font-semibold text-slate-600">Filters:</span>
        </div>
        <a href="{{ route('admin.pesanan') }}"
           class="text-blue-500 text-sm hover:underline">
            Clear all filters
        </a>
    </div>

    <form method="GET">
        <div class="flex flex-wrap gap-3 items-end">

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] uppercase font-bold text-slate-400 tracking-wide">
                    Status Pembayaran
                </label>
                <select name="status_pembayaran"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    <option value="">Semua</option>
                    <option value="Belum Dibayar">Belum Dibayar</option>
                    <option value="Sudah dibayar">Sudah Dibayar</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] uppercase font-bold text-slate-400 tracking-wide">
                    Status Pesanan
                </label>
                <select name="status_pesanan"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    <option value="">Semua</option>
                    <option value="Pending">Pending</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] uppercase font-bold text-slate-400 tracking-wide">
                    Status Pengiriman
                </label>
                <select name="status_kirim"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                    <option value="">Semua</option>
                    <option value="Belum dikirim">Belum dikirim</option>
                    <option value="Dikirim">Dikirim</option>
                    <option value="Diterima">Diterima</option>
                </select>
            </div>

            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] uppercase font-bold text-slate-400 tracking-wide opacity-0">
                    &nbsp;
                </label>
                <button type="submit"
                    class="bg-blue-900 hover:bg-blue-800 text-white rounded-xl px-5 py-2 text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
            </div>

        </div>
    </form>

</div>

{{-- SEARCH --}}
<div class="bg-white rounded-2xl px-5 py-3 border border-slate-200 mb-5">
    <form method="GET">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama pelanggan atau kode order..."
                class="w-full border-0 bg-transparent focus:ring-0 text-sm text-slate-700 placeholder:text-slate-400">
        </div>
    </form>
</div>

    <div class="overflow-x-auto">
    <table class="w-full">

        <thead>
            <tr class="bg-slate-50 rounded-xl">
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold rounded-l-xl">Order ID</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold">Customer</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold">Total</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold">Status Pembayaran</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold">Status Pesanan</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold">Status Pengiriman</th>
                <th class="py-3 px-4 text-left text-[11px] uppercase tracking-wider text-slate-400 font-bold rounded-r-xl">Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pesanan as $row)
            <tr class="hover:bg-blue-50/40 transition group">

                {{-- ORDER ID --}}
                <td class="py-4 px-4">
                    <span class="font-bold text-blue-900 text-sm bg-blue-50 px-2.5 py-1 rounded-lg">
                        #RK{{ str_pad($row->id_pembelian,5,'0',STR_PAD_LEFT) }}
                    </span>
                </td>

                {{-- CUSTOMER --}}
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-800 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                            {{ strtoupper(substr($row->nama_penerima,0,1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 text-sm">{{ $row->nama_penerima }}</p>
                            <p class="text-xs text-slate-400">{{ $row->no_telp_penerima }}</p>
                        </div>
                    </div>
                </td>

                {{-- TOTAL --}}
                <td class="py-4 px-4">
                    <span class="font-bold text-slate-800 text-sm">
                        Rp {{ number_format($row->total_harga,0,',','.') }}
                    </span>
                </td>

                <form action="{{ route('admin.pesanan.update',$row->id_pembelian) }}" method="POST">
                    @csrf

                    {{-- STATUS PEMBAYARAN --}}
                    <td class="py-4 px-4">
                        <select name="status_pembayaran"
                            class="text-xs font-semibold rounded-full border-0 px-3 py-1.5 cursor-pointer
                            {{ $row->status_pembayaran == 'Sudah dibayar'
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-orange-100 text-orange-600' }}">
                            <option value="Belum Dibayar" {{ $row->status_pembayaran=='Belum Dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="Sudah dibayar" {{ $row->status_pembayaran=='Sudah dibayar' ? 'selected' : '' }}>Sudah Dibayar</option>
                        </select>
                    </td>

                    {{-- STATUS PESANAN --}}
                    <td class="py-4 px-4">
                        <select name="status_pesanan"
                            class="text-xs font-semibold rounded-full border-0 px-3 py-1.5 cursor-pointer
                            {{ $row->status_pesanan == 'Selesai' ? 'bg-emerald-100 text-emerald-700'
                                : ($row->status_pesanan == 'Diproses' ? 'bg-blue-100 text-blue-700'
                                : ($row->status_pesanan == 'Dibatalkan' ? 'bg-red-100 text-red-600'
                                : 'bg-slate-100 text-slate-600')) }}">
                            <option value="Pending" {{ $row->status_pesanan=='Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Diproses" {{ $row->status_pesanan=='Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai" {{ $row->status_pesanan=='Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ $row->status_pesanan=='Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </td>

                    {{-- STATUS PENGIRIMAN --}}
                    <td class="py-4 px-4">
                        <select name="status_kirim"
                            class="text-xs font-semibold rounded-full border-0 px-3 py-1.5 cursor-pointer
                            {{ $row->status_kirim == 'Diterima' ? 'bg-emerald-100 text-emerald-700'
                                : ($row->status_kirim == 'Dikirim' ? 'bg-blue-100 text-blue-700'
                                : 'bg-slate-100 text-slate-600') }}">
                            <option value="Belum dikirim" {{ $row->status_kirim=='Belum dikirim' ? 'selected' : '' }}>Belum dikirim</option>
                            <option value="Dikirim" {{ $row->status_kirim=='Dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="Diterima" {{ $row->status_kirim=='Diterima' ? 'selected' : '' }}>Diterima</option>
                        </select>
                    </td>

                    {{-- AKSI --}}
                    <td class="py-4 px-4">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-blue-900 hover:bg-blue-800 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                    </td>

                </form>
            </tr>
            @endforeach

        </tbody>
    </table>
</div>

</div>

@endsection