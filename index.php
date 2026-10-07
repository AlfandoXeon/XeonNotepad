<?php
declare(strict_types=1);

/**
 * Xeon Notepad — Front Controller
 * All HTTP requests are routed through this file via .htaccess
 */

define('ROOT_PATH',  __DIR__);
define('APP_PATH',   ROOT_PATH . '/app');
define('VIEWS_PATH', ROOT_PATH . '/views');

// ── Load core configs (order matters) ──────────────────────────────────────
require_once APP_PATH . '/config/Config.php';     // defines constants from .env

// ── Error reporting (shows detailed error if APP_ENV=development) ──────────
if (defined('APP_ENV') && APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

require_once APP_PATH . '/config/Database.php';    // PDO singleton
require_once APP_PATH . '/config/Encryption.php';  // AES-256-GCM helpers

// ── Load MVC core ──────────────────────────────────────────────────────────
require_once APP_PATH . '/core/Model.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// ── Load models ────────────────────────────────────────────────────────────
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Note.php';
require_once APP_PATH . '/models/NoteHistory.php';

// ── Autoloader fallback ────────────────────────────────────────────────────
spl_autoload_register(function (string $class): void {
    $directories = [
        APP_PATH . '/core/',
        APP_PATH . '/models/',
        APP_PATH . '/services/',
        APP_PATH . '/controllers/',
        APP_PATH . '/config/',
    ];
    foreach ($directories as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Load services ──────────────────────────────────────────────────────────
if (file_exists(APP_PATH . '/services/GroqService.php')) {
    require_once APP_PATH . '/services/GroqService.php';
}

// ── Load controllers ───────────────────────────────────────────────────────
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/NoteController.php';
require_once APP_PATH . '/controllers/HistoryController.php';
require_once APP_PATH . '/controllers/ProfileController.php';
if (file_exists(APP_PATH . '/controllers/AiController.php')) {
    require_once APP_PATH . '/controllers/AiController.php';
}
if (file_exists(APP_PATH . '/controllers/LandingController.php')) {
    require_once APP_PATH . '/controllers/LandingController.php';
}


// ── Security headers ───────────────────────────────────────────────────────
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header("Content-Security-Policy: default-src 'self' https: data: blob:; "
    . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https:; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https:; "
    . "font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com data: https:; "
    . "img-src 'self' data: https: blob:; "
    . "connect-src 'self' https:;");


// ── Session configuration ──────────────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Dispatch ───────────────────────────────────────────────────────────────
(new Router())->route();
