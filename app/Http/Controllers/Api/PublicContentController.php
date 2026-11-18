<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class PublicContentController extends Controller
{
    public function home()
    {
        return [
            'banners' => json_decode((string) Setting::get('home_banners', '[]'), true),
            'featured_categories' => json_decode((string) Setting::get('home_featured_categories', '[]'), true),
        ];
    }
}
