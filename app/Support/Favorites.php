<?php

namespace App\Support;

use App\Models\Favorit;

/** ID menu favorit milik user yang login (di-cache per request supaya kartu tidak query berulang). */
class Favorites
{
    private static ?array $ids = null;

    /** @return array<int, int> */
    public static function ids(): array
    {
        if (self::$ids !== null) {
            return self::$ids;
        }

        return self::$ids = auth()->check()
            ? Favorit::where('user_id', auth()->id())->whereNotNull('menu_id')->pluck('menu_id')->map(fn ($id) => (int) $id)->all()
            : [];
    }

    public static function has(int $menuId): bool
    {
        return in_array($menuId, self::ids(), true);
    }

    /** Untuk test: kosongkan cache statis. */
    public static function flush(): void
    {
        self::$ids = null;
    }
}
