@extends('marketplace.layout')

@section('title', 'Edit Produk')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('seller.products.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke produk</a>
        <h1 class="mt-4 text-3xl font-bold text-[#26402e]">Edit Produk</h1>
        <p class="mt-2 text-sm text-stone-600">Perbarui informasi produk Anda.</p>
        <form method="POST" action="{{ route('seller.products.update', $product) }}" class="mt-6">
            @csrf
            @method('PUT')
            @include('seller.products.form')
            <button type="submit" class="mt-6 w-full rounded-xl bg-[#26402e] px-5 py-3 font-semibold text-white hover:bg-[#1e3325]">Simpan Perubahan</button>
        </form>
    </div>
@endsection
