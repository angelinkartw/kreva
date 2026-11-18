<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService
{
    // ---------- OTP ----------

    public function sendOtp(string $channel, string $target, string $purpose): void
    {
        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Batalkan OTP lama yang belum dipakai
        OtpCode::where('target', $target)->where('purpose', $purpose)->whereNull('used_at')->update(['used_at' => now()]);

        OtpCode::forceCreate([
            'channel' => $channel, 'target' => $target, 'purpose' => $purpose,
            'code_hash' => $this->hashOtp($target, $code),
            'expires_at' => now()->addMinutes(config('kreva.otp_ttl_minutes')),
        ]);

        NotificationService::sendOtp($channel, $target, $code);
    }

    public function verifyOtp(string $target, string $purpose, string $code): void
    {
        $otp = OtpCode::where('target', $target)->where('purpose', $purpose)
            ->whereNull('used_at')->where('expires_at', '>', now())->latest('id')->first();

        if (! $otp || $otp->attempts >= config('kreva.otp_max_attempts')) {
            throw new DomainException('Kode OTP tidak valid atau sudah kedaluwarsa.', 'otp_invalid');
        }

        $otp->increment('attempts');

        if (! hash_equals($otp->code_hash, $this->hashOtp($target, $code))) {
            throw new DomainException('Kode OTP salah.', 'otp_invalid');
        }

        $otp->forceFill(['used_at' => now()])->save();
    }

    private function hashOtp(string $target, string $code): string
    {
        return hash_hmac('sha256', $target.'|'.$code, config('app.key'));
    }

    // ---------- Token ----------

    /** @return array{access_token:string, expires_in:int, refresh_token:string} */
    public function issueTokens(User $user, Request $request, ?string $familyId = null): array
    {
        $access = $user->createToken('access', ['*'], now()->addMinutes(15));
        $plainRefresh = Str::random(64);

        DB::table('refresh_tokens')->insert([
            'user_id' => $user->id,
            'family_id' => $familyId ?? (string) Str::uuid(),
            'token_hash' => hash('sha256', $plainRefresh),
            'expires_at' => now()->addDays(config('kreva.refresh_ttl_days')),
            'ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return ['access_token' => $access->plainTextToken, 'expires_in' => 900, 'refresh_token' => $plainRefresh];
    }

    /** Rotasi refresh token + deteksi pemakaian ulang (cabut seluruh sesi bila terdeteksi). */
    public function refresh(string $plain, Request $request): array
    {
        $row = DB::table('refresh_tokens')->where('token_hash', hash('sha256', $plain))->first();

        if (! $row || $row->revoked_at || now()->greaterThan($row->expires_at)) {
            throw new DomainException('Sesi tidak valid. Silakan login ulang.', 'invalid_refresh');
        }

        if ($row->used_at) {
            // Token yang sudah dipakai muncul lagi => kemungkinan dicuri
            $this->revokeAll(User::findOrFail($row->user_id));
            AuditLogger::log('auth.refresh_reuse_detected', null, ['user_id' => $row->user_id], $row->user_id);
            throw new DomainException('Sesi tidak valid. Silakan login ulang.', 'refresh_reuse');
        }

        $user = User::findOrFail($row->user_id);
        if ($user->status !== 'active') {
            throw new DomainException('Akun tidak aktif.', 'inactive');
        }

        DB::table('refresh_tokens')->where('id', $row->id)->update(['used_at' => now(), 'updated_at' => now()]);

        return $this->issueTokens($user, $request, $row->family_id);
    }

    public function revokeFamily(string $plain): void
    {
        $row = DB::table('refresh_tokens')->where('token_hash', hash('sha256', $plain))->first();
        if ($row) {
            DB::table('refresh_tokens')->where('family_id', $row->family_id)->update(['revoked_at' => now()]);
        }
    }

    public function revokeAll(User $user): void
    {
        DB::table('refresh_tokens')->where('user_id', $user->id)->whereNull('revoked_at')->update(['revoked_at' => now()]);
        $user->tokens()->delete();
    }
}
