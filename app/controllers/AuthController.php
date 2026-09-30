<?php
declare(strict_types=1);

/**
 * AuthController — handles Login, Register, and Logout.
 * Includes brute-force rate limiting (session-based) on login.
 */
class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS    = 900; // 15 minutes

    // ── Login ─────────────────────────────────────────────────────────────

    public function loginForm(): void
    {
        $this->requireGuest();
        $this->view('auth', 'auth/login', [
            'pageTitle' => 'Sign In — ' . APP_NAME,
            'csrfToken' => $this->generateCsrf(),
        ]);
    }

    public function login(): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        // Rate limiting
        $attempts    = (int)($_SESSION['login_attempts']     ?? 0);
        $lastAttempt = (int)($_SESSION['last_login_attempt'] ?? 0);

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS
            && (time() - $lastAttempt) < self::LOCKOUT_SECONDS
        ) {
            $remaining = self::LOCKOUT_SECONDS - (time() - $lastAttempt);
            $errors[]  = 'Too many failed attempts. Please wait ' . ceil($remaining / 60) . ' minute(s) before trying again.';
        } else {
            // Validate inputs
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address.';
            }
            if (empty($password)) {
                $errors[] = 'Password is required.';
            }
        }

        if (empty($errors)) {
            $userModel = new User();
            $user      = $userModel->findByEmail($email);

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                // ── Login success ──
                unset($_SESSION['login_attempts'], $_SESSION['last_login_attempt']);
                session_regenerate_id(true);

                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];

                $this->redirect('/');
            } else {
                // ── Login failure — increment counter ──
                $_SESSION['login_attempts']     = min($attempts + 1, self::MAX_LOGIN_ATTEMPTS);
                $_SESSION['last_login_attempt'] = time();
                $errors[]                       = 'Invalid email or password.';
            }
        }

        $this->view('auth', 'auth/login', [
            'pageTitle' => 'Sign In — ' . APP_NAME,
            'csrfToken' => $this->generateCsrf(),
            'errors'    => $errors,
            'oldEmail'  => htmlspecialchars($email),
        ]);
    }

    // ── Register ──────────────────────────────────────────────────────────

    public function registerForm(): void
    {
        $this->requireGuest();
        $this->view('auth', 'auth/register', [
            'pageTitle' => 'Create Account — ' . APP_NAME,
            'csrfToken' => $this->generateCsrf(),
        ]);
    }

    public function register(): void
    {
        $this->requireGuest();
        $this->verifyCsrf();

        $username  = trim($_POST['username'] ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password_confirm'] ?? '';
        $errors    = [];

        // Validate username
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            $errors[] = 'Username must be 3–50 characters: letters, numbers, and underscores only.';
        }

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        // Validate password
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if ($password !== $password2) {
            $errors[] = 'Passwords do not match.';
        }

        if (empty($errors)) {
            $userModel = new User();
            if ($userModel->usernameExists($username)) {
                $errors[] = 'This username is already taken.';
            }
            if ($userModel->emailExists($email)) {
                $errors[] = 'An account with this email already exists.';
            }

            if (empty($errors)) {
                $newId = $userModel->create($username, $email, $password);
                if ($newId > 0) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']  = $newId;
                    $_SESSION['username'] = $username;
                    $_SESSION['email']    = $email;
                    $this->redirect('/');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
        }

        $this->view('auth', 'auth/register', [
            'pageTitle'   => 'Create Account — ' . APP_NAME,
            'csrfToken'   => $this->generateCsrf(),
            'errors'      => $errors,
            'oldUsername' => htmlspecialchars($username),
            'oldEmail'    => htmlspecialchars($email),
        ]);
    }

    // ── Logout ────────────────────────────────────────────────────────────

    public function logout(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 86400,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
        $this->redirect('/login');
    }
}
