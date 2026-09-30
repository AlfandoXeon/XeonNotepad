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
        define('APP_URL',        rtrim(self::get('APP_URL', 'http://localhost/XeonNotepad'), '/'));
        define('APP_ENV',        self::get('APP_ENV', 'production'));
        define('DB_HOST',        self::get('DB_HOST', 'localhost'));
        define('DB_NAME',        self::get('DB_NAME', 'xeon_notepad'));
        define('DB_USER',        self::get('DB_USER', 'root'));
        define('DB_PASS',        self::get('DB_PASS', ''));
        define('ENCRYPTION_KEY', self::get('ENCRYPTION_KEY', ''));
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$data[$key] ?? $default;
    }
}

Config::load();
