<header class="topbar" role="banner">

    <!-- Brand logo + name -->
    <a href="<?= APP_URL ?>/" class="topbar-brand" aria-label="<?= APP_NAME ?> — Go to dashboard">
        <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="" width="32" height="32" aria-hidden="true">
        <span class="topbar-brand-name"><?= htmlspecialchars(APP_NAME) ?></span>
    </a>

    <?php if (empty($hideSidebar)): ?>
    <!-- Sidebar toggle button (Microsoft style) -->
    <button class="btn-icon sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle sidebar panel (Ctrl+\)" aria-label="Toggle sidebar panel" aria-expanded="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="9" y1="3" x2="9" y2="21"></line>
        </svg>
    </button>
    <?php endif; ?>

    <!-- Live search bar -->
    <div class="topbar-search" id="searchWrapper" role="search">
        <svg class="topbar-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input
            type="text"
            id="searchInput"
            placeholder="Search notes…"
            autocomplete="off"
            spellcheck="false"
            aria-label="Search notes"
            aria-autocomplete="list"
            aria-controls="searchDropdown"
        >
        <div class="search-dropdown" id="searchDropdown" role="listbox" aria-label="Search results"></div>
    </div>

    <?php if (!empty($includeEditor)): ?>
    <!-- Auto-save indicator (visible only on editor page) -->
    <div class="save-indicator" id="saveIndicator" aria-live="polite" aria-atomic="true">
        <span class="save-dot" aria-hidden="true"></span>
        <span class="save-text"></span>
    </div>
    <?php endif; ?>

    <!-- Right-side actions -->
    <div class="topbar-actions">

        <!-- Dark mode toggle -->
        <button class="btn-icon" id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark mode">
            <!-- Sun icon (shown in dark mode) -->
            <svg id="themeIconSun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none" aria-hidden="true">
                <circle cx="12" cy="12" r="5"></circle>
                <line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                <line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
            </svg>
            <!-- Moon icon (shown in light mode) -->
            <svg id="themeIconMoon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </button>

        <!-- User chip (links to profile) -->
        <a href="<?= APP_URL ?>/profile" class="user-chip" id="userChipBtn" aria-label="Your profile: <?= htmlspecialchars($currentUser['username'] ?? '') ?>" title="View your profile">
            <div class="user-avatar" aria-hidden="true">
                <?= strtoupper(mb_substr($currentUser['username'] ?? 'U', 0, 1)) ?>
            </div>
            <span class="user-name"><?= htmlspecialchars($currentUser['username'] ?? '') ?></span>
        </a>

        <!-- Logout -->
        <button class="btn-icon" id="logoutBtn" aria-label="Sign out" title="Sign out">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </button>

    </div>
</header>
