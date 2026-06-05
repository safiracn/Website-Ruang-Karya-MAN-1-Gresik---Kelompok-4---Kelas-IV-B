<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Informasi Produk</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .no-spinner::-webkit-outer-spin-button,
        .no-spinner::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .no-spinner { -moz-appearance: textfield; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight">Ubah Informasi Produk</h1>
            <p class="text-sm text-slate-500 mt-1">Edit data produk yang sudah ada.</p>
        </div>

        <form action="{{ route('admin.produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data"
              class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="id" value="{{ $produk->id_produk }}">

            {{-- KOLOM KIRI (Memanggil file partial form) --}}
            <div class="xl:col-span-2 space-y-8">
                @include('admin.form')
            </div>

            {{-- KOLOM KANAN — Foto + Tombol --}}
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
    </div>

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
</body>
</html>