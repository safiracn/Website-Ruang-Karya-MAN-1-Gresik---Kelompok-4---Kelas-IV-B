@extends('layouts.app')

@section('title', 'Katalog Produk - Ruang Karya')

@section('content')

<main class="min-h-screen bg-slate-50">
    <section class="pt-4 pb-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Filter Kategori --}}
            <div class="mt-0 mb-8 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
                        <span class="text-sm font-semibold text-slate-500">Kategori</span>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterCategory('all')"
                                    class="category-btn rounded-xl bg-blue-900 px-4 py-2 text-sm font-medium text-white shadow-md"
                                    data-category="all">
                                Semua
                            </button>
                            @foreach($kategoris as $kat)
                                <button onclick="filterCategory('{{ strtolower($kat->nama_kategori) }}')"
                                        class="category-btn rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium hover:bg-slate-50 transition"
                                        data-category="{{ strtolower($kat->nama_kategori) }}">
                                    {{ $kat->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grid Produk --}}
            <!-- Menampilkan seluruh produk yang dikirim dari controller -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($produk as $row) 
                    <a href="{{ route('produk.detail', $row->id_produk) }}"
                       class="product-item block group"
                       data-category="{{ strtolower($row->nama_kategori) }}">
                        <article class="flex h-full flex-col overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-200 transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                            <div class="relative bg-slate-100 p-6 pt-8">
                                <img src="{{ asset('image/' . $row->foto_produk) }}"
                                     alt="{{ $row->nama_produk }}"
                                     class="mx-auto h-44 w-full object-contain" />
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <h2 class="text-lg font-bold text-blue-900 line-clamp-1">{{ $row->nama_produk }}</h2>
                                <p class="mt-1 min-h-[80px] text-sm text-slate-500 line-clamp-3">{{ $row->deskripsi }}</p>

                                <div class="mt-3">
                                    <span class="text-xl font-extrabold text-blue-900">
                                        Rp{{ number_format($row->harga_mulai, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="mt-auto pt-5 flex gap-3">
                                    <div class="flex-1 rounded-xl bg-blue-900 px-4 py-3 font-semibold text-white transition hover:bg-yellow-500 hover:text-blue-900 text-center block">
                                        Lihat Detail
                                    </div>
                                </div>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>

        </div>
    </section>
</main>

@endsection

@push('scripts')
<script>
function filterCategory(category) {
    const products = document.querySelectorAll('.product-item');
    const buttons  = document.querySelectorAll('.category-btn');

    buttons.forEach(btn => {
        if (btn.dataset.category === category) {
            btn.classList.add('bg-blue-900', 'text-white', 'shadow-md');
            btn.classList.remove('bg-white', 'border', 'border-slate-300');
        } else {
            btn.classList.remove('bg-blue-900', 'text-white', 'shadow-md');
            btn.classList.add('bg-white', 'border', 'border-slate-300');
        }
    });

    products.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>
@endpush