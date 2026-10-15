<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageOptimizer
{
    /** Target max file size in KB (dimensions unchanged). */
    public const TARGET_KB = 200;

    /** Start at high quality; lower only if needed to reach TARGET_KB. */
    public const START_QUALITY = 95;

    /**
     * Save as WebP keeping original width and height.
     * Compresses toward 200 KB without resizing.
     */
    public static function store(UploadedFile $file, string $directory, ?string $basename = null): string
    {
        [$fullDir, $relativeDir] = self::resolveDirectory($directory);
        File::ensureDirectoryExists($fullDir);

        $basename = $basename
            ? self::normalizeBasename($basename)
            : self::makeBasename($file);

        $fullPath     = $fullDir . $basename;
        $relativePath = $relativeDir . $basename;
        $maxBytes     = self::TARGET_KB * 1024;

        self::saveOptimizedWebp($file->getRealPath(), $fullPath, $maxBytes);

        return $relativePath;
    }

    public static function makeBasename(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($name) ?: 'image';

        return time() . '-' . uniqid() . '-' . $name . '.webp';
    }

    public static function basenameFromOriginal(UploadedFile $file, string $prefix = ''): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::slug($name) ?: 'image';
        $prefix = $prefix !== '' ? rtrim($prefix, '-') . '-' : '';

        return self::normalizeBasename($prefix . time() . '-' . uniqid() . '-' . $name);
    }

    public static function normalizeBasename(string $basename): string
    {
        $basename = trim($basename);
        $basename = preg_replace('/\.(jpe?g|png|gif|webp|bmp)$/i', '', $basename) ?? $basename;
        $basename = Str::slug($basename) ?: 'image';

        return $basename . '.webp';
    }

    private static function resolveDirectory(string $directory): array
    {
        $directory = rtrim(str_replace('\\', '/', $directory), '/') . '/';

        if (str_starts_with($directory, 'public/')) {
            return [base_path($directory), $directory];
        }

        return [public_path($directory), $directory];
    }

    private static function saveOptimizedWebp(string $sourcePath, string $fullPath, int $maxBytes): void
    {
        $startQuality = self::START_QUALITY;
        $size         = self::saveAtQuality($sourcePath, $fullPath, $startQuality);

        if ($size <= $maxBytes) {
            return;
        }

        $estimated = (int) round($startQuality * pow($maxBytes / max($size, 1), 0.72) * 0.96);
        $estimated = max(55, min(94, $estimated));

        if ($estimated < $startQuality) {
            $size = self::saveAtQuality($sourcePath, $fullPath, $estimated);
            if ($size <= $maxBytes) {
                return;
            }
        }

        $fallback = max(48, $estimated - 12);
        if ($fallback < $estimated) {
            self::saveAtQuality($sourcePath, $fullPath, $fallback);
        }
    }

    private static function saveAtQuality(string $sourcePath, string $fullPath, int $quality): int
    {
        $image = Image::make($sourcePath);
        self::orientate($image);
        $image->encode('webp', $quality);
        $image->save($fullPath);
        $image->destroy();

        return file_exists($fullPath) ? (int) filesize($fullPath) : 0;
    }

    private static function orientate($image): void
    {
        if (method_exists($image, 'orientate')) {
            $image->orientate();
        }
    }
}
