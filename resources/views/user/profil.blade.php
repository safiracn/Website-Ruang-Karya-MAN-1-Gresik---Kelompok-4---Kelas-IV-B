@extends('layouts.app')

@section('title', 'Profil Pengguna - Ruang Karya')

@section('content')

<div class="h-[40px]"></div>

<main class="px-6 pb-16">
    <div class="mx-auto max-w-[1280px]">

        {{-- Heading + Logout --}}
        <section class="mb-8 flex items-start justify-between gap-6">
            <div>
                <h1 class="text-[46px] font-extrabold leading-none text-blue-900">Profil Pengguna</h1>
                <p class="mt-3 text-[17px] text-slate-500">
                    Sesuaikan informasi publik dan pengaturan akun Anda di Ruang Karya MAN 1 Gresik.
                </p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-[15px] font-semibold text-red-500 shadow-sm transition hover:bg-red-50">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    Logout
                </button>
            </form>
        </section>

        {{-- Konten --}}
        <section class="grid grid-cols-1 gap-8 xl:grid-cols-[260px_1fr]">

            {{-- Foto Kiri --}}
            <div class="h-fit rounded-[24px] bg-white p-7 shadow-sm ring-1 ring-slate-200">
                <div class="flex flex-col items-center">
                    <div class="h-[150px] w-[150px] overflow-hidden rounded-full">
                        <img src="{{ $avatarUrl }}" alt="Foto Profil"
                             class="h-full w-full rounded-full object-cover">
                    </div>
                    <p class="mt-4 font-bold text-blue-900 text-center">{{ $user->nama_lengkap ?? '-' }}</p>
                    <span class="mt-1 text-xs text-slate-400">Pengguna</span>
                </div>
            </div>

            {{-- Informasi --}}
            <div class="rounded-[24px] bg-white p-10 shadow-sm ring-1 ring-slate-200">
                <div class="space-y-8">

                    <div>
                        <label class="mb-4 block text-[15px] font-semibold text-blue-900">Nama Lengkap</label>
                        <input type="text" value="{{ $user->nama_lengkap ?? '' }}" readonly
                               class="h-[64px] w-full cursor-default rounded-2xl border border-slate-300 bg-slate-50 px-6 text-[18px] text-slate-700 outline-none">
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <label class="mb-4 block text-[15px] font-semibold text-blue-900">Email</label>
                            <input type="email" value="{{ $user->email ?? '' }}" readonly
                                   class="h-[64px] w-full cursor-default rounded-2xl border border-slate-300 bg-slate-50 px-6 text-[18px] text-slate-700 outline-none">
                        </div>
                        <div>
                            <label class="mb-4 block text-[15px] font-semibold text-blue-900">No. Telepon</label>
                            <input type="text" value="{{ $user->no_telp ?? '' }}" readonly
                                   class="h-[64px] w-full cursor-default rounded-2xl border border-slate-300 bg-slate-50 px-6 text-[18px] text-slate-700 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="mb-4 block text-[15px] font-semibold text-blue-900">Alamat</label>
                        <textarea rows="7" readonly
                                  class="w-full cursor-default rounded-2xl border border-slate-300 bg-slate-50 px-6 py-5 text-[18px] text-slate-700 outline-none resize-none">{{ $user->alamat ?? '' }}</textarea>
                    </div>

                </div>
            </div>

        </section>
    </div>
</main>

@endsection