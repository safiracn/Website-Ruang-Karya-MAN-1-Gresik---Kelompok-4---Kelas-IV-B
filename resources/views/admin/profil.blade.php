@extends('layouts.admin')

@section('title', 'Profil')
@section('activeMenu', 'profil')
@section('pageDesc', 'Kelola informasi akun admin Ruang Karya MAN 1 Gresik.')

@section('content')

@php
    $adminNama = $admin->nama_lengkap ?? 'Admin';
    $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($adminNama) . "&background=e2e8f0&color=1e3a8a&size=128";
@endphp

<section class="grid grid-cols-1 gap-8 xl:grid-cols-[240px_1fr]">

    {{-- Card Foto --}}
    <div class="h-fit rounded-[24px] bg-white p-7 shadow-sm ring-1 ring-slate-200">
        <div class="flex flex-col items-center">
            <div class="flex h-[112px] w-[112px] items-center justify-center overflow-hidden rounded-full ring-4 ring-slate-200">
                <img src="{{ $avatarUrl }}" alt="Foto Profil" class="h-full w-full rounded-full object-cover">
            </div>
            <p class="mt-4 font-bold text-blue-900 text-center">{{ $adminNama }}</p>
            <p class="text-xs text-slate-400 mt-1">System Root</p>
        </div>
    </div>

    {{-- Card Informasi --}}
    <div class="rounded-[24px] bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <div class="space-y-7">

            <div>
                <label class="mb-3 block text-[15px] font-semibold text-blue-900">Nama Lengkap</label>
                <input type="text" value="{{ $admin->nama_lengkap ?? '' }}" readonly
                       class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none">
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-3 block text-[15px] font-semibold text-blue-900">Email</label>
                    <input type="email" value="{{ $admin->email ?? '' }}" readonly
                           class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none">
                </div>
                <div>
                    <label class="mb-3 block text-[15px] font-semibold text-blue-900">No. Telepon</label>
                    <input type="text" value="{{ $admin->no_telp ?? '' }}" readonly
                           class="h-[56px] w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 text-[16px] text-slate-700 outline-none">
                </div>
            </div>

            <div>
                <label class="mb-3 block text-[15px] font-semibold text-blue-900">Alamat</label>
                <textarea rows="5" readonly
                          class="w-full cursor-default rounded-xl border border-slate-300 bg-slate-50 px-5 py-4 text-[16px] text-slate-700 outline-none resize-none">{{ $admin->alamat ?? '' }}</textarea>
            </div>

        </div>
    </div>

</section>

@endsection