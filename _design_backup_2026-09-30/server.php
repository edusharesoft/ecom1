<?php
/**
 * Local dev router: runs the site from the project root (like cPanel),
 * so asset('public/...') links work.
 * Usage:  php -S 127.0.0.1:8000 server.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');

// Serve real static files (css, js, images) directly
if ($uri !== '/' && is_file(__DIR__ . $uri)) {
    return false;
}

require __DIR__ . '/index.php';
