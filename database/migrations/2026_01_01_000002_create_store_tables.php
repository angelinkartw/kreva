<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $t) {
            $t->id();
            // Perbaikan: ditambahkan ('users', 'id_user')
            $t->foreignId('user_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $t->string('label', 50)->nullable();
            $t->string('recipient');
            $t->string('phone', 20);
            $t->string('province');
            $t->string('city');
            $t->string('district');
            $t->string('postal_code', 10);
            $t->text('full_address');
            $t->decimal('latitude', 10, 7)->nullable();
            $t->decimal('longitude', 10, 7)->nullable();
            $t->boolean('is_default')->default(false);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('stores', function (Blueprint $t) {
            $t->id();
            // Perbaikan: ditambahkan ('users', 'id_user')
            $t->foreignId('user_id')->unique()->constrained('users', 'id_user')->cascadeOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->text('story')->nullable(); // "Cerita Pengrajin"
            $t->string('logo')->nullable();
            $t->string('banner')->nullable();
            $t->string('province')->nullable();
            $t->string('city')->nullable()->index();
            $t->string('district')->nullable();
            $t->string('postal_code', 10)->nullable();
            $t->text('origin_address')->nullable();
            $t->string('ktp_number')->nullable();      // dienkripsi (cast encrypted)
            $t->string('npwp_number')->nullable();     // dienkripsi
            $t->string('verification_status', 20)->default('pending')->index(); // pending|verified|rejected|suspended
            $t->text('verification_note')->nullable();
            $t->timestamp('verified_at')->nullable();
            $t->json('operating_hours')->nullable();
            $t->json('enabled_couriers')->nullable();
            $t->decimal('rating_avg', 3, 2)->default(0);
            $t->unsignedInteger('rating_count')->default(0);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('seller_bank_accounts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->string('bank', 50);
            $t->text('account_number');               // dienkripsi
            $t->string('account_holder');
            $t->boolean('verified')->default(false);
            $t->timestamp('changed_at')->nullable();  // untuk masa tunggu sebelum penarikan
            $t->timestamps();
        });

        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_id')->constrained()->cascadeOnDelete();
            $t->foreignId('category_id')->constrained();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('description')->nullable();
            $t->text('craftsman_story')->nullable();
            $t->string('status', 20)->default('draft')->index(); // draft|active|archived|banned
            $t->unsignedInteger('weight_gram');
            $t->unsignedInteger('length_cm')->nullable();
            $t->unsignedInteger('width_cm')->nullable();
            $t->unsignedInteger('height_cm')->nullable();
            $t->unsignedBigInteger('price_min')->default(0)->index(); // denormalisasi: harga varian terendah (Rupiah)
            $t->decimal('rating_avg', 3, 2)->default(0)->index();
            $t->unsignedInteger('rating_count')->default(0);
            $t->unsignedInteger('sold_count')->default(0)->index();
            $t->timestamps();
            $t->softDeletes();
            $t->index(['store_id', 'status']);
            $t->index(['category_id', 'status', 'created_at']);
        });

        Schema::create('product_variants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('sku')->nullable();
            $t->string('label')->nullable();           // mis. "Merah / L"
            $t->json('attributes')->nullable();
            $t->unsignedBigInteger('price');           // Rupiah, integer
            $t->unsignedInteger('stock')->default(0);
            $t->timestamps();
            $t->index(['product_id', 'stock']);
        });

        Schema::create('product_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('product_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('carts', function (Blueprint $t) {
            $t->id();
            // Perbaikan: ditambahkan ('users', 'id_user')
            $t->foreignId('user_id')->unique()->constrained('users', 'id_user')->cascadeOnDelete();
            $t->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $t->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $t->unsignedInteger('qty');
            $t->timestamps();
            $t->unique(['cart_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        foreach (['cart_items', 'carts', 'product_images', 'product_variants', 'products', 'categories', 'seller_bank_accounts', 'stores', 'addresses'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};