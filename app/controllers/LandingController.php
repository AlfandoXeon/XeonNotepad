<?php
declare(strict_types=1);

/**
 * LandingController — Serves the public landing page for Xeon Notepad.
 */
class LandingController extends Controller
{
    public function index(): void
    {
        $user = $this->isLoggedIn() ? $this->currentUser() : null;

        // Render landing view directly
        $viewFile = VIEWS_PATH . '/landing/index.php';
        if (!file_exists($viewFile)) {
            die('Landing view not found.');
        }

        $data = [
            'pageTitle'   => APP_NAME . ' — Private, Encrypted & AI Powered Notepad',
            'currentUser' => $user,
            'isLoggedIn'  => $this->isLoggedIn(),
        ];
        extract($data, EXTR_SKIP);
        require $viewFile;
    }
}
