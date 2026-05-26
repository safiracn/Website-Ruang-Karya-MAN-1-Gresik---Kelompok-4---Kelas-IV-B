@extends('layouts.app')

@section('title', 'Keranjang Belanja - Ruang Karya')

@section('content')

<main class="px-4 py-8 md:px-6">
    <div class="mx-auto max-w-[1450px]">

        <section class="mb-6">
            <h1 class="font-serif-heading text-[38px] font-bold leading-tight text-blue-900 md:text-[52px]">
                Keranjang Belanja
            </h1>
            <p class="mt-2 max-w-[760px] text-[15px] leading-relaxed text-slate-500 md:text-[16px]">
                Koleksi karya terpilih dari siswa MAN 1 Gresik yang siap menghiasi ruang belajar dan kerja Anda.
            </p>
        </section>

        <section class="overflow-hidden rounded-[24px] bg-white shadow-sm ring-1 ring-slate-200">

            {{-- Header Tabel --}}
            <div class="grid grid-cols-[48px_2.4fr_1fr_150px_1fr_40px] items-center border-b border-slate-200 px-4 py-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500 md:px-6">
                <div></div>
                <div>Produk</div>
                <div>Harga Satuan</div>
                <div>Jumlah</div>
                <div>Total</div>
                <div></div>
            </div>

            {{-- List Item --}}
            <div class="h-[560px] overflow-y-auto">
                @forelse($items as $row)
                    <div class="cart-item grid grid-cols-[48px_2.4fr_1fr_150px_1fr_40px] items-center border-b border-slate-200 px-4 py-3 transition hover:bg-slate-50 md:px-6"
                         data-id="{{ (int)$row->id_keranjang_detail }}"
                         data-subtotal="{{ (float)$row->subtotal }}">

                        {{-- Checkbox --}}
                        <div class="flex justify-center">
                            <input type="checkbox"
                                   class="item-checkbox h-6 w-6 cursor-pointer rounded-md border-2 border-slate-300 bg-white text-blue-900 accent-blue-900 transition"
                                   value="{{ (int)$row->id_keranjang_detail }}" checked>
                        </div>

                        {{-- Produk --}}
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('image/' . $row->foto_produk) }}"
                                 alt="{{ $row->nama_produk }}"
                                 class="h-[68px] w-[68px] rounded-xl object-cover ring-1 ring-slate-200 md:h-[78px] md:w-[78px]">
                            <div class="min-w-0">
                                <p class="text-[18px] font-bold leading-tight text-blue-900 md:text-[21px]">
                                    {{ $row->nama_produk }}
                                </p>
                                <p class="mt-1 text-[12px] font-medium text-slate-500 md:text-[13px]">
                                    Varian: {{ $row->nama_varian }}
                                </p>
                            </div>
                        </div>

                        {{-- Harga Satuan --}}
                        <div>
                            <p class="text-[19px] font-bold text-blue-900 md:text-[21px]">
                                Rp {{ number_format($row->harga, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Qty Controls --}}
                        <div>
                            <div class="inline-flex h-[42px] items-center overflow-hidden rounded-xl border border-slate-300 bg-white shadow-sm">
                                {{-- Kurang --}}
                                <form method="POST" action="{{ route('keranjang.update') }}" class="contents">
                                    @csrf
                                    <input type="hidden" name="id_detail" value="{{ (int)$row->id_keranjang_detail }}">
                                    <input type="hidden" name="mode_qty" value="minus">
                                    <button type="submit"
                                            class="flex h-full w-11 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                        <i class="fa-solid fa-minus text-sm"></i>
                                    </button>
                                </form>

                                <span class="flex h-full min-w-[48px] items-center justify-center text-[17px] font-semibold text-slate-700">
                                    {{ (int)$row->jumlah }}
                                </span>

                                {{-- Tambah --}}
                                <form method="POST" action="{{ route('keranjang.update') }}" class="contents">
                                    @csrf
                                    <input type="hidden" name="id_detail" value="{{ (int)$row->id_keranjang_detail }}">
                                    <input type="hidden" name="mode_qty" value="plus">
                                    <button type="submit"
                                            class="flex h-full w-11 items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-blue-900">
                                        <i class="fa-solid fa-plus text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Subtotal --}}
                        <div>
                            <p class="item-total-text text-[19px] font-bold text-blue-900 md:text-[21px]">
                                Rp {{ number_format($row->subtotal, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Hapus --}}
                        <div class="flex justify-center">
                            <form method="POST" action="{{ route('keranjang.hapus') }}">
                                @csrf
                                <input type="hidden" name="id_detail" value="{{ (int)$row->id_keranjang_detail }}">
                                <button type="submit"
                                        class="text-red-500 transition hover:scale-110 hover:text-red-600">
                                    <i class="fa-regular fa-trash-can text-[20px]"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                @empty
                    <div class="min-h-[280px] border-b border-slate-200">
                        <div class="flex h-full items-center justify-center px-6 py-20 text-[18px] text-slate-400">
                            Keranjang masih kosong.
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Footer Keranjang --}}
            <div class="border-t border-slate-200 bg-white px-4 py-4 shadow-[0_-6px_18px_rgba(15,23,42,0.04)] md:px-6">
                <form action="{{ route('checkout') }}" method="GET" id="checkoutForm">
                    <input type="hidden" name="selected_items" id="selectedItemsInput">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_auto_240px] md:items-center md:gap-5">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="selectAll"
                                   class="h-6 w-6 cursor-pointer rounded-md border-2 border-slate-300 bg-white text-blue-900 accent-blue-900 transition"
                                   {{ $totalItem > 0 ? 'checked' : '' }}>
                            <p class="text-[18px] font-semibold text-slate-800 md:text-[20px]">
                                Pilih Semua (<span id="selectedCountText">{{ $totalItem }}</span> Produk)
                            </p>
                        </div>

                        <div class="text-left md:text-right">
                            <p class="text-[13px] font-medium text-slate-500">
                                Total Pesanan (<span id="selectedCountInfo">{{ $totalItem }}</span> Produk)
                            </p>
                            <p id="grandTotalText" class="mt-1 text-[30px] font-bold leading-none text-blue-900 md:text-[36px]">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" id="checkoutButton"
                                    class="inline-flex h-[56px] w-full items-center justify-center rounded-2xl bg-yellow-500 px-6 text-[20px] font-semibold text-blue-900 shadow-sm transition hover:bg-yellow-400 disabled:cursor-not-allowed disabled:opacity-60"
                                    {{ $totalItem > 0 ? '' : 'disabled' }}>
                                Pesan Sekarang
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </section>
    </div>
