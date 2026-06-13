{{-- Informasi Dasar --}}
<div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100">
    <h3 class="font-bold text-lg text-blue-900 mb-6 flex items-center gap-2">
        <span class="w-1 h-6 bg-yellow-500 rounded-full"></span> Informasi Dasar
    </h3>
    <div class="space-y-5">
        <div>
            <label class="text-sm font-medium text-slate-600 mb-1">NAMA PRODUK <span class="text-red-600">*</span></label>
            <input type="text" name="nama_produk" placeholder="Masukkan nama produk" required
                   value="{{ old('nama_produk', $produk->nama_produk ?? '') }}"
                   class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 mb-1">KATEGORI <span class="text-red-600">*</span></label>
            <select name="id_kategori" required
                    class="block w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">
                <option value="" disabled {{ !isset($produk) ? 'selected' : '' }}>Pilih Kategori</option>
                <option value="1" {{ old('id_kategori', $produk->id_kategori ?? '') == 1 ? 'selected' : '' }}>Mebel</option>
                <option value="2" {{ old('id_kategori', $produk->id_kategori ?? '') == 2 ? 'selected' : '' }}>Busana</option>
                <option value="3" {{ old('id_kategori', $produk->id_kategori ?? '') == 3 ? 'selected' : '' }}>Kerajinan Tangan</option>
            </select>
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 mb-1">DESKRIPSI PRODUK <span class="text-red-600">*</span></label>
            <textarea name="deskripsi" rows="5" placeholder="Jelaskan detail keunggulan dan spesifikasi produk..." required
                      class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 focus:ring-2 focus:ring-yellow-400 outline-none transition">{{ old('deskripsi', $produk->deskripsi ?? '') }}</textarea>
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
            <label class="text-sm font-medium text-slate-600 mb-1">MATERIAL <span class="text-red-600">*</span></label>
            <input type="text" name="bahan" placeholder="Contoh: Kayu Jati" required
                   value="{{ old('bahan', $produk->bahan ?? '') }}"
                   class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">FINISHING</label>
            <input type="text" name="finishing" placeholder="Contoh: Melamine Gloss"
                   value="{{ old('finishing', $produk->finishing ?? '') }}"
                   class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-600 mb-1">DIMENSI (PxLxT)</label>
            <input type="text" name="dimensi" placeholder="Contoh: 100 x 50 x 40 cm"
                   value="{{ old('dimensi', $produk->dimensi ?? '') }}" 
                   class="w-full p-3 border border-slate-200 rounded-xl bg-slate-50 outline-none">
        </div>
        <div>
            <label class="text-sm font-medium text-slate-600 mb-1">GARANSI <span class="text-red-600">*</span></label>
            <input type="text" name="garansi" placeholder="Contoh: 1 Tahun" required
                   value="{{ old('garansi', $produk->garansi ?? '') }}"
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
                    <th class="px-4 py-2 font-medium">VARIAN PRODUK</th>
                    <th class="px-4 py-2 font-medium">HARGA JUAL (Rp)</th>
                    <th class="px-4 py-2 font-medium">STOK TERSEDIA</th>
                </tr>
            </thead>
            <tbody id="variant-table-body">
                {{-- Cek jika ada variabel $varians bawaan dari halaman edit --}}
                @if(isset($varians) && count($varians) > 0)
                    @foreach($varians as $v)
                        <tr class="bg-slate-50 rounded-xl">
                            <td class="px-4 py-4 first:rounded-l-xl">
                                <div class="flex items-center gap-2">
                                    <input type="text" name="nama_varian[]" value="{{ $v->nama_varian }}"
                                           class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px] focus:ring-2 focus:ring-yellow-400">
                                    <input type="hidden" name="id_varian[]" value="{{ $v->id_varian }}">

                                {{-- Jika ini baris pertama ($loop->first), jangan tampilkan tombol hapus --}}
                                @if(!$loop->first)
                                    <button type="button" onclick="this.closest('tr').remove()"
                                            class="text-red-500 hover:text-red-700">
                                        <i class="fa-solid fa-trash text-lg"></i>
                                    </button>
                                @else
                                    {{-- Opsional: Kasih penanda/ikon gembok kecil kalau ini varian utama --}}
                                    <span class="text-slate-400 text-xs px-1" title="Varian Utama (Tidak dapat dihapus)">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                @endif
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
                    @endforeach
                @else
                    {{-- Default row kosongan untuk halaman tambah produk --}}
                    <tr class="bg-slate-50 rounded-xl">
                        <td class="px-4 py-4 first:rounded-l-xl">
                            <div class="flex items-center gap-2">
                                <input type="text" name="nama_varian[]" value="Standard"
                                       class="bg-blue-900 text-white text-xs font-bold px-3 py-1.5 rounded-full outline-none w-full max-w-[120px]">
                                <input type="hidden" name="id_varian[]" value="">

                                {{-- Di halaman tambah produk, baris pertama ini juga dikunci gembok --}}
                                <span class="text-slate-400 text-xs px-1" title="Varian Utama (Tidak dapat dihapus)">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
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
                @endif
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