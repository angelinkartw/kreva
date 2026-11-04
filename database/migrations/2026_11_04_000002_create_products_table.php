<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id_produk');
            $table->unsignedBigInteger('id_user')->index();
            $table->string('nama_produk', 150);
            $table->text('deskripsi')->nullable();
            $table->string('kategori', 100);
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