</main>

@endsection

@push('scripts')
<script>
    const itemCheckboxes   = document.querySelectorAll('.item-checkbox');
    const selectAll        = document.getElementById('selectAll');
    const selectedCountText = document.getElementById('selectedCountText');
    const selectedCountInfo = document.getElementById('selectedCountInfo');
    const grandTotalText   = document.getElementById('grandTotalText');
    const selectedInput    = document.getElementById('selectedItemsInput');
    const checkoutButton   = document.getElementById('checkoutButton');

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    function updateCartSummary() {
        let total = 0, count = 0, ids = [];

        itemCheckboxes.forEach(cb => {
            if (cb.checked) {
                count++;
                ids.push(cb.value);
                const row = cb.closest('.cart-item');
                total += Number(row.dataset.subtotal || 0);
            }
        });

        selectedCountText.textContent = count;
        selectedCountInfo.textContent = count;
        grandTotalText.textContent    = formatRupiah(total);
        selectedInput.value           = ids.join(',');
        checkoutButton.disabled       = count === 0;

        if (itemCheckboxes.length === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;
        }

        if (count === itemCheckboxes.length) {
            selectAll.checked = true;
            selectAll.indeterminate = false;
        } else if (count === 0) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        } else {
            selectAll.checked = false;
            selectAll.indeterminate = true;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateCartSummary();
        });
    }

    itemCheckboxes.forEach(cb => cb.addEventListener('change', updateCartSummary));
    updateCartSummary();
</script>
@endpush