<?php
declare(strict_types=1);

/**
 * Router — maps incoming HTTP method + URI to a controller action.
 * Handles static routes and dynamic routes with ID segments.
 */
class Router
{
    private string $basePath;

    public function __construct()
    {
        // e.g. "/XeonNotepad" — strip from request URI to get clean path
        $this->basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    }

    public function route(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = $this->resolveUri();

        // ── Auth ──────────────────────────────────────────────────────────
        if ($method === 'GET'  && $uri === '/login')    { (new AuthController())->loginForm();    return; }
        if ($method === 'POST' && $uri === '/login')    { (new AuthController())->login();        return; }
        if ($method === 'GET'  && $uri === '/register') { (new AuthController())->registerForm(); return; }
        if ($method === 'POST' && $uri === '/register') { (new AuthController())->register();     return; }
        if ($method === 'POST' && $uri === '/logout')   { (new AuthController())->logout();       return; }

        // ── Profile ───────────────────────────────────────────────────────
        if ($method === 'GET'  && $uri === '/profile')          { (new ProfileController())->index();          return; }
        if ($method === 'POST' && $uri === '/profile/password') { (new ProfileController())->changePassword(); return; }

        // ── Notes — static ────────────────────────────────────────────────
        if ($method === 'GET'  && $uri === '/')              { (new NoteController())->index();   return; }
        if ($method === 'GET'  && $uri === '/note/new')      { (new NoteController())->create();  return; }
        if ($method === 'POST' && $uri === '/note/save')     { (new NoteController())->save();    return; }
        if ($method === 'GET'  && $uri === '/note/search')   { (new NoteController())->search();  return; }
        if ($method === 'GET'  && $uri === '/note/download') { (new NoteController())->download();return; }
        if ($method === 'POST' && $uri === '/note/delete')   { (new NoteController())->delete();  return; }

        // ── Notes — dynamic: /note/{id} ───────────────────────────────────
        if ($method === 'GET' && preg_match('#^/note/(\d+)$#', $uri, $m)) {
            (new NoteController())->edit((int)$m[1]);
            return;
        }

        // ── History — dynamic: /note/history/{id} ─────────────────────────
        if ($method === 'GET' && preg_match('#^/note/history/(\d+)$#', $uri, $m)) {
            (new HistoryController())->index((int)$m[1]);
            return;
        }

        // ── 404 ───────────────────────────────────────────────────────────
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
              <title>404 — ' . APP_NAME . '</title>
              <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;
              min-height:100vh;margin:0;background:#fff;flex-direction:column;gap:16px;color:#1a1a1a}
              h1{font-size:72px;color:#FF8000;margin:0;font-weight:800}
              p{color:#888;margin:0}a{color:#FF8000;font-weight:600}</style></head>
              <body><h1>404</h1><p>Page not found.</p>
              <a href="' . APP_URL . '/">Go to Dashboard</a></body></html>';
    }

    private function resolveUri(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = rawurldecode($path ?? '/');

        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }

        $path = '/' . ltrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
