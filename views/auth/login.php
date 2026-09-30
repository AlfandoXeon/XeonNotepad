<div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="<?= htmlspecialchars(APP_NAME) ?> Logo" width="48" height="48">
            <span class="auth-logo-text"><?= htmlspecialchars(APP_NAME) ?></span>
        </div>

        <!-- Heading -->
        <div class="auth-heading">
            <h1>Welcome back</h1>
            <p>Sign in to access your notes</p>
        </div>

        <!-- Error alert -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
        </div>
        <?php endif; ?>

        <!-- Login form -->
        <form class="auth-form" method="POST" action="<?= APP_URL ?>/login" id="loginForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Email -->
            <div class="auth-input-wrapper">
                <label class="form-label" for="email">Email address</label>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input <?= !empty($errors) ? 'error' : '' ?>"
                    placeholder="you@example.com"
                    value="<?= $oldEmail ?? '' ?>"
                    required
                    autocomplete="email"
                >
            </div>

            <!-- Password -->
            <div class="auth-input-wrapper">
                <label class="form-label" for="password">Password</label>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input <?= !empty($errors) ? 'error' : '' ?>"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="auth-pass-toggle" id="togglePassword" aria-label="Show or hide password" tabindex="0">
                    <svg id="eyeOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>

            <button type="submit" class="btn btn-primary auth-submit" id="loginBtn">
                Sign in
            </button>
        </form>

        <div class="auth-divider">
            Don't have an account? <a href="<?= APP_URL ?>/register">Create one for free</a>
        </div>
</div>

