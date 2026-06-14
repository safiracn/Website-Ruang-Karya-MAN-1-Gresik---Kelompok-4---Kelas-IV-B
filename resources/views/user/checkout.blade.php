<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('Checkout', 'Ruang Karya MAN 1 Gresik')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- CSS Tailwind (build dari input.css) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @stack('styles')
</head>

<body class="font-sans-body bg-slate-50 text-slate-800">

<div class="bg-gray-50 w-full max-w-6xl mx-auto rounded-xl shadow-2xl overflow-hidden flex flex-col my-10">

    {{-- Header --}}
    <header class="bg-white px-6 py-5 flex items-center gap-4">
        <img src="{{ asset('image/LOGO.jpeg') }}" alt="Logo" class="h-14 w-14 object-contain">
        <div>
            <h1 class="text-xl font-serif-heading font-bold text-blue-900 leading-tight">Ruang Karya</h1>
            <h2 class="text-sm italic font-semibold text-blue-900">MAN 1 Gresik</h2>
            <p class="text-[11px] md:text-sm italic text-blue-900 leading-snug">
                Islami, Cerdas, Unggul, Kompetitif, &amp; Peduli Lingkungan
            </p>
        </div>
    </header>

    {{-- Menampilkan Error Alert dari Server/Controller jika ada --}}
    @if ($errors->any())
        <div class="mx-6 mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 roundedshadow-sm">
            <p class="font-bold">Mohon periksa kembali inputan Anda:</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- // Form digunakan untuk mengisi data penerima dan mengirim pesanan -->
    <form id="orderForm" action="{{ route('checkout.proses') }}" method="POST"
          class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
        @csrf

        <input type="hidden"
           name="selected_items"
           value="{{ old('selected_items', request('selected_items')) }}">
        <input type="hidden" name="id_produk_langsung" value="{{ old('id_produk_langsung', request('id_produk')) }}">
    <input type="hidden" name="id_varian_langsung" value="{{ old('id_varian_langsung', request('id_varian')) }}">
    <input type="hidden" name="jumlah_langsung" value="{{ old('jumlah_langsung', request('jumlah')) }}">

        {{-- ========== INFORMASI PENGIRIMAN ========== --}}
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h2 class="text-blue-900 uppercase font-bold mb-6 tracking-wider">Informasi Pengiriman</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Penerima</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Shava Nisa'"
                               class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none @error('nama') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">
                               <p class="text-[11px] text-gray-400 mt-0.5">Hanya boleh huruf, spasi, dan tanda petik (')</p>
        @error('nama')
            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Telepon</label>
                        <input type="text" id="noTelp" name="noTelp" value="{{ old('noTelp') }}" required placeholder="Contoh: 0832XXXXXXXX"
                               class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none @error('noTelp') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">
                               <p class="text-[11px] text-gray-400 mt-0.5">Hanya boleh diisi angka</p>
        @error('noTelp')
            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Provinsi</label>
                            <input type="text" id="provinsi" name="provinsi" value="{{ old('provinsi') }}" required
                                   class="w-full p-2.5 border border-slate-300 rounded-lg outline-none @error('provinsi') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">
                                   <p class="text-[11px] text-gray-400 mt-0.5">Hanya boleh huruf</p>
        @error('provinsi')
            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
        @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kota/Kabupaten</label>
                            <input type="text" id="kota" name="kota" value="{{ old('kota') }}" required
                                   class="w-full p-2.5 border border-slate-300 rounded-lg outline-none @error('kota') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">
                                   <p class="text-[11px] text-gray-400 mt-0.5">Hanya boleh huruf</p>
        @error('kota')
            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
        @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kode Pos</label>
                        <input type="text" id="kodePos" name="kodePos" value="{{ old('kodePos') }}" required
                               class="w-full p-2.5 border border-slate-300 rounded-lg outline-none @error('kodePos') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">
                               <p class="text-[11px] text-gray-400 mt-0.5">Hanya boleh diisi angka</p>
        @error('kodePos')
            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fa-solid fa-circle-exclamation mr-1"></i>{{ $message }}</p>
        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Detail Alamat</label>
                        <textarea id="detail" name="detail" rows="3" required
                                  class="w-full p-2.5 border border-slate-300 rounded-lg outline-none @error('detail') border-red-500 bg-red-50 focus:ring-red-400 @else border-slate-300 @enderror">{{ old('detail') }}</textarea>
                                  @error('detail')
            <p class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</p>
        @enderror
                    </div>
                    <div>
                        <h3 class="font-bold text-blue-900">Metode Pengiriman</h3>
                        <div class="flex gap-4 mt-2">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="pengiriman" value="Ambil" {{ old('pengiriman', 'Ambil') == 'Ambil' ? 'checked' : '' }} class="hidden peer">
                                <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:bg-yellow-100 peer-checked:border-yellow-400 font-semibold transition-all">
                                    Ambil
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="pengiriman" value="Antar" {{ old('pengiriman') == 'Antar' ? 'checked' : '' }} class="hidden peer">
                                <div class="p-3 border-2 border-slate-200 rounded-xl text-center peer-checked:bg-yellow-100 peer-checked:border-yellow-400 font-semibold transition-all">
                                    Antar
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== RINGKASAN PRODUK ========== --}}
        <div class="space-y-6">
            <div class="bg-gray-100 border border-slate-200 p-6 rounded-xl shadow-lg">
                <h2 class="text-blue-900 uppercase font-bold mb-6 tracking-wider">Ringkasan Produk</h2>

                <div id="product-list" class="max-h-[400px] overflow-y-auto space-y-4 pr-2">
                    @forelse($items as $index => $item)
                        <div class="flex items-center gap-4 bg-white p-3 rounded-lg border border-slate-200 shadow-sm">
                            <input type="hidden" name="items[{{ $index }}][id_varian]" value="{{ $item['id_varian'] }}">
                            <input type="hidden" name="items[{{ $index }}][jumlah]"    value="{{ $item['jumlah'] }}">
                            <input type="hidden" name="items[{{ $index }}][harga]"     value="{{ $item['harga'] }}">

                            <img src="{{ asset('image/' . $item['gambar']) }}"
                                 class="w-20 h-20 object-cover rounded-lg">
                            <div class="flex-1">
                                <p class="text-black font-bold">{{ $item['nama'] }}</p>
                                <p class="text-xs text-blue-900">Jumlah: {{ $item['jumlah'] }} pcs</p>
                                <p class="text-yellow-600 font-bold">
                                    Rp {{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="italic text-blue-900 opacity-70">Belum ada barang yang dipilih!</p>
                    @endforelse
                </div>

                {{-- Total & Tombol Pesan --}}
                <div class="mt-8 pt-6 border-t border-slate-300">
                    <div class="flex justify-between items-center text-xl font-black">
                        <span class="text-blue-900">TOTAL</span>
                        <span class="text-yellow-600">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</span>
                    </div>
                    <input type="hidden" name="total_final" value="{{ $totalAkhir }}">

                    <button type="submit"
                            class="w-full bg-yellow-500 text-blue-900 font-black py-4 rounded-xl mt-6 hover:scale-[1.02] transition shadow-xl hover:bg-blue-900 hover:text-yellow-500 uppercase">
                        Pesan Sekarang
                    </button>
                </div>
            </div>
        </div>

    </form>

    <footer class="p-4 text-center border-t border-slate-200 bg-white">
        <p class="text-gray-400 text-xs tracking-widest uppercase">&copy; 2026 Ruang Karya MAN 1 Gresik</p>
    </footer>
</div>

@stack('scripts')

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Regex pengetatan kriteria input kamu
        const alphaRegex = /^[a-zA-Z\s']+$/; // Huruf, spasi, dan tanda petik satu (')
        const numRegex   = /^[0-9]+$/;       // Hanya angka

        // 1. FILTER REALTIME SAAT KETIK (Mencegah karakter ilegal masuk ke inputan)
        function filterInput(elementId, regexType) {
            const el = document.getElementById(elementId);
            if (!el) return;

            el.addEventListener('keypress', function (e) {
                // Beri akses tombol khusus (BackSpace, Enter, Delete, Tab)
                if (e.key === 'Backspace' || e.key === 'Enter' || e.key === 'Delete' || e.key === 'Tab') {
                    return;
                }
                
                if (regexType === 'alpha') {
                    if (!alphaRegex.test(e.key)) { e.preventDefault(); }
                } else if (regexType === 'num') {
                    if (!numRegex.test(e.key)) { e.preventDefault(); }
                }
            });

            // Antisipasi jika user melakukan Copy Paste teks ilegal
            el.addEventListener('input', function () {
                let val = this.value;
                if (regexType === 'alpha') {
                    // Hapus karakter selain huruf, spasi, dan petik (')
                    this.value = val.replace(/[^a-zA-Z\s']/g, '');
                } else if (regexType === 'num') {
                    // Hapus karakter selain angka
                    this.value = val.replace(/[^0-9]/g, '');
                }
            });
        }

        // Jalankan pelindung input realtime
        filterInput('nama', 'alpha');
        filterInput('provinsi', 'alpha');
        filterInput('kota', 'alpha');
        filterInput('noTelp', 'num');
        filterInput('kodePos', 'num');

        // 2. VALIDASI FINAL SAAT TOMBOL SUBMIT DIKLIK
        document.getElementById('orderForm').addEventListener('submit', function (e) {
            const fields = [
                { id: 'nama',     label: 'Nama Lengkap',   type: 'alpha' },
                { id: 'noTelp',   label: 'No. Telepon',    type: 'num' },
                { id: 'provinsi', label: 'Provinsi',       type: 'alpha' },
                { id: 'kota',     label: 'Kota/Kabupaten', type: 'alpha' },
                { id: 'kodePos',  label: 'Kode Pos',       type: 'num' },
                { id: 'detail',   label: 'Detail Alamat',  type: 'required' },
            ];

            for (const field of fields) {
                const el  = document.getElementById(field.id);
                const val = el.value.trim();

                if (!val) {
                    alert(`Peringatan: ${field.label} tidak boleh kosong!`);
                    e.preventDefault(); el.focus(); return;
                }
                if (field.type === 'alpha' && !alphaRegex.test(val)) {
                    alert(`Peringatan: ${field.label} hanya boleh diisi huruf, spasi, dan simbol ' !`);
                    e.preventDefault(); el.focus(); return;
                }
                if (field.type === 'num' && !numRegex.test(val)) {
                    alert(`Peringatan: ${field.label} hanya boleh berisi angka!`);
                    e.preventDefault(); el.focus(); return;
                }
            }
        });
    });
</script>
@endpush
</body>
</html>