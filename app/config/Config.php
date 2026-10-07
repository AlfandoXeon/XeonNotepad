<?php
declare(strict_types=1);

/**
 * Config — loads .env and defines application constants.
 */
class Config
{
    private static array $data = [];

    public static function load(): void
    {
        $envFile = ROOT_PATH . '/.env';
        if (!file_exists($envFile)) {
            die('<h2>Configuration Error</h2><p>.env file not found. Please create it from .env.example.</p>');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (!str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            self::$data[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
        }

        define('APP_NAME',       self::get('APP_NAME', 'Xeon Notepad'));
        define('APP_ENV',        self::get('APP_ENV', 'production'));

        $configuredUrl = self::get('APP_URL', '');
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        $scheme = $isHttps ? 'https' : 'http';

        // Auto-detect production URL if APP_URL is empty or points to localhost while running on live host (e.g. InfinityFree)
        if (empty($configuredUrl) || (str_contains($configuredUrl, 'localhost') && !str_contains($host, 'localhost') && !str_contains($host, '127.0.0.1'))) {
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
            $scriptDir = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
            $detectedUrl = $scheme . '://' . $host . $scriptDir;
            define('APP_URL', rtrim($detectedUrl, '/'));
        } else {
            define('APP_URL', rtrim($configuredUrl ?: 'http://localhost/XeonNotepad', '/'));
        }
        define('DB_HOST',        self::get('DB_HOST', 'localhost'));
        define('DB_NAME',        self::get('DB_NAME', 'xeon_notepad'));
        define('DB_USER',        self::get('DB_USER', 'root'));
        define('DB_PASS',        self::get('DB_PASS', ''));
        define('ENCRYPTION_KEY', self::get('ENCRYPTION_KEY', ''));
        define('GROQ_API_KEY',   self::get('GROQ_API_KEY', ''));
        define('GROQ_MODEL',     self::get('GROQ_MODEL', 'llama-3.3-70b-versatile'));
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$data[$key] ?? $default;
    }
}

Config::load();
