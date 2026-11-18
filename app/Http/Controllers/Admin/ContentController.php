<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

/** Konten beranda (banner, kategori unggulan) disimpan sebagai JSON di tabel settings. */
class ContentController extends Controller
{
    private const KEYS = ['home_banners', 'home_featured_categories'];

    public function show(string $key)
    {
        abort_unless(in_array($key, self::KEYS, true), 404);

        return json_decode((string) Setting::get($key, '[]'), true);
    }

    public function update(Request $request, string $key)
    {
        abort_unless(in_array($key, self::KEYS, true), 404);
        $data = $request->validate(['value' => ['required', 'array', 'max:20']]);
        Setting::put($key, json_encode($data['value']));

        return $data['value'];
    }
}
