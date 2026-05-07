<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

/**
 * Developer Diagnostic API Controller
 *
 * Endpoint ini dilindungi oleh API Key (DEV_API_SECRET di .env).
 * Digunakan oleh AI assistant untuk memeriksa kode, log, dan database
 * tanpa memerlukan akses FTP.
 *
 * SEMUA OPERASI BERSIFAT READ-ONLY (tidak ada write ke DB atau filesystem).
 */
class DevApiController extends Controller
{
    /**
     * GET /api/dev/ping
     * Cek koneksi dan status server.
     */
    public function ping()
    {
        return response()->json([
            'status'      => 'ok',
            'app'         => config('app.name'),
            'env'         => config('app.env'),
            'php_version' => PHP_VERSION,
            'laravel'     => app()->version(),
            'timestamp'   => now()->toDateTimeString(),
            'db_connected'=> $this->testDbConnection(),
        ]);
    }

    /**
     * GET /api/dev/logs?lines=100
     * Baca Laravel error log terbaru.
     */
    public function logs(Request $request)
    {
        $lines = min((int) $request->get('lines', 100), 500);
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return response()->json(['error' => 'File log tidak ditemukan.', 'path' => $logPath], 404);
        }

        // Ambil N baris terakhir secara efisien
        $content = $this->tailFile($logPath, $lines);

