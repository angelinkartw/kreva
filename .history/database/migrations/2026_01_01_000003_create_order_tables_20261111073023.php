<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            // Perbaikan: Menghubungkan buyer_id ke kolom primary key id_user pada tabel users
            $t->foreignId('buyer_id')->constrained('users', 'id_user');
            $t->string('status', 30)->index();           // PENDING_PAYMENT|PAID|EXPIRED|CANCELLED
            $t->unsignedBigInteger('total_items');
            $t->unsignedBigInteger('total_shipping');
            $t->unsignedBigInteger('total_paid');
            $t->json('address_snapshot');
            $t->timestamp('expired_at')->index();
            $t->timestamp('paid_at')->nullable();
            $t->timestamps();
            $t->index(['buyer_id', 'created_at']);
        });

        Schema::create('store_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('store_id')->constrained();
            $t->string('status', 30)->index();
            $t->string('courier', 30);
            $t->string('service', 30);
            $t->unsignedBigInteger('subtotal');
            $t->unsignedBigInteger('shipping_cost');
            $t->unsignedBigInteger('commission')->default(0);
            $t->string('tracking_number')->nullable();
            $t->timestamp('shipped_at')->nullable();
            $t->timestamp('delivered_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->text('cancel_reason')->nullable();
            $t->timestamps();
            $t->index(['store_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_order_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained();
            $t->foreignId('variant_id')->constrained('product_variants');
            $t->string('name_snapshot');
            $t->string('variant_label_snapshot')->nullable();
            $t->unsignedBigInteger('price_snapshot');
            $t->unsignedInteger('qty');
            $t->timestamps();
        });

        Schema::create('payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained()->cascadeOnDelete();
            $t->string('gateway', 30);
            $t->string('method', 30)->nullable();
            $t->string('gateway_reference')->nullable()->index();
            $t->string('status', 30)->index();            // pending|paid|failed|expired|refund_pending|refunded
            $t->unsignedBigInteger('amount');
            $t->string('redirect_url')->nullable();
            $t->timestamp('paid_at')->nullable();
            $t->json('webhook_payload')->nullable();
            $t->timestamps();
        });

        Schema::create('shipments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('store_order_id')->constrained()->cascadeOnDelete();
            $t->string('courier', 30);
            $t->string('tracking_number');
            $t->string('status', 30)->default('shipped');
            $t->json('tracking_history')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['shipments', 'payments', 'order_items', 'store_orders', 'orders'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};