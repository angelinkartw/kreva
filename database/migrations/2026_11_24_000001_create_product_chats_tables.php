<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['buyer_id', 'product_id']);
            $table->index(['seller_id', 'updated_at']);
        });

        Schema::create('product_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('product_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_messages');
        Schema::dropIfExists('product_conversations');
    }
};