        return response()->json([
            'log_file'    => $logPath,
            'lines_shown' => $lines,
            'content'     => $content,
        ]);
    }

    /**
     * GET /api/dev/files?path=app/Http/Controllers
     * List file dalam direktori (relatif terhadap root proyek).
     */
    public function listFiles(Request $request)
    {
        $relativePath = ltrim($request->get('path', ''), '/\\');
        $allowedRoots = ['app', 'routes', 'resources/views', 'database', 'config', 'public'];

        // Keamanan: hanya izinkan direktori yang diperbolehkan
        $allowed = false;
        foreach ($allowedRoots as $root) {
            if (str_starts_with($relativePath, $root) || empty($relativePath)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed && !empty($relativePath)) {
            return response()->json(['error' => 'Akses ditolak. Direktori tidak diizinkan.'], 403);
        }

        $basePath = base_path($relativePath);

        if (!is_dir($basePath)) {
            return response()->json(['error' => 'Direktori tidak ditemukan: ' . $relativePath], 404);
        }

        $items = [];
        foreach (scandir($basePath) as $item) {
            if (in_array($item, ['.', '..'])) continue;
            $fullPath = $basePath . DIRECTORY_SEPARATOR . $item;
            $items[] = [
                'name'     => $item,
                'type'     => is_dir($fullPath) ? 'directory' : 'file',
                'size'     => is_file($fullPath) ? $this->formatSize(filesize($fullPath)) : null,
                'modified' => date('Y-m-d H:i:s', filemtime($fullPath)),
                'path'     => $relativePath ? $relativePath . '/' . $item : $item,
            ];
        }

        return response()->json([
            'directory' => $relativePath ?: '(root)',
            'count'     => count($items),
            'items'     => $items,
        ]);
    }

    /**
     * GET /api/dev/file?path=app/Http/Controllers/AdminController.php
     * Baca isi file dengan nomor baris.
     */
    public function readFile(Request $request)
    {
        $relativePath = ltrim($request->get('path', ''), '/\\');

        if (empty($relativePath)) {
            return response()->json(['error' => 'Parameter path diperlukan.'], 422);
        }

        // Keamanan: blokir path yang mencurigakan
        if (str_contains($relativePath, '..') || str_starts_with($relativePath, '/')) {
            return response()->json(['error' => 'Path tidak valid.'], 403);
        }

        $allowedExtensions = ['php', 'blade.php', 'js', 'css', 'json', 'env', 'md', 'sql', 'txt', 'xml', 'yaml', 'yml'];
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            return response()->json(['error' => 'Ekstensi file tidak diizinkan: ' . $ext], 403);
        }

        $fullPath = base_path($relativePath);

        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File tidak ditemukan: ' . $relativePath], 404);
        }

        $raw = file_get_contents($fullPath);
        $lines = explode("\n", $raw);

        $numbered = [];
        foreach ($lines as $i => $line) {
            $numbered[] = ($i + 1) . ': ' . $line;
        }

        return response()->json([
            'path'       => $relativePath,
            'lines'      => count($lines),
            'size'       => $this->formatSize(filesize($fullPath)),
            'modified'   => date('Y-m-d H:i:s', filemtime($fullPath)),
            'content'    => implode("\n", $numbered),
        ]);
    }

    /**
     * GET /api/dev/db/tables
     * List semua tabel dalam database beserta jumlah baris.
     */
    public function dbTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $key = 'Tables_in_' . $dbName;

            $result = [];
            foreach ($tables as $table) {
                $name = $table->$key;
                $count = DB::table($name)->count();
                $columns = Schema::getColumnListing($name);
                $result[] = [
                    'table'   => $name,
                    'rows'    => $count,
                    'columns' => $columns,
                ];
            }

            return response()->json([
                'database' => $dbName,
                'tables'   => count($result),
                'data'     => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * POST /api/dev/db/query
     * Jalankan query SELECT (READ-ONLY).
     * Body: { "sql": "SELECT * FROM users LIMIT 10" }
     */
    public function dbQuery(Request $request)
    {
        $sql = trim($request->input('sql', ''));

        if (empty($sql)) {
            return response()->json(['error' => 'Parameter sql diperlukan.'], 422);
        }

        // Keamanan: hanya izinkan SELECT, SHOW, DESCRIBE, EXPLAIN
        $firstWord = strtoupper(strtok($sql, " \t\n\r"));
        $allowed = ['SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN'];

        if (!in_array($firstWord, $allowed)) {
            return response()->json([
                'error' => 'Hanya query baca (SELECT/SHOW/DESCRIBE/EXPLAIN) yang diizinkan. Query write diblokir demi keamanan.',
            ], 403);
        }

        try {
            $start   = microtime(true);
            $results = DB::select($sql);
            $elapsed = round((microtime(true) - $start) * 1000, 2);

            return response()->json([
                'sql'          => $sql,
                'rows'         => count($results),
                'time_ms'      => $elapsed,
                'results'      => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'sql' => $sql], 500);
        }
    }

    /**
     * GET /api/dev/routes
     * List semua route yang terdaftar di aplikasi.
     */
    public function routes()
    {
        $routes = [];
        foreach (Route::getRoutes() as $route) {
            $routes[] = [
                'method'     => implode('|', $route->methods()),
                'uri'        => $route->uri(),
                'name'       => $route->getName(),
                'action'     => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        }

        return response()->json([
            'total'  => count($routes),
            'routes' => $routes,
        ]);
    }

    /**
     * GET /api/dev/errors?limit=20
     * Parse error terbaru dari Laravel log dengan format yang bersih.
     */
    public function errors(Request $request)
    {
        $limit   = min((int) $request->get('limit', 20), 100);
        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            return response()->json(['error' => 'File log tidak ditemukan.'], 404);
        }

        $content = file_get_contents($logPath);
        $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}[^\]]*)\]\s+(\w+)\.(\w+):\s+(.*?)(?=\[\d{4}|\Z)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $errors = [];
        foreach (array_slice(array_reverse($matches), 0, $limit) as $match) {
            $errors[] = [
                'timestamp'   => $match[1],
                'channel'     => $match[2],
                'level'       => $match[3],
                'message'     => trim(substr($match[4], 0, 1000)),
            ];
        }

        return response()->json([
            'count'  => count($errors),
            'errors' => $errors,
        ]);
    }

    /**
     * GET /api/dev/env
     * Tampilkan env yang aman (tanpa secrets/passwords).
     */
    public function envInfo()
    {
        $safe = [
            'APP_NAME'      => config('app.name'),
            'APP_ENV'       => config('app.env'),
            'APP_DEBUG'     => config('app.debug') ? 'true' : 'false',
            'APP_URL'       => config('app.url'),
            'DB_CONNECTION' => config('database.default'),
            'DB_HOST'       => config('database.connections.mysql.host'),
            'DB_DATABASE'   => config('database.connections.mysql.database'),
            'CACHE_STORE'   => config('cache.default'),
            'SESSION_DRIVER'=> config('session.driver'),
            'QUEUE_CONNECTION'=> config('queue.default'),
            'PHP_VERSION'   => PHP_VERSION,
            'LARAVEL_VERSION' => app()->version(),
        ];

        return response()->json($safe);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function testDbConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function tailFile(string $path, int $lines): string
    {
        $file  = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $total = $file->key();
        $start = max(0, $total - $lines);

        $output = '';
        $file->seek($start);
        while (!$file->eof()) {
            $output .= $file->fgets();
        }
        return $output;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
