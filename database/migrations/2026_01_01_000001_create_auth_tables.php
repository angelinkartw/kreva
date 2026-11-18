<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $t) {
            $t->id();
            $t->string('channel', 10);          // email|phone
            $t->string('target')->index();
            $t->string('purpose', 30);          // verify|login|reset
            $t->string('code_hash');
            $t->unsignedTinyInteger('attempts')->default(0);
            $t->timestamp('expires_at');
            $t->timestamp('used_at')->nullable();
            $t->timestamps();
        });

        Schema::create('refresh_tokens', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $t->uuid('family_id')->index();     // untuk deteksi pemakaian ulang
            $t->string('token_hash', 64)->unique();
            $t->timestamp('expires_at');
            $t->timestamp('used_at')->nullable();
            $t->timestamp('revoked_at')->nullable();
            $t->string('ip', 45)->nullable();
            $t->string('user_agent')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['refresh_tokens', 'otp_codes'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
