<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $categories = [
        ['name' => 'Makanan & Minuman', 'slug' => 'makanan-minuman', 'sort_order' => 1],
        ['name' => 'Fashion & Aksesori', 'slug' => 'fashion-aksesori', 'sort_order' => 2],
        ['name' => 'Kerajinan Tangan', 'slug' => 'kerajinan-tangan', 'sort_order' => 3],
        ['name' => 'Kecantikan & Perawatan', 'slug' => 'kecantikan-perawatan', 'sort_order' => 4],
        ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'sort_order' => 5],
        ['name' => 'Pertanian & Perkebunan', 'slug' => 'pertanian-perkebunan', 'sort_order' => 6],
        ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'sort_order' => 7],
        ['name' => 'Lainnya', 'slug' => 'lainnya', 'sort_order' => 8],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        $timestamp = now();
        $rows = array_map(
            fn (array $category): array => [...$category, 'created_at' => $timestamp, 'updated_at' => $timestamp],
            $this->categories,
        );

        DB::table('categories')->insertOrIgnore($rows);
    }

    public function down(): void
    {
    }
};
