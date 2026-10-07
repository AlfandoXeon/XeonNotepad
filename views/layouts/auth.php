<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <meta name="description" content="Xeon Notepad — Your secure, encrypted online notepad. Access your notes from anywhere.">
    <meta name="csrf-token" content="<?= $csrfToken ?? '' ?>">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/logo/NotepadIcon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0&display=swap">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/auth.css">
</head>
<body class="auth-body">

<!-- ── Navbar ──────────────────────────────────────────────────── -->
<nav class="auth-navbar" role="navigation" aria-label="Main navigation">
    <div class="auth-navbar-inner">
        <a href="<?= APP_URL ?>/" class="auth-navbar-brand" aria-label="<?= APP_NAME ?> Home">
            <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="" width="28" height="28" aria-hidden="true">
            <span><?= htmlspecialchars(APP_NAME) ?></span>
        </a>
        <div class="auth-navbar-links">
            <a href="<?= APP_URL ?>/login"
               class="auth-nav-link <?= str_contains($pageTitle ?? '', 'Sign In') ? 'is-active' : '' ?>">
                Sign In
            </a>
            <a href="<?= APP_URL ?>/register" class="btn btn-primary btn-sm">
                Get Started
            </a>
        </div>
    </div>
</nav>

<!-- ── Split Shell ──────────────────────────────────────────────── -->
<div class="auth-shell">

    <!-- Left: Branding / Promo panel -->
    <aside class="auth-promo" aria-hidden="true">
        <div class="auth-promo-inner">
            <img src="<?= APP_URL ?>/logo/NotepadIcon.png"
                 alt="" class="auth-promo-logo" aria-hidden="true">

            <h2 class="auth-promo-title">Your notes,<br>always safe.</h2>
            <p class="auth-promo-subtitle">
                A secure, encrypted online notepad you can access<br>
                from campus, home, or anywhere in between.
            </p>

            <ul class="auth-feature-list" role="list">
                <li>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    End-to-end encrypted notes
                </li>
                <li>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Access from any device
                </li>
                <li>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Rich text formatting
                </li>
                <li>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Auto-saved as you type
                </li>
                <li>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Full change history log
                </li>
            </ul>
        </div>

        <!-- Decorative floating circles -->
        <div class="promo-circle promo-circle-1" aria-hidden="true"></div>
        <div class="promo-circle promo-circle-2" aria-hidden="true"></div>
        <div class="promo-circle promo-circle-3" aria-hidden="true"></div>
    </aside>

    <!-- Right: Form panel -->
    <div class="auth-form-panel">
        <?= $pageContent ?>
    </div>

</div><!-- /.auth-shell -->

<!-- ── Footer ──────────────────────────────────────────────────── -->
<footer class="auth-footer" role="contentinfo">
    <div class="auth-footer-inner">
        <span>© <?= date('Y') ?> <?= htmlspecialchars(APP_NAME) ?>. All rights reserved.</span>
        <span class="auth-footer-dot" aria-hidden="true">·</span>
        <span>Your notes are always encrypted and private.</span>
    </div>
</footer>

<div id="toast-container"></div>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
</body>
</html>
