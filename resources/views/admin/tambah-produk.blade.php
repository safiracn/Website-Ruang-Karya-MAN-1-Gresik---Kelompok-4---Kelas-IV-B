@extends('layouts.admin')

@section('title', 'Tambah Produk Baru')
@section('activeMenu', 'dashboard')
@section('pageDesc', 'Tambahkan produk karya siswa baru ke katalog.')

@push('styles')
<style>
    .no-spinner::-webkit-outer-spin-button,
    .no-spinner::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .no-spinner { -moz-appearance: textfield; }
</style>
@endpush

@section('content')

<form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data"
      class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    @csrf

    {{-- ============================================================ --}}
    {{-- KOLOM KIRI (2/3)                                             --}}
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
                    <input type="text" name="nama_produk" placeholder="Masukkan nama produk" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Kategori <span class="text-red-600">*</span></label>
                    <select name="id_kategori" required
                            class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        <option value="" disabled selected>Pilih Kategori</option>
                        <option value="1">Mebel</option>
                        <option value="2">Busana</option>
                        <option value="3">Kerajinan Tangan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Deskripsi Produk <span class="text-red-600">*</span></label>
                    <textarea name="deskripsi" rows="5" placeholder="Ceritakan detail produk..." required
                              class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition"></textarea>
                </div>
            </div>
        </div>

        {{-- Spesifikasi --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Spesifikasi Detail
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Material <span class="text-red-600">*</span></label>
                    <input type="text" name="bahan" placeholder="Contoh: Kayu Jati" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Finishing</label>
                    <input type="text" name="finishing" placeholder="Contoh: Melamine Gloss"
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">Dimensi (PxLxT)</label>
                    <input type="text" name="dimensi" placeholder="100 x 50 x 40 cm"
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600 mb-1">Garansi <span class="text-red-600">*</span></label>
                    <input type="text" name="garansi" placeholder="Contoh: 1 Tahun" required
                           class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
        </div>

        {{-- Varian --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Data Penjualan <span class="text-red-600 ml-1">*</span>
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
                        <tr class="bg-slate-50 rounded-xl">
                            <td class="px-4 py-4 first:rounded-l-xl">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="nama_varian[]" value="Standard"
                                           class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px]">
                                    <button type="button" onclick="this.closest('tr').remove()"
                                            class="text-red-500"><i class="fa-solid fa-trash"></i></button>
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
                        </tr>
                    </tbody>
                </table>
            </div>
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
    {{-- KOLOM KANAN (1/3)                                            --}}
    {{-- ============================================================ --}}
    <div class="space-y-8">

        {{-- Foto --}}
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 text-center relative">
            <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2 text-left">
                <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Foto Produk Utama <span class="text-red-600">*</span>
            </h3>

            <label for="foto_utama"
                   class="absolute top-8 right-8 text-sm font-semibold text-blue-700 cursor-pointer hover:text-yellow-600 flex items-center gap-1.5 z-10">
                <i class="fa-solid fa-plus text-xs"></i> Pilih Foto
            </label>

            <div class="aspect-square rounded-2xl overflow-hidden border border-slate-100 shadow-inner mb-4 bg-slate-50 flex items-center justify-center border-dashed border-2">
                <img id="preview-foto" src="" alt="Preview Foto" class="w-full h-full object-cover hidden">
                <div id="placeholder-icon" class="text-slate-300">
                    <i class="fa-solid fa-image text-6xl"></i>
                    <p class="text-xs mt-2 font-medium text-slate-400">Belum ada foto dipilih</p>
                </div>
            </div>

            <input type="file" id="foto_utama" name="foto_utama" accept="image/*" required
                   class="hidden" onchange="previewImage(this)">
            <p class="text-xs text-slate-400 mt-2">Format: JPG, PNG, WEBP (Maks 2MB)</p>
        </div>

        {{-- Tombol --}}
        <div class="flex flex-col gap-4">
            <button type="submit"
                    class="w-full bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                SIMPAN PRODUK
            </button>
            <a href="{{ route('admin.dashboard') }}"
               class="w-full bg-white text-slate-500 font-bold py-4 rounded-xl text-center border border-slate-200 hover:bg-slate-50 transition duration-300">
                Batalkan
            </a>
        </div>

    </div>
</form>

@endsection

@push('scripts')
<script>
    function previewImage(input) {
        const preview     = document.getElementById('preview-foto');
        const placeholder = document.getElementById('placeholder-icon');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validasiAngka(input) {
        const valid = input.value.replace(/[^0-9]/g, '');
        if (input.value !== valid) input.value = valid;
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