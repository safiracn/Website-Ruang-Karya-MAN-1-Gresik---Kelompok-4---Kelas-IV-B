@extends('layouts.app')

@section('title', $produk->nama_produk . ' - Ruang Karya')

@section('content')

<main class="min-h-screen bg-slate-50 py-10">
    <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">

        {{-- TOP SECTION --}}
        <section class="grid grid-cols-1 gap-8 lg:grid-cols-2">

            {{-- Gambar Produk --}}
            <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-center rounded-[22px] bg-slate-100 p-4">
                    <img src="{{ asset('image/' . ($produk->foto_produk ?? 'no-image.png')) }}"
                         alt="{{ $produk->nama_produk }}"
                         class="h-[320px] w-full rounded-2xl object-cover sm:h-[400px] lg:h-[460px]">
                </div>
            </div>

            {{-- Detail Produk --}}
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm sm:p-7 lg:p-8">
                <div class="mb-4">
                    <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-blue-900">
                        {{ $produk->nama_kategori }}
                    </span>
                </div>

                <h1 class="text-3xl font-extrabold leading-tight text-slate-900 lg:text-4xl">
                    {{ $produk->nama_produk }}
                </h1>
                <p class="mt-3 text-sm leading-7 text-slate-500">
                    Produk pilihan dengan kualitas yang baik, tampilan rapi, dan cocok digunakan sesuai kebutuhan.
                </p>

                {{-- Harga --}}
                <div class="mt-5 rounded-2xl bg-[#1e3a8a] p-4 text-white shadow-sm">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-200/80">Harga</p>
                    <h2 id="harga-produk" class="mt-1 text-3xl font-black tracking-tight lg:text-4xl">
                        Rp {{ number_format($varianPertama->harga, 0, ',', '.') }}
                    </h2>
                </div>

                {{-- Pilihan Varian --}}
                @if($varians->count() > 0)
    <div class="mt-5">
        <p class="mb-3 text-sm font-bold text-slate-700">
            {{ $isBusana ? 'Pilih Ukuran' : 'Pilih Varian' }}
        </p>

        <div class="flex flex-wrap gap-3">
            @foreach($varians as $index => $v)
                <button type="button"
                        class="size-btn rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $index === 0 ? 'border-blue-900 bg-blue-900 text-white' : 'border-slate-300 bg-white text-slate-700 hover:border-blue-900 hover:text-blue-900' }}"
                        data-id-varian="{{ $v->id_varian }}"
                        data-harga="{{ $v->harga }}"
                        data-stok="{{ $v->stok }}"
                        data-nama="{{ $v->nama_varian }}"
                        onclick="pilihVarian(this)">
                    {{ $v->nama_varian }}
                </button>
            @endforeach
        </div>
    </div>
