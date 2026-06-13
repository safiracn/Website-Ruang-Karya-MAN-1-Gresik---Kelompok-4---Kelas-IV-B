@extends('layouts.admin')

@section('title', 'Daftar Produk')
@section('activeMenu', 'dashboard')
@section('pageDesc', 'Kelola dan pantau semua inventaris produk karya siswa Anda.')

@section('content')

{{-- Notifikasi --}}
@if(session('error_hapus'))
    <div id="notif-error-hapus"
         class="mb-6 rounded-xl border border-yellow-300 bg-yellow-100 px-4 py-3 text-[15px] font-semibold text-yellow-800 shadow-sm">
        {{ session('error_hapus') }}
    </div>
@endif

@if(session('success_tambah'))
    <div id="notif-tambah"
         class="mb-6 rounded-xl border border-green-300 bg-green-100 px-4 py-3 text-[15px] font-semibold text-green-700 shadow-sm">
        Produk berhasil ditambahkan.
    </div>
@endif

@if(session('success_update'))
    <div id="notif-update"
         class="mb-6 rounded-xl border border-blue-300 bg-blue-100 px-4 py-3 text-[15px] font-semibold text-blue-700 shadow-sm">
        Produk berhasil diperbarui.
    </div>
@endif

@if(session('success_hapus'))
    <div id="notif-hapus"
         class="mb-6 rounded-xl border border-red-300 bg-red-100 px-4 py-3 text-[15px] font-semibold text-red-700 shadow-sm">
        Produk "<span class="font-bold">{{ session('success_hapus') }}</span>" berhasil dihapus.
    </div>
@endif

{{-- Cards Statistik --}}
<section class="mb-7 grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="flex items-center justify-between rounded-2xl border-l-4 border-blue-900 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Total Produk</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900">{{ $totalProduk }}</h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-blue-900">
            <i class="fa-solid fa-box-archive text-xl"></i>
        </div>
    </div>

    <div class="flex items-center justify-between rounded-2xl border-l-4 border-green-500 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Stok Tersedia</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900">{{ $stokTersedia }}</h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600">
            <i class="fa-solid fa-circle-check text-xl"></i>
        </div>
    </div>

    <div class="flex items-center justify-between rounded-2xl border-l-4 border-red-500 bg-white px-7 py-7 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
        <div>
            <p class="text-[15px] text-slate-500">Stok Habis</p>
            <h3 class="mt-2 text-[28px] font-bold text-blue-900">{{ $stokHabis }}</h3>
        </div>
        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-red-500">
            <i class="fa-solid fa-circle-exclamation text-xl"></i>
        </div>
    </div>
</section>

{{-- Tabel Produk --}}
<section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

    {{-- Search + Tambah --}}
    <div class="mb-6 flex items-center justify-between gap-4">
        <form action="{{ route('admin.dashboard') }}" method="GET" class="w-[360px]">
            <div class="group flex h-12 items-center gap-3 rounded-xl bg-slate-100 px-4 transition hover:bg-slate-200">
                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                <input type="text" name="search" value="{{ $keyword }}"
                       placeholder="Cari nama produk..."
                       class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400">
            </div>
        </form>

        <a href="{{ route('admin.produk.tambah') }}"
           class="inline-flex h-14 items-center gap-2 rounded-2xl bg-yellow-500 px-6 text-[15px] font-semibold text-blue-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-yellow-400 hover:shadow-md">
            <i class="fa-solid fa-circle-plus"></i>
            <span>Tambah Produk</span>
        </a>
    </div>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="w-full table-fixed border-collapse">
            <thead>
                <tr class="border-b border-slate-200 text-left text-[14px] font-semibold uppercase tracking-wide text-slate-500">
                    <th class="w-[39%] px-4 py-4">Informasi Produk</th>
                    <th class="w-[16%] px-4 py-4">Kategori</th>
                    <th class="w-[16%] px-4 py-4">Harga</th>
                    <th class="w-[17%] px-4 py-4">Stok</th>
                    <th class="w-[12%] px-4 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $row)
                    <tr class="border-b border-slate-200 align-middle transition hover:bg-slate-50">
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center gap-4">
                                <img src="{{ asset('image/' . $row->foto_produk) }}"
                                     alt="{{ $row->nama_produk }}"
                                     class="h-12 w-12 shrink-0 rounded-xl object-cover ring-1 ring-slate-200">
                                <div class="min-w-0">
                                    <p class="truncate text-[16px] font-bold leading-tight text-blue-900">
                                        {{ $row->nama_produk }}
                                    </p>
                                    <p class="mt-1 text-sm leading-none text-slate-400">ID: PRD-{{ $row->id_produk }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center">
                                <span class="text-[15px] text-slate-700">{{ $row->nama_kategori }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center">
                                <span class="text-[15px] font-semibold text-blue-900">
                                    Rp {{ number_format($row->harga, 0, ',', '.') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] flex-col justify-center gap-2">
                                @if((int)$row->stok > 10)
                                    <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">{{ $row->stok }} Pcs</span>
                                @elseif((int)$row->stok === 0)
                                    <span class="inline-flex w-fit rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">0 Pcs</span>
                                @else
                                    <span class="inline-flex w-fit rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">{{ $row->stok }} Pcs</span>
                                @endif
                                <p class="pr-2 text-[11px] leading-relaxed text-slate-400">{{ $row->detail_varian ?? '-' }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-4 align-middle">
                            <div class="flex min-h-[64px] items-center gap-2">
                                <a href="{{ route('admin.produk.edit', $row->id_produk) }}"
                                   class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700 transition hover:-translate-y-0.5 hover:bg-blue-100 hover:shadow-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.produk.hapus', $row->id_produk) }}"
      method="POST"
      onsubmit="return confirm('Yakin ingin menghapus produk {{ $row->nama_produk }}?')">

    @csrf
    @method('DELETE')

    <button type="submit"
        class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50 text-red-600 transition hover:-translate-y-0.5 hover:bg-red-100 hover:shadow-sm">
        <i class="fa-solid fa-trash"></i>
    </button>
</form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">Produk tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script>
    ['notif-hapus', 'notif-error-hapus'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            setTimeout(() => {
                el.style.transition = 'all 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-8px)';
                setTimeout(() => el.remove(), 500);
            }, 3000);
        }
    });
</script>
@endpush