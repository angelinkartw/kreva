<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ledger_entries')) {
            Schema::create('ledger_entries', function (Blueprint $table) {
                $table->id();
                $table->string('account_type', 20);
                $table->unsignedBigInteger('account_id')->default(0);
                $table->string('type', 40);
                $table->bigInteger('amount');
                $table->bigInteger('balance_after');
                $table->nullableMorphs('reference');
                $table->string('note')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['account_type', 'account_id']);
            });
        }

        if (! Schema::hasTable('payouts')) {
            Schema::create('payouts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_id')->constrained();
                $table->unsignedBigInteger('amount');
                $table->string('status', 20)->default('pending')->index();
                $table->string('bank', 50);
                $table->text('account_number');
                $table->string('account_holder');
                $table->timestamp('processed_at')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_item_id')->unique()->constrained()->cascadeOnDelete();
                $table->foreignId('buyer_id')->constrained('users', 'id_user');
                $table->foreignId('product_id')->constrained();
                $table->foreignId('store_id')->constrained();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->json('photos')->nullable();
                $table->text('seller_reply')->nullable();
                $table->string('moderation_status', 20)->default('published')->index();
                $table->timestamps();
                $table->index(['product_id', 'moderation_status']);
            });
        } else {
            $reviewForeignKeys = [
                'buyer_id' => ['users', 'id_user'],
                'product_id' => ['products', 'id'],
                'store_id' => ['stores', 'id'],
            ];
            $existingForeignKeys = Schema::getForeignKeys('reviews');

            foreach ($reviewForeignKeys as $column => [$referencedTable, $referencedColumn]) {
                $exists = false;

                foreach ($existingForeignKeys as $foreignKey) {
                    if (
                        $foreignKey['columns'] === [$column]
                        && $foreignKey['foreign_table'] === $referencedTable
                        && $foreignKey['foreign_columns'] === [$referencedColumn]
                    ) {
                        $exists = true;
                        break;
                    }
                }

                if (! $exists) {
                    Schema::table('reviews', function (Blueprint $table) use ($column, $referencedTable, $referencedColumn) {
                        $table->foreign($column)->references($referencedColumn)->on($referencedTable);
                    });
                }
            }

            if (! Schema::hasIndex('reviews', 'reviews_order_item_id_unique')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->unique('order_item_id');
                });
            }
        }

        if (! Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('store_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('buyer_id')->constrained('users', 'id_user');
                $table->text('reason');
                $table->string('status', 20)->default('open')->index();
                $table->text('resolution_note')->nullable();
                $table->foreignId('resolved_by')->nullable()->constrained('users', 'id_user');
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('user_notifications')) {
            Schema::create('user_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users', 'id_user')->cascadeOnDelete();
                $table->string('type', 50);
                $table->string('message');
                $table->json('data')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'read_at']);
            });
        }

        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('actor_id')->nullable()->constrained('users', 'id_user')->nullOnDelete();
                $table->string('action', 80)->index();
                $table->nullableMorphs('subject');
                $table->json('meta')->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reporter_id')->constrained('users', 'id_user');
                $table->morphs('subject');
                $table->string('reason', 50);
                $table->text('detail')->nullable();
                $table->string('status', 20)->default('open')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Existing tables may predate this migration; leave them intact to prevent data loss.
    }
};