@endif

                {{-- Info Varian & Stok --}}
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-slate-100 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ $isBusana ? 'Ukuran' : 'Tipe' }}
                        </p>
                        <p id="label-varian" class="mt-1 text-base font-bold text-blue-900">
                            {{ $varianPertama->nama_varian }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-green-50 px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Stok</p>
                        <p class="mt-1 text-base font-bold text-green-700">
                            <span id="label-stok">{{ (int)$varianPertama->stok }}</span> tersedia
                        </p>
                    </div>
                </div>

                {{-- Form Tambah Keranjang / Beli Sekarang --}}
                <form action="{{ route('keranjang.tambah') }}" method="POST" class="mt-5 space-y-6" id="form-produk">
                    @csrf
                    <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                    <input type="hidden" name="id_varian" id="id_varian" value="{{ $varianPertama->id_varian }}">

                    <div>
                        <label class="mb-3 block text-sm font-bold text-slate-700">Kuantitas</label>
                        <div class="flex max-w-[160px] items-center overflow-hidden rounded-2xl border-2 border-slate-200 bg-white shadow-sm">
                            <button type="button" onclick="changeQty(-1)"
                                    class="flex h-12 w-12 items-center justify-center text-xl font-bold text-slate-500 transition hover:bg-slate-50 hover:text-blue-900">
                                -
                            </button>
                            <input type="number" name="jumlah" id="jumlah" value="1" min="1"
                                   max="{{ (int)$varianPertama->stok }}"
                                   class="h-12 w-full border-x-2 border-slate-100 bg-transparent text-center text-lg font-black text-slate-900 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <button type="button" onclick="changeQty(1)"
                                    class="flex h-12 w-12 items-center justify-center text-xl font-bold text-slate-500 transition hover:bg-slate-50 hover:text-blue-900">
                                +
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        {{-- Tambah ke Keranjang --}}
                        @auth
                            <button type="submit"
                                    class="rounded-2xl border-2 border-blue-900 px-6 py-4 text-sm font-bold text-blue-900 transition hover:bg-blue-50">
                                🛒 Tambah ke Keranjang
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="rounded-2xl border-2 border-blue-900 px-6 py-4 text-sm font-bold text-blue-900 transition hover:bg-blue-50 text-center">
                                🛒 Login untuk Beli
                            </a>
                        @endauth

                        {{-- Beli Sekarang --}}
                        @auth
                            <button type="submit" form="form-beli"
                                    class="rounded-2xl bg-blue-900 px-6 py-4 text-sm font-bold text-white transition hover:bg-blue-800">
                                Beli Sekarang
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="rounded-2xl bg-blue-900 px-6 py-4 text-sm font-bold text-white transition hover:bg-blue-800 text-center">
                                Beli Sekarang
                            </a>
                        @endauth
                    </div>
                </form>

                {{-- Form tersembunyi untuk Beli Sekarang → ke checkout --}}
                @auth
                <form id="form-beli" action="{{ route('checkout') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="aksi" value="beli">
                    <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                    <input type="hidden" name="id_varian" id="id_varian_beli" value="{{ $varianPertama->id_varian }}">
                    <input type="hidden" name="jumlah" id="jumlah_beli" value="1">
                </form>
                @endauth

            </div>
        </section>

        {{-- BOTTOM SECTION --}}
        <section class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-[1.4fr_0.8fr]">

            {{-- Deskripsi --}}
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                <h3 class="mb-4 text-2xl font-extrabold text-slate-900">Deskripsi Produk</h3>
                <div class="text-sm leading-7 text-slate-600 lg:text-[15px]">
                    {!! nl2br(e($produk->deskripsi)) !!}
                </div>
                <div class="mt-6 rounded-2xl border-l-4 border-blue-900 bg-blue-50 px-5 py-5 text-sm italic leading-7 text-slate-600">
                    Produk diproses dengan kualitas terbaik sesuai kategori dan bahan yang digunakan.
                </div>
            </div>

            {{-- Spesifikasi --}}
            <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
                <h3 class="mb-5 text-2xl font-extrabold text-slate-900">Spesifikasi Detail</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-3">
                        <span class="text-slate-400">Kategori</span>
                        <span class="text-right font-bold text-slate-700">{{ $produk->nama_kategori }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-3">
                        <span class="text-slate-400">Bahan</span>
                        <span class="text-right font-bold text-slate-700">{{ $produk->bahan }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-3">
                        <span class="text-slate-400">Finishing</span>
                        <span class="text-right font-bold text-slate-700">{{ $produk->finishing ?: '-' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-3">
                        <span class="text-slate-400">Dimensi</span>
                        <span class="text-right font-bold text-slate-700">{{ $produk->dimensi ?: '-' }}</span>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <span class="text-slate-400">Garansi</span>
                        <span class="text-right font-bold text-slate-700">{{ $produk->garansi }}</span>
                    </div>
                </div>
            </div>

        </section>
    </div>
</main>

@endsection

@push('scripts')
<script>
    function pilihVarian(btn) {
        document.querySelectorAll('.size-btn').forEach(item => {
            item.classList.remove('bg-blue-900', 'text-white', 'border-blue-900');
            item.classList.add('text-slate-700', 'border-slate-300', 'bg-white');
        });
        btn.classList.add('bg-blue-900', 'text-white', 'border-blue-900');
        btn.classList.remove('text-slate-700', 'border-slate-300', 'bg-white');

        const harga    = parseInt(btn.dataset.harga);
        const stok     = parseInt(btn.dataset.stok);
        const idVarian = btn.dataset.idVarian;
        const nama     = btn.dataset.nama;

        document.getElementById('id_varian').value       = idVarian;
        document.getElementById('id_varian_beli').value  = idVarian;
        document.getElementById('label-stok').innerText  = stok;
        document.getElementById('jumlah').max            = stok;

        const labelVarian = document.getElementById('label-varian');
        if (labelVarian) labelVarian.innerText = nama;

        document.getElementById('harga-produk').innerText =
            'Rp ' + harga.toLocaleString('id-ID');
    }

    function changeQty(amount) {
        const input   = document.getElementById('jumlah');
        const inputB  = document.getElementById('jumlah_beli');
        let current   = parseInt(input.value) || 1;
        let max       = parseInt(input.max) || 1;
        let next      = current + amount;

        if (next >= 1 && next <= max) {
            input.value  = next;
            if (inputB) inputB.value = next;
        }
    }

    // Sinkronkan jumlah antara form keranjang dan form beli
    document.getElementById('jumlah').addEventListener('input', function () {
        const inputB = document.getElementById('jumlah_beli');
        if (inputB) inputB.value = this.value;
    });
</script>
@endpush