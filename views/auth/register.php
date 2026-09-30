<div class="auth-card">

        <!-- Logo -->
        <div class="auth-logo">
            <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="<?= htmlspecialchars(APP_NAME) ?> Logo" width="48" height="48">
            <span class="auth-logo-text"><?= htmlspecialchars(APP_NAME) ?></span>
        </div>

        <!-- Heading -->
        <div class="auth-heading">
            <h1>Create account</h1>
            <p>Start taking notes securely, for free</p>
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

        <!-- Registration form -->
        <form class="auth-form" method="POST" action="<?= APP_URL ?>/register" id="registerForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

            <!-- Username -->
            <div class="auth-input-wrapper">
                <label class="form-label" for="username">Username</label>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    placeholder="your_username"
                    value="<?= $oldUsername ?? '' ?>"
                    required
                    minlength="3"
                    maxlength="50"
                    pattern="[a-zA-Z0-9_]+"
                    autocomplete="username"
                    title="Letters, numbers, and underscores only"
                >
            </div>

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
                    class="form-input"
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
                    class="form-input"
                    placeholder="At least 8 characters"
                    required
                    minlength="8"
                    autocomplete="new-password"
                >
                <button type="button" class="auth-pass-toggle" id="togglePassword" aria-label="Show or hide password" tabindex="0">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" aria-hidden="true">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <!-- Password strength indicator -->
                <div class="password-strength" id="strengthIndicator" style="display:none" aria-live="polite">
                    <div class="strength-bar" id="bar1"></div>
                    <div class="strength-bar" id="bar2"></div>
                    <div class="strength-bar" id="bar3"></div>
                    <div class="strength-bar" id="bar4"></div>
                    <span class="strength-text" id="strengthText"></span>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="auth-input-wrapper">
                <label class="form-label" for="password_confirm">Confirm password</label>
                <svg class="auth-input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    class="form-input"
                    placeholder="Repeat your password"
                    required
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn btn-primary auth-submit" id="registerBtn">
                Create Account
            </button>
        </form>

        <div class="auth-divider">
            Already have an account? <a href="<?= APP_URL ?>/login">Sign in</a>
        </div>
</div>
