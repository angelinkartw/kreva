<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

/**
 * Notifikasi dalam aplikasi disimpan di DB. Email/WhatsApp: sambungkan di sini
 * (dispatch Job berantrian dengan retry + dead-letter via failed_jobs).
 */
class NotificationService
{
    public static function notify(User $user, string $type, string $message, array $data = []): void
    {
        UserNotification::forceCreate(['user_id' => $user->getKey(), 'type' => $type, 'message' => $message, 'data' => $data ?: null]);
        // TODO: dispatch(new SendEmailNotification(...)); dispatch(new SendWhatsAppNotification(...));
    }

    public static function sendOtp(string $channel, string $target, string $code): void
    {
        // TODO: kirim lewat Mail / penyedia WhatsApp Business API resmi. Jangan log kode OTP di produksi.
        if (! app()->environment('production')) {
            Log::info("[OTP] $channel:$target => $code");
        }
    }
}
