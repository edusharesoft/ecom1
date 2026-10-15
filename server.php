<?php
/**
 * Local dev router: runs the site from the project root (like cPanel),
 * so asset('public/...') links work.
 * Usage:  php -S 127.0.0.1:8000 server.php
 *
 * Static files are sent by this script itself, so it works no matter which
 * folder the command is started from (or if -t public is given).
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
$file = realpath(__DIR__ . $uri);

// Serve real static files (css, js, images, fonts) directly
if ($uri !== '/' && $file && is_file($file) && str_starts_with($file, __DIR__)
    && strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'php') {
    $types = [
        'css' => 'text/css', 'js' => 'application/javascript', 'mjs' => 'application/javascript', 'json' => 'application/json',
        'map' => 'application/json', 'svg' => 'image/svg+xml', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'ico' => 'image/x-icon', 'avif' => 'image/avif', 'bmp' => 'image/bmp',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'otf' => 'font/otf', 'eot' => 'application/vnd.ms-fontobject',
        'mp4' => 'video/mp4', 'webm' => 'video/webm', 'mp3' => 'audio/mpeg', 'pdf' => 'application/pdf', 'txt' => 'text/plain',
        'xml' => 'application/xml', 'html' => 'text/html', 'htm' => 'text/html', 'apk' => 'application/vnd.android.package-archive',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    header('Content-Type: ' . ($types[$ext] ?? (function_exists('mime_content_type') ? (mime_content_type($file) ?: 'application/octet-stream') : 'application/octet-stream')));
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=3600');
    readfile($file);
    return true;
}

require __DIR__ . '/index.php';
