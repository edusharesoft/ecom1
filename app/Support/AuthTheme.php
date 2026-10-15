<?php

namespace App\Support;

use Throwable;

/**
 * Login / registration page look (same as the Multi-Branch platform).
 *
 * Background photo and YouTube video are set by the admin (Site Setting → Login & Registration Page),
 * separately for the login and the registration pages (registration falls back to login).
 * Stored in storage/app/auth_theme.json, so no database change is needed.
 */
class AuthTheme
{
    public const KINDS = ['login', 'register'];

    protected static ?array $cache = null;

    public static function file(): string
    {
        return storage_path('app/auth_theme.json');
    }

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        try {
            $data = is_file(self::file()) ? json_decode((string) file_get_contents(self::file()), true) : [];
        } catch (Throwable) {
            $data = [];
        }

        return self::$cache = is_array($data) ? $data : [];
    }

    public static function get(string $key, $default = null)
    {
        $v = self::all()[$key] ?? null;

        return ($v === null || $v === '') ? $default : $v;
    }

    public static function save(array $values): void
    {
        $data = array_merge(self::all(), $values);
        if (! is_dir(dirname(self::file()))) {
            @mkdir(dirname(self::file()), 0775, true);
        }
        file_put_contents(self::file(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        self::$cache = $data;
    }

    public static function background(string $kind): ?string
    {
        $path = self::get($kind.'_bg') ?: self::get('login_bg');

        return $path ? asset($path) : null;
    }

    /** YouTube video id for the page, or null. */
    public static function video(string $kind): ?string
    {
        if ((string) self::get('show_video', '1') === '0') {
            return null;
        }

        return self::youtubeId((string) (self::get($kind.'_video') ?: self::get('login_video')));
    }

    /** Accepts watch?v=, youtu.be/, /shorts/, /embed/, /live/ links or a bare 11-character id. */
    public static function youtubeId(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $url)) {
            return $url;
        }
        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}
