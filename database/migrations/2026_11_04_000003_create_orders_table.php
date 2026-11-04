<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id_order');
            $table->unsignedBigInteger('id_user')->index();
            $table->string('nama_penerima', 150);
            $table->string('telepon', 30);
            $table->text('alamat');
            $table->decimal('total', 18, 2);
            $table->string('status', 30)->default('pending');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id_order_item');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_produk')->nullable();
            $table->string('nama_produk', 150);
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('kuantitas');
            $table->decimal('subtotal', 14, 2);

            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_produk')->references('id_produk')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
