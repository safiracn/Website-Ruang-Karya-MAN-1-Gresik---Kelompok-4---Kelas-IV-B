@extends('layouts.admin')

@section('title', 'Ubah Informasi Produk')
@section('activeMenu', 'dashboard')
@section('pageDesc', 'Edit data produk yang sudah ada.')

@push('styles')
<style>
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinner { -moz-appearance: textfield; }
</style>
@endpush

@section('content')

<form action="{{ route('admin.produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data"
      class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    @csrf
    <input type="hidden" name="id" value="{{ $produk->id_produk }}">

    {{-- ============================================================ --}}
    {{-- KOLOM KIRI (2/3) — Informasi Dasar, Spesifikasi, Varian     --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-2 space-y-8">

        {{-- Informasi Dasar --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Informasi Dasar
            </h3>
            <div class="space-y-5">
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Nama Produk <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_produk" value="{{ $produk->nama_produk }}" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Kategori <span class="text-red-600">*</span></label>
                    <select name="id_kategori"
                            class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        <option value="1" {{ $produk->id_kategori == 1 ? 'selected' : '' }}>Mebel</option>
                        <option value="2" {{ $produk->id_kategori == 2 ? 'selected' : '' }}>Busana</option>
                        <option value="3" {{ $produk->id_kategori == 3 ? 'selected' : '' }}>Kerajinan Tangan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Deskripsi Produk <span class="text-red-600">*</span></label>
                    <textarea name="deskripsi" rows="5" required
                              class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">{{ $produk->deskripsi }}</textarea>
                </div>
            </div>
        </div>

        {{-- Spesifikasi Detail --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Spesifikasi Detail
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Material <span class="text-red-600">*</span></label>
                    <input type="text" name="bahan" value="{{ $produk->bahan }}" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Finishing</label>
                    <input type="text" name="finishing" value="{{ $produk->finishing }}"
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Dimensi (PxLxT)</label>
                    <input type="text" name="dimensi" value="{{ $produk->dimensi }}"
                           placeholder="Contoh: 100 x 50 x 40 cm"
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Garansi <span class="text-red-600">*</span></label>
                    <input type="text" name="garansi" value="{{ $produk->garansi }}" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
        </div>

        {{-- Data Penjualan / Varian --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Data Penjualan <span class="text-red-600">*</span>
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-slate-600 text-sm">
                            <th class="px-4 py-2 font-medium">Varian Produk</th>
                            <th class="px-4 py-2 font-medium">Harga Jual (Rp)</th>
                            <th class="px-4 py-2 font-medium">Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody id="variant-table-body">
                        @forelse($varians as $v)
                            <tr class="bg-slate-50 rounded-xl">
                                <td class="px-4 py-4 first:rounded-l-xl">
                                    <div class="flex items-center gap-2">
                                        <input type="text" name="nama_varian[]" value="{{ $v->nama_varian }}"
                                               class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px] focus:ring-2 focus:ring-yellow-400">
                                        <input type="hidden" name="id_varian[]" value="{{ $v->id_varian }}">
                                        <button type="button" onclick="this.closest('tr').remove()"
                                                class="text-red-500 hover:text-red-700">
                                            <i class="fa-solid fa-trash text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="text" name="harga_varian[]" value="{{ $v->harga }}"
                                           oninput="validasiAngka(this)"
                                           class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 focus:border-blue-900 outline-none font-semibold">
                                </td>
                                <td class="px-4 py-4 last:rounded-r-xl text-center">
                                    <input type="number" name="stok_varian[]" value="{{ $v->stok }}"
                                            oninput="validasiAngka(this)"
                                           class="w-20 p-2 bg-transparent border-b border-slate-300 focus:border-blue-900 outline-none text-center font-semibold">
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-slate-50 rounded-xl">
                                <td class="px-4 py-4 first:rounded-l-xl">
                                    <span class="text-slate-400 italic text-sm">Tanpa Varian (Standard)</span>
                                    <input type="hidden" name="nama_varian[]" value="Standard">
                                </td>
                                <td class="px-4 py-4">
                                    <input type="text" name="harga_varian[]" value="0"
                                           oninput="validasiAngka(this)"
                                           class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 outline-none font-semibold">
                                </td>
                                <td class="px-4 py-4 last:rounded-r-xl text-center">
                                    <input type="number" name="stok_varian[]" value="0"
                                            oninput="validasiAngka(this)"
                                           class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center">
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Tambah Varian Baru --}}
            <div class="mt-6 flex gap-2">
                <input type="text" id="new-variant-name" placeholder="Tambah Varian Baru..."
                       class="flex-1 p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none text-sm">
                <button type="button" onclick="addNewVariantRow()"
                        class="bg-blue-900 text-white px-6 rounded-xl font-bold text-sm hover:bg-blue-800 transition">
                    TAMBAH
                </button>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- KOLOM KANAN (1/3) — Foto + Tombol                           --}}
    {{-- ============================================================ --}}
    <div class="space-y-8">

        {{-- Foto Produk --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center relative">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2 text-left">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Foto Produk Utama
            </h3>

            <label for="foto_utama"
                   class="absolute top-8 right-8 text-sm font-semibold text-blue-700 cursor-pointer hover:text-yellow-600 flex items-center gap-1.5 z-10">
                <i class="fa-solid fa-pen text-xs"></i> Ubah Foto
            </label>

            <div class="aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-inner mb-4 bg-slate-50 flex items-center justify-center">
                <img id="preview-foto"
                     src="{{ asset('image/' . $produk->foto_produk) }}"
                     alt="Foto Produk"
                     class="w-full h-full object-cover">
            </div>

            <input type="file" id="foto_utama" name="foto_utama" accept="image/*"
                   class="hidden" onchange="previewImage(this)">
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex flex-col sm:flex-row xl:flex-col gap-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex-1 bg-white text-blue-900 font-bold py-4 rounded-xl text-center border border-slate-200 hover:scale-[1.02] transition duration-300 shadow-sm">
                Batalkan
            </a>
            <button type="submit"
                    class="flex-1 bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                Simpan Perubahan
            </button>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview = document.getElementById('preview-foto');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validasiAngka(input) {
        let valid = input.value.replace(/[^0-9]/g, '');
        if (input.value !== valid) {
            alert('Kolom Harga Jual hanya boleh diisi dengan angka!');
            input.value = valid;
        }
    }

    function addNewVariantRow() {
        const input = document.getElementById('new-variant-name');
        const name  = input.value.trim();
        if (!name) return;

        const tbody  = document.getElementById('variant-table-body');
        const newRow = document.createElement('tr');
        newRow.className = 'bg-slate-50 rounded-xl';
        newRow.innerHTML = `
            <td class="px-4 py-4 first:rounded-l-xl">
                <div class="flex items-center gap-2">
                    <input type="text" name="nama_varian[]" value="${name}"
                           class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px]">
                    <input type="hidden" name="id_varian[]" value="">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-red-500">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </td>
            <td class="px-4 py-4">
                <input type="text" name="harga_varian[]" value="0" oninput="validasiAngka(this)"
                       class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 outline-none font-semibold">
            </td>
            <td class="px-4 py-4 last:rounded-r-xl text-center">
                <input type="number" name="stok_varian[]" value="0" oninput="validasiAngka(this)"
                       class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center font-semibold">
            </td>
        `;
        tbody.appendChild(newRow);
        input.value = '';
    }
</script>
@endpush