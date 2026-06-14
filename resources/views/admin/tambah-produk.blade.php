<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru</title>
    
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
            <h1 class="text-3xl font-extrabold text-blue-900 tracking-tight">Tambah Produk Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Tambahkan produk karya siswa baru ke katalog.</p>
        </div>

        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data"
              class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            @csrf

            {{-- KOLOM KIRI (Partial Form) --}}
            <div class="xl:col-span-2 space-y-8">
                @include('admin.form')
            </div>

            {{-- KOLOM KANAN (Foto & Action) --}}
            <div class="space-y-8">
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

                <div class="flex flex-col gap-4">
                    <button type="submit"
                            class="w-full bg-yellow-500 text-blue-900 font-bold py-4 rounded-xl hover:scale-[1.02] transition duration-300 shadow-lg shadow-yellow-200 tracking-wider">
                        Simpan Produk
                    </button>
                    <a href="{{ route('admin.dashboard') }}"
                       class="w-full bg-white text-slate-500 font-bold py-4 rounded-xl text-center border border-slate-200 hover:bg-slate-50 transition duration-300">
                        Batalkan
                    </a>
                </div>
            </div>
        </form>
    </div>

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

        // === SEKARANG SAMA PERSIS DENGAN FILE EDIT ===
        function validasiAngka(input, namaKolom) {
    let valid = input.value.replace(/[^0-9]/g, '');
    if (input.value !== valid) {
        alert('Kolom ' + namaKolom + ' hanya boleh diisi dengan angka!');
        input.value = valid;
    }
}

        // Fungsi tambah row varian
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
                    <input type="text" name="harga_varian[]" value="0" oninput="validasiAngka(this, 'Harga Jual')"
                           class="no-spinner w-full p-2 bg-transparent border-b border-slate-300 outline-none font-semibold">
                </td>
                <td class="px-4 py-4 last:rounded-r-xl text-center">
                    <input type="number" name="stok_varian[]" value="0" oninput="validasiAngka(this, 'Stok Tersedia')"
                           class="w-20 p-2 bg-transparent border-b border-slate-300 outline-none text-center font-semibold">
                </td>
            `;
            tbody.appendChild(newRow);
            input.value = '';
        }
    </script>
</body>
</html>