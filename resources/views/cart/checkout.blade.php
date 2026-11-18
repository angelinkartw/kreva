@extends('marketplace.layout')

@section('title', 'Checkout')

@section('content')
    <div class="mx-auto grid max-w-6xl gap-6 lg:grid-cols-[1fr_360px]">
        <section>
            <a href="{{ route('cart.index') }}" class="text-sm font-semibold text-[#416743] hover:underline">&larr; Kembali ke keranjang</a>
            <h1 class="mt-3 text-3xl font-bold text-[#26402e]">Checkout</h1>
            <p class="mt-2 text-sm text-stone-600">Periksa alamat dan pilihan pengiriman sebelum membuat pesanan.</p>

            @if (session('database_error'))
                <div role="alert" class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('database_error') }}</div>
            @endif
            @if ($errors->any())
                <div role="alert" class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            @if ($addresses->isEmpty())
                <form method="POST" action="{{ route('checkout.addresses.store') }}" class="mt-6 space-y-4 rounded-2xl border border-stone-200 bg-white p-6">
                    @csrf
                    <h2 class="text-lg font-bold text-stone-900">Alamat pengiriman</h2>
                    <p class="text-sm text-stone-600">Tambahkan alamat agar kami dapat menghitung ongkir.</p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="recipient" class="mb-1 block text-sm font-semibold">Nama penerima</label>
                            <input id="recipient" name="recipient" value="{{ old('recipient', auth()->user()->nama_user) }}" required maxlength="255" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="phone" class="mb-1 block text-sm font-semibold">Nomor telepon</label>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required maxlength="20" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="province" class="mb-1 block text-sm font-semibold">Provinsi</label>
                            <input id="province" name="province" value="{{ old('province') }}" required maxlength="255" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="city" class="mb-1 block text-sm font-semibold">Kota/Kabupaten</label>
                            <input id="city" name="city" value="{{ old('city') }}" required maxlength="255" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="district" class="mb-1 block text-sm font-semibold">Kecamatan</label>
                            <input id="district" name="district" value="{{ old('district') }}" required maxlength="255" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="postal_code" class="mb-1 block text-sm font-semibold">Kode pos</label>
                            <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required maxlength="10" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="full_address" class="mb-1 block text-sm font-semibold">Alamat lengkap</label>
                        <textarea id="full_address" name="full_address" rows="3" required maxlength="2000" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">{{ old('full_address') }}</textarea>
                    </div>
                    <button type="submit" class="rounded-xl bg-[#26402e] px-5 py-3 text-sm font-semibold text-white hover:bg-[#1e3325]">Simpan alamat dan lanjutkan</button>
                </form>
            @else
                <form method="GET" action="{{ route('checkout.create') }}" class="mt-6 rounded-2xl border border-stone-200 bg-white p-6">
                    <label for="address_id" class="mb-2 block text-sm font-semibold">Alamat pengiriman</label>
                    <select id="address_id" name="address_id" onchange="this.form.submit()" class="w-full rounded-xl border border-stone-300 px-4 py-3 text-sm">
                        @foreach ($addresses as $savedAddress)
                            <option value="{{ $savedAddress->id }}" @selected($address?->id === $savedAddress->id)>
                                {{ $savedAddress->recipient }} — {{ $savedAddress->city }}, {{ $savedAddress->province }}
                            </option>
                        @endforeach
                    </select>
                    @if ($address)
                        <p class="mt-3 text-sm leading-6 text-stone-600">{{ $address->full_address }}, {{ $address->district }}, {{ $address->city }}, {{ $address->province }} {{ $address->postal_code }}</p>
                    @endif
                </form>

                @if ($address)
                    <form method="POST" action="{{ route('checkout.store') }}" class="mt-5 space-y-5 rounded-2xl border border-stone-200 bg-white p-6">
                        @csrf
                        <input type="hidden" name="address_id" value="{{ $address->id }}">
                        <h2 class="text-lg font-bold text-stone-900">Pilih pengiriman</h2>
                        @foreach ($shippingOptions as $group)
                            <fieldset class="rounded-xl border border-stone-200 p-4">
                                <legend class="px-2 text-sm font-semibold text-stone-800">{{ $group['store_name'] }}</legend>
                                <div class="space-y-2">
                                    @foreach ($group['options'] as $option)
                                        <label class="flex cursor-pointer items-center justify-between gap-3 rounded-lg border border-stone-100 p-3 text-sm hover:bg-stone-50">
                                            <span class="flex items-center gap-3">
                                                <input type="radio" name="shipping[{{ $group['store_id'] }}][choice]" value="{{ $option['courier'] }}|{{ $option['service'] }}" required>
                                                <span>{{ $option['label'] }} <span class="text-xs text-stone-500">({{ $option['etd'] }})</span></span>
                                            </span>
                                            <span class="whitespace-nowrap font-semibold">Rp {{ number_format($option['cost'], 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                        <button type="submit" class="w-full rounded-xl bg-[#26402e] px-5 py-3 font-semibold text-white hover:bg-[#1e3325]">Buat pesanan</button>
                    </form>
                @endif
            @endif
        </section>

        <aside class="h-fit rounded-2xl border border-stone-200 bg-white p-5">
            <h2 class="font-bold text-stone-900">Ringkasan pesanan</h2>
            <div class="mt-4 space-y-3">
                @foreach ($items as $item)
                    <div class="flex justify-between gap-3 text-sm">
                        <span class="text-stone-600">{{ $item->variant->product->name }} ({{ $item->variant->label ?: 'Default' }}) × {{ $item->qty }}</span>
                        <span class="whitespace-nowrap font-semibold">Rp {{ number_format($item->variant->price * $item->qty, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-between border-t border-stone-100 pt-4 font-bold">
                <span>Total produk</span>
                <span class="text-[#26402e]">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>
            <p class="mt-3 text-xs leading-5 text-stone-500">Ongkir dihitung berdasarkan alamat dan pilihan layanan untuk masing-masing toko.</p>
        </aside>
    </div>
@endsection
