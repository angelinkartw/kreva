<?php

namespace App\Support;

class Html
{
    /**
     * Sanitasi dasar deskripsi dari penjual: allowlist tag, semua atribut dibuang.
     * Frontend tetap wajib menyanitasi lagi (DOMPurify). Untuk kebutuhan lanjut pakai mews/purifier.
     */
    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }
        $html = strip_tags($html, '<p><br><ul><ol><li><strong><em><b><i><h3><h4>');

        return preg_replace('/<([a-z0-9]+)\b[^>]*>/i', '<$1>', $html);
    }

    public static function phone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }
        $p = preg_replace('/[^0-9]/', '', $phone);

        return match (true) {
            str_starts_with($p, '62') => $p,
            str_starts_with($p, '0') => '62'.substr($p, 1),
            default => '62'.$p,
        };
    }
}
