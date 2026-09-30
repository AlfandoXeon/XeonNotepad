<?php
declare(strict_types=1);

/**
 * Controller — base class for all controllers.
 * Provides view rendering, JSON responses, redirects,
 * session helpers, CSRF generation/verification, and HTML sanitization.
 */
class Controller
{
    /**
     * Render an inner view captured into a layout.
     *
     * @param string $layout  Layout name (e.g. 'main', 'auth')
     * @param string $view    View path relative to /views/ (e.g. 'notes/index')
     * @param array  $data    Variables extracted into view and layout scope
     */
    protected function view(string $layout, string $view, array $data = []): void
    {
        // Make all data variables available in view and layout scope
        extract($data, EXTR_SKIP);

        // Capture the inner view's HTML output
        ob_start();
        $viewFile = VIEWS_PATH . '/' . $view . '.php';
        if (!file_exists($viewFile)) {
            ob_end_clean();
            die('View not found: ' . htmlspecialchars($view));
        }
        require $viewFile;
        // NOTE: variables set inside $viewFile (e.g. $includeEditor) persist here
        $pageContent = ob_get_clean();

        // Render the layout which wraps $pageContent
        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            die('Layout not found: ' . htmlspecialchars($layout));
        }
        require $layoutFile;
    }

    /**
     * Send a JSON response and halt execution.
     */
    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect to a path relative to APP_URL.
     */
    protected function redirect(string $path): never
    {
        header('Location: ' . APP_URL . $path);
        exit;
    }

    // ── Auth helpers ──────────────────────────────────────────────────────

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requireGuest(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
    }

    protected function currentUser(): array
    {
        return [
            'id'       => (int)($_SESSION['user_id']  ?? 0),
            'username' => (string)($_SESSION['username'] ?? ''),
            'email'    => (string)($_SESSION['email']    ?? ''),
        ];
    }

    // ── CSRF helpers ──────────────────────────────────────────────────────

    protected function generateCsrf(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        $valid = !empty($_SESSION['csrf_token'])
              && hash_equals($_SESSION['csrf_token'], $token);

        if (!$valid) {
            http_response_code(403);
            die('403 Forbidden — CSRF token mismatch.');
        }
    }

    // ── Input helpers ─────────────────────────────────────────────────────

    /**
     * Sanitize rich-text HTML (strip dangerous tags/attributes, keep formatting).
     */
    protected function sanitizeHtml(string $html): string
    {
        // Strip out any <script> and <style> blocks completely including contents
        $html = preg_replace('/<(script|style)\b[^>]*>(.*?)<\/\1>/is', '', $html);

        $allowed = '<b><i><u><s><strong><em><br><p><span><font><div>'
                 . '<ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><pre><code>'
                 . '<a><hr><table><thead><tbody><tr><th><td><img><del><input>';
        $html = strip_tags($html, $allowed);

        // Remove inline event handlers (onclick, onload, etc.)
        $html = preg_replace('/\s+on\w+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $html);

        // Remove javascript: in href/src/action
        $html = preg_replace('/\b(href|src|action)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $html);

        return $html;
    }

    protected function postString(string $key, string $default = ''): string
    {
        return trim(strip_tags($_POST[$key] ?? $default));
    }

    protected function getInt(string $key, int $default = 0): int
    {
        return (int)($_GET[$key] ?? $default);
    }
}
