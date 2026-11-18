<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_always_creates_buyer_even_if_role_is_sent(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Budi', 'email' => 'budi@test.id', 'password' => 'Password#12345', 'role' => 'admin',
        ])->assertCreated();

        $this->assertSame('buyer', \App\Models\User::where('email', 'budi@test.id')->first()->role->value);
    }

    public function test_login_requires_verification_then_works_after_otp(): void
    {
        $this->postJson('/api/v1/auth/register', ['name' => 'Budi', 'email' => 'budi@test.id', 'password' => 'Password#12345'])->assertCreated();
        $this->postJson('/api/v1/auth/login', ['email' => 'budi@test.id', 'password' => 'Password#12345'])->assertStatus(403)->assertJson(['code' => 'unverified']);

        // Ambil OTP dengan menyetel ulang kode yang diketahui
        $code = '123456';
        \App\Models\OtpCode::where('target', 'budi@test.id')->update(['code_hash' => hash_hmac('sha256', 'budi@test.id|'.$code, config('app.key'))]);
        $this->postJson('/api/v1/auth/otp/verify', ['channel' => 'email', 'target' => 'budi@test.id', 'code' => $code])->assertOk();

        $this->postJson('/api/v1/auth/login', ['email' => 'budi@test.id', 'password' => 'Password#12345'])
            ->assertOk()->assertJsonStructure(['access_token', 'expires_in', 'user']);
    }

    public function test_wrong_password_is_rejected(): void
    {
        \App\Models\User::forceCreate(['name' => 'A', 'email' => 'a@test.id', 'password' => 'Password#12345', 'role' => 'buyer', 'status' => 'active', 'email_verified_at' => now()]);
        $this->postJson('/api/v1/auth/login', ['email' => 'a@test.id', 'password' => 'salah'])->assertStatus(422);
    }
}
