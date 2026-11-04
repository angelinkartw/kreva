@extends('marketplace.layout')

@section('title', 'Tambah Produk')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('seller.products.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke produk</a>
        <h1 class="mt-4 text-3xl font-bold text-[#26402e]">Tambah Produk</h1>
        <p class="mt-2 text-sm text-stone-600">Lengkapi informasi produk agar pembeli dapat menemukannya.</p>
        <form method="POST" action="{{ route('seller.products.store') }}" class="mt-6">
            @csrf
            @include('seller.products.form')
            <button type="submit" class="mt-6 w-full rounded-xl bg-[#26402e] px-5 py-3 font-semibold text-white hover:bg-[#1e3325]">Simpan Produk</button>
        </form>
    </div>
@endsection
