<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <meta name="description" content="Xeon Notepad — Your secure online notepad.">
    <meta name="csrf-token"  content="<?= $csrfToken ?? '' ?>">
    <meta name="app-url"     content="<?= APP_URL ?>">
    <link rel="icon" type="image/png" href="<?= APP_URL ?>/logo/NotepadIcon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/editor.css">
</head>
<body>

<div class="app-container <?= !empty($hideSidebar) ? 'no-sidebar' : '' ?>" id="app">

    <?php include VIEWS_PATH . '/partials/topbar.php'; ?>

    <?php if (empty($hideSidebar)): ?>
        <?php include VIEWS_PATH . '/partials/sidebar.php'; ?>
        <div class="sidebar-resizer" id="sidebarResizer" role="separator" aria-orientation="vertical" tabindex="0" title="Drag to adjust sidebar width, double-click to reset (Ctrl+\)">
            <div class="resizer-handle" aria-hidden="true"></div>
        </div>
    <?php endif; ?>

    <main class="main-content" id="main-content">
        <?= $pageContent ?>
    </main>

</div><!-- /.app-container -->

<!-- Toast container -->
<div id="toast-container"></div>

<!-- Delete confirm modal -->
<div class="modal-overlay" id="deleteModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="modal">
        <div class="modal-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
            </svg>
        </div>
        <h3 id="deleteModalTitle">Delete this note?</h3>
        <p>This action cannot be undone. The note will be permanently removed from your account.</p>
        <div class="modal-actions">
            <button class="btn btn-ghost" id="cancelDelete">Cancel</button>
            <button class="btn btn-danger" id="confirmDelete">Delete Note</button>
        </div>
    </div>
</div>

<!-- Hidden logout form (submitted by JS) -->
<form id="logoutForm" method="POST" action="<?= APP_URL ?>/logout" style="display:none">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
</form>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="<?= APP_URL ?>/public/js/app.js"></script>
<?php if (!empty($includeEditor)): ?>
<script src="<?= APP_URL ?>/public/js/marked.min.js"></script>
<script src="<?= APP_URL ?>/public/js/turndown.js"></script>
<script src="<?= APP_URL ?>/public/js/editor.js"></script>
<?php endif; ?>
<script src="<?= APP_URL ?>/public/js/search.js"></script>

<?php if (!empty($_SESSION['flash'])):
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof xeonToast === 'function') {
        xeonToast(
            <?= json_encode($flash['title']   ?? 'Notice') ?>,
            <?= json_encode($flash['message'] ?? '') ?>,
            <?= json_encode($flash['type']    ?? 'info') ?>
        );
    }
});
</script>
<?php endif; ?>

</body>
</html>
