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

// ── Load controllers ───────────────────────────────────────────────────────
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/NoteController.php';
require_once APP_PATH . '/controllers/HistoryController.php';
require_once APP_PATH . '/controllers/ProfileController.php';


// ── Security headers ───────────────────────────────────────────────────────
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data:;");

// ── Session configuration ──────────────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false, // Set to true when using HTTPS
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

// ── Dispatch ───────────────────────────────────────────────────────────────
(new Router())->route();
