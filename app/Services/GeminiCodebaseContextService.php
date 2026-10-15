<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GeminiCodebaseContextService
{
    protected const INDEX_CACHE_KEY = 'gemini_codebase_index_v1';

    protected const FILES_CACHE_KEY = 'gemini_codebase_files_v1';

    protected const CACHE_TTL = 3600;

    protected int $maxSearchFiles = 6;

    protected int $maxLinesPerSnippet = 100;

    protected string $projectRoot;

    /** @var array<int, string> */
    protected array $excludeDirs = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        'ecom1',
        '.git',
        'public/build',
    ];

    /** @var array<int, string> */
    protected array $searchExtensions = ['php', 'blade.php'];

    public function __construct()
    {
        $this->projectRoot = base_path();
    }

    public function buildCodebaseIndex(): string
    {
        return Cache::remember(self::INDEX_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->compileIndex();
        });
    }

    public function refreshIndex(): void
    {
        Cache::forget(self::INDEX_CACHE_KEY);
        Cache::forget(self::FILES_CACHE_KEY);
    }

    public function searchRelevantFiles(?string $message): string
    {
        if ($message === null || trim($message) === '') {
            return '';
        }

        $keywords = $this->extractKeywords($message);
        if ($keywords === []) {
            return '';
        }

        $files = $this->getSearchableFiles();
        $candidates = [];

        foreach ($files as $file) {
            $relative = $this->relativePath($file);
            $basename = strtolower(basename($file));
            $score = 0;

            foreach ($keywords as $keyword) {
                if (str_contains($basename, $keyword)) {
                    $score += 12;
                }
                if (str_contains(strtolower($relative), $keyword)) {
                    $score += 6;
                }
            }

            if ($score > 0) {
                $candidates[] = ['path' => $relative, 'file' => $file, 'score' => $score];
            }
        }

        if ($candidates === []) {
            foreach ($files as $file) {
                $relative = strtolower($this->relativePath($file));
                if (! str_contains($relative, 'controller')
                    && ! str_contains($relative, 'models/')
                    && ! str_contains($relative, 'services/')
                    && ! str_contains($relative, 'routes/')
                    && ! str_contains($relative, 'views/backend')) {
                    continue;
                }
                $candidates[] = [
                    'path' => $this->relativePath($file),
                    'file' => $file,
                    'score' => 1,
                ];
            }
            $candidates = array_slice($candidates, 0, 35);
        }

        foreach ($candidates as &$candidate) {
            $content = @file_get_contents($candidate['file']);
            if ($content === false) {
                continue;
            }
            $lower = strtolower($content);
            foreach ($keywords as $keyword) {
                $candidate['score'] += substr_count($lower, $keyword) * 2;
            }
            $candidate['content'] = $content;
        }
        unset($candidate);

        $scored = array_values(array_filter($candidates, static fn ($item) => ($item['score'] ?? 0) > 0));

        if ($scored === []) {
            return '';
        }

        usort($scored, static fn ($a, $b) => $b['score'] <=> $a['score']);
        $top = array_slice($scored, 0, $this->maxSearchFiles);

        if ($top === []) {
            return '';
        }

        $blocks = ['## Relevant source code snippets (matched admin question)'];

        foreach ($top as $item) {
            $content = $item['content'] ?? (@file_get_contents($item['file']) ?: '');
            if ($content === '') {
                continue;
            }
            $blocks[] = $this->formatSnippet($item['path'], $content, $keywords);
        }

        return implode("\n\n", $blocks);
    }

    protected function compileIndex(): string
    {
        $lines = [];
        $lines[] = 'Project root: ' . $this->projectRoot;
        $lines[] = 'Framework: Laravel ' . app()->version();
        $lines[] = 'PHP: ' . PHP_VERSION;
        $lines[] = '';

        $lines[] = '### Directory map';
        foreach (['app/Http/Controllers/Admin', 'app/Http/Controllers/Frontend', 'app/Models', 'app/Services', 'routes', 'resources/views/backEnd', 'config'] as $dir) {
            $full = base_path($dir);
            if (! is_dir($full)) {
                continue;
            }
            $count = $this->countFiles($full);
            $lines[] = "- {$dir}/ ({$count} files)";
        }
        $lines[] = '';

        $lines[] = '### Admin controllers & public methods';
        $lines = array_merge($lines, $this->indexAdminControllers());
        $lines[] = '';

        $lines[] = '### Models (' . $this->countFiles(app_path('Models')) . ')';
        $lines = array_merge($lines, $this->indexModels());
        $lines[] = '';

        $lines[] = '### Services';
        $lines = array_merge($lines, $this->indexServices());
        $lines[] = '';

        $lines[] = '### Admin routes (name → URI)';
        $lines = array_merge($lines, $this->indexAdminRoutes());
        $lines[] = '';

        $lines[] = '### Key config';
        $lines = array_merge($lines, $this->indexKeyConfig());

        return implode("\n", $lines);
    }

    /**
     * @return array<int, string>
     */
    protected function indexAdminControllers(): array
    {
        $dir = app_path('Http/Controllers/Admin');
        if (! is_dir($dir)) {
            return ['(none)'];
        }

        $lines = [];
        $files = glob($dir . '/*.php') ?: [];

        sort($files);

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $methods = $this->extractPublicMethods($file);
            $methodList = $methods !== [] ? implode(', ', array_slice($methods, 0, 20)) : 'no public methods';
            if (count($methods) > 20) {
                $methodList .= ', ...';
            }
            $lines[] = "- {$name}: {$methodList}";
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    protected function indexModels(): array
    {
        $dir = app_path('Models');
        $files = glob($dir . '/*.php') ?: [];
        sort($files);

        $names = array_map(static fn ($f) => basename($f, '.php'), $files);

        return ['- ' . implode(', ', $names)];
    }

    /**
     * @return array<int, string>
     */
    protected function indexServices(): array
    {
        $dir = app_path('Services');
        if (! is_dir($dir)) {
            return ['(none)'];
        }

        $lines = [];
        $files = glob($dir . '/*.php') ?: [];
        sort($files);

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $summary = $this->extractClassSummary($file);
            $lines[] = $summary !== '' ? "- {$name}: {$summary}" : "- {$name}";
        }

        return $lines;
    }

    /**
     * @return array<int, string>
     */
    protected function indexAdminRoutes(): array
    {
        $routesFile = base_path('routes/web.php');
        if (! is_file($routesFile)) {
            return ['(routes/web.php missing)'];
        }

        $content = file_get_contents($routesFile) ?: '';
        $lines = [];
        $routeLines = explode("\n", $content);

        foreach ($routeLines as $line) {
            $trimmed = trim($line);
            if (! str_contains($trimmed, 'Route::')) {
                continue;
            }
            if (! str_contains($trimmed, "name('admin.") && ! str_contains($trimmed, 'prefix(')) {
                continue;
            }
            if (preg_match("/->name\('([^']+)'\)/", $trimmed, $nameMatch)) {
                $routeName = $nameMatch[1];
                if (! str_starts_with($routeName, 'admin.')) {
                    continue;
                }
                $uri = '';
                if (preg_match("/Route::(?:get|post|put|patch|delete|match)\(\s*'([^']*)'/", $trimmed, $uriMatch)) {
                    $uri = $uriMatch[1];
                }
                $lines[] = "- {$routeName} → /admin/{$uri}";
            }
        }

        return array_slice($lines, 0, 200);
    }

    /**
     * @return array<int, string>
     */
    protected function indexKeyConfig(): array
    {
        return [
            '- app.name: ' . config('app.name'),
            '- app.url: ' . config('app.url'),
            '- app.env: ' . config('app.env'),
            '- app.debug: ' . (config('app.debug') ? 'true' : 'false'),
            '- services.gemini.model: ' . config('services.gemini.model', 'gemini-2.5-flash'),
            '- DEMO_MODE env may block admin saves when true',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function extractPublicMethods(string $file): array
    {
        $content = file_get_contents($file) ?: '';
        preg_match_all('/public function ([a-zA-Z0-9_]+)\s*\(/', $content, $matches);

        $methods = $matches[1] ?? [];
        $skip = ['__construct', '__invoke'];

        return array_values(array_filter($methods, static fn ($m) => ! in_array($m, $skip, true)));
    }

    protected function extractClassSummary(string $file): string
    {
        $content = file_get_contents($file) ?: '';
        if (preg_match('/\/\*\*\s*\n\s*\*\s*(.+?)\n/s', $content, $match)) {
            return trim(preg_replace('/\s+/', ' ', $match[1]));
        }

        return '';
    }

    /**
     * @return array<int, string>
     */
    protected function extractKeywords(string $message): array
    {
        $normalized = mb_strtolower($message);

        $map = [
            'অর্ডার' => 'order',
            'প্রোডাক্ট' => 'product',
            'ভেন্ডর' => 'vendor',
            'কাস্টমার' => 'customer',
            'লাভ' => 'profit',
            'ক্ষতি' => 'loss',
            'রিপোর্ট' => 'report',
            'পেমেন্ট' => 'payment',
            'কুরিয়ার' => 'courier',
            'ডেলিভারি' => 'delivery',
            'সেটিং' => 'setting',
            'এরর' => 'error',
            'বাগ' => 'bug',
            'রাউট' => 'route',
            'মাইগ্রেশন' => 'migration',
            'জেমিনি' => 'gemini',
            'এআই' => 'ai',
            'চ্যাট' => 'chat',
            'নোটিফিকেশন' => 'notification',
            'কুপন' => 'coupon',
            'রিসেলার' => 'reseller',
            'ব্যানার' => 'banner',
            'এসইও' => 'seo',
            'এসএমএস' => 'sms',
            'ফ্রড' => 'fraud',
            'হোমপেজ' => 'homepage',
            'কোড' => 'code',
            'ফাইল' => 'file',
            'কন্ট্রোলার' => 'controller',
            'মডেল' => 'model',
        ];

        foreach ($map as $bn => $en) {
            if (str_contains($normalized, $bn)) {
                $message .= ' ' . $en;
            }
        }

        $tokens = preg_split('/[^a-z0-9_]+/i', mb_strtolower($message)) ?: [];
        $stop = ['the', 'a', 'an', 'is', 'are', 'am', 'i', 'my', 'me', 'how', 'what', 'why', 'when', 'where', 'can', 'do', 'does', 'ki', 'kivabe', 'kemon', 'ache', 'ki', 'এটা', 'আমি', 'আমার', 'কি', 'কিভাবে', 'কেন', 'হয়', 'হবে', 'দাও', 'বলো', 'please'];

        $keywords = [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if (strlen($token) < 3 || in_array($token, $stop, true)) {
                continue;
            }
            $keywords[] = $token;
        }

        return array_values(array_unique($keywords));
    }

    /**
     * @return array<int, string>
     */
    protected function getSearchableFiles(): array
    {
        return Cache::remember(self::FILES_CACHE_KEY, self::CACHE_TTL, function () {
            return $this->collectSearchableFiles();
        });
    }

    /**
     * @return array<int, string>
     */
    protected function collectSearchableFiles(): array
    {
        $roots = [
            app_path(),
            base_path('routes'),
            resource_path('views/backEnd'),
            config_path(),
        ];

        $files = [];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $fileInfo) {
                if (! $fileInfo->isFile()) {
                    continue;
                }

                $path = $fileInfo->getPathname();
                if ($this->shouldExclude($path)) {
                    continue;
                }

                $ext = strtolower($fileInfo->getExtension());
                if ($ext === 'php' || str_ends_with($path, '.blade.php')) {
                    if ($fileInfo->getSize() > 512000) {
                        continue;
                    }
                    $files[] = $path;
                }
            }
        }

        return $files;
    }

    protected function shouldExclude(string $path): bool
    {
        $normalized = str_replace('\\', '/', $path);

        foreach ($this->excludeDirs as $exclude) {
            if (str_contains($normalized, '/' . trim($exclude, '/') . '/')) {
                return true;
            }
        }

        $basename = basename($path);

        if (in_array($basename, ['.env', '.env.backup', 'composer.lock', 'package-lock.json'], true)) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<int, string>  $keywords
     */
    protected function formatSnippet(string $relativePath, string $content, array $keywords): string
    {
        $lines = explode("\n", $content);
        $matchedLineNumbers = [];

        foreach ($lines as $i => $line) {
            $lower = strtolower($line);
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) {
                    $matchedLineNumbers[] = $i;
                    break;
                }
            }
        }

        if ($matchedLineNumbers === []) {
            $start = 0;
            $end = min(count($lines) - 1, $this->maxLinesPerSnippet - 1);
        } else {
            $center = (int) floor(array_sum($matchedLineNumbers) / count($matchedLineNumbers));
            $half = (int) floor($this->maxLinesPerSnippet / 2);
            $start = max(0, $center - $half);
            $end = min(count($lines) - 1, $start + $this->maxLinesPerSnippet - 1);
        }

        $snippetLines = [];
        for ($i = $start; $i <= $end; $i++) {
            $snippetLines[] = sprintf('%4d| %s', $i + 1, rtrim($lines[$i] ?? ''));
        }

        return "### File: {$relativePath}\n```\n" . implode("\n", $snippetLines) . "\n```";
    }

    protected function countFiles(string $directory): int
    {
        $count = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isFile()) {
                $count++;
            }
        }

        return $count;
    }

    protected function relativePath(string $absolute): string
    {
        $normalizedRoot = str_replace('\\', '/', $this->projectRoot);
        $normalizedPath = str_replace('\\', '/', $absolute);

        return ltrim(str_replace($normalizedRoot, '', $normalizedPath), '/');
    }
}
