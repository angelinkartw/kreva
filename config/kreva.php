<?php

return [
    'payment_driver' => env('PAYMENT_DRIVER', 'fake'),            // fake | midtrans
    'commission_percent' => (float) env('KREVA_COMMISSION_PERCENT', 5),
    'payment_expire_hours' => (int) env('KREVA_PAYMENT_EXPIRE_HOURS', 24),
    'auto_complete_days' => (int) env('KREVA_AUTO_COMPLETE_DAYS', 3),         // setelah barang tiba
    'auto_complete_after_ship_days' => (int) env('KREVA_AUTO_COMPLETE_SHIP_DAYS', 10), // fallback bila tracking belum terintegrasi
    'min_payout' => (int) env('KREVA_MIN_PAYOUT', 50000),
    'payout_wait_days' => (int) env('KREVA_PAYOUT_WAIT_DAYS', 3),    // masa tunggu setelah ganti rekening
    'otp_ttl_minutes' => 5,
    'otp_max_attempts' => 5,
    'refresh_ttl_days' => 30,
    'max_variants_per_product' => 50,
];
