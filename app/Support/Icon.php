<?php

namespace App\Support;

/**
 * Ikon Lucide yang disalin ke resources/svg/lucide (lihat scripts/sync-icons.mjs).
 * Isi SVG di-cache per proses supaya <x-ui.icon> murah dipakai berkali-kali dalam satu halaman.
 */
class Icon
{
    /** @var array<string, string|null> */
    private static array $cache = [];

    /** Isi di antara <svg ...> dan </svg>, atau null bila ikon tidak ada. */
    public static function inner(string $name): ?string
    {
        if (array_key_exists($name, self::$cache)) {
            return self::$cache[$name];
        }

        $inner = null;
        if (preg_match('/^[a-z0-9-]+$/', $name) === 1) {
            $path = resource_path("svg/lucide/{$name}.svg");
            if (is_file($path) && preg_match('/<svg[^>]*>(.*)<\/svg>/s', (string) file_get_contents($path), $m) === 1) {
                $inner = trim(preg_replace('/\s+/', ' ', $m[1]));
            }
        }

        return self::$cache[$name] = $inner;
    }

    /** SVG utuh sebagai string (untuk template JS), mis. Icon::svg('store', 'size-5'). */
    public static function svg(string $name, string $class = 'size-5', float|int $stroke = 2): string
    {
        $inner = self::inner($name);
        if ($inner === null) {
            return '';
        }

        return '<svg class="'.e($class).' shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="'.$stroke.'" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$inner.'</svg>';
    }
}
