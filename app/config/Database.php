<?php
declare(strict_types=1);

/**
 * Database — PDO singleton with secure connection options.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone()     {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_NAME
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('[Database] Connection failed: ' . $e->getMessage());
                if (APP_ENV === 'development') {
                    die('<h2>Database Error</h2><p>' . htmlspecialchars($e->getMessage()) . '</p>');
                }
                die('<h2>Service Unavailable</h2><p>Please try again later.</p>');
            }
        }

        return self::$instance;
    }
}
