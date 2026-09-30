<div class="profile-page">

    <!-- ── Profile header ───────────────────────────────────────── -->
    <div class="profile-header">
        <div class="profile-avatar-large" aria-hidden="true">
            <?= strtoupper(mb_substr($userData['username'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="profile-header-info">
            <h1 class="profile-username"><?= htmlspecialchars($userData['username'] ?? '') ?></h1>
            <p class="profile-email"><?= htmlspecialchars($userData['email'] ?? '') ?></p>
            <p class="profile-joined">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8"  y1="2" x2="8"  y2="6"></line>
                    <line x1="3"  y1="10" x2="21" y2="10"></line>
                </svg>
                Member since <?= date('F j, Y', strtotime($userData['created_at'] ?? 'now')) ?>
            </p>
        </div>
    </div>

    <!-- ── Stats row ─────────────────────────────────────────────── -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number"><?= (int)$noteCount ?></div>
            <div class="stat-label">Total Notes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="font-size: 22px; color: var(--success);">Active</div>
            <div class="stat-label">Account Status</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php
                    if (!empty($userData['created_at'])) {
                        $diff = (new DateTime())->diff(new DateTime($userData['created_at']));
                        echo $diff->days;
                    } else {
                        echo '0';
                    }
                ?>
            </div>
            <div class="stat-label">Days Active</div>
        </div>
    </div>

    <!-- ── Two-column forms ───────────────────────────────────────── -->
    <div class="profile-grid">

        <!-- Account info card (read-only) -->
        <section class="profile-card" aria-labelledby="accountInfoTitle">
            <div class="profile-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <h2 id="accountInfoTitle">Account Information</h2>
            </div>
            <div class="profile-card-body">
                <div class="info-row">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= htmlspecialchars($userData['username'] ?? '') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email address</div>
                    <div class="info-value"><?= htmlspecialchars($userData['email'] ?? '') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Account created</div>
                    <div class="info-value"><?= date('F j, Y \a\t g:i A', strtotime($userData['created_at'] ?? 'now')) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge-status-active">
                            <span class="status-dot" aria-hidden="true"></span>
                            Active
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Change password card -->
        <section class="profile-card" aria-labelledby="changePasswordTitle">
            <div class="profile-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <h2 id="changePasswordTitle">Change Password</h2>
            </div>
            <div class="profile-card-body">

                <?php if (!empty($passwordErrors)): ?>
                <div class="alert alert-error" role="alert">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div><?= implode('<br>', array_map('htmlspecialchars', $passwordErrors)) ?></div>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?= APP_URL ?>/profile/password" id="changePasswordForm" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <!-- Current password -->
                    <div class="form-group">
                        <label class="form-label" for="current_password">Current password</label>
                        <div class="pass-input-wrapper">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-input"
                                   placeholder="Your current password"
                                   required autocomplete="current-password">
                            <button type="button" class="pass-toggle-btn" data-target="current_password" aria-label="Toggle current password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- New password -->
                    <div class="form-group">
                        <label class="form-label" for="new_password">New password</label>
                        <div class="pass-input-wrapper">
                            <input type="password" id="new_password" name="new_password"
                                   class="form-input"
                                   placeholder="At least 8 characters"
                                   required minlength="8" autocomplete="new-password">
                            <button type="button" class="pass-toggle-btn" data-target="new_password" aria-label="Toggle new password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm new password -->
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm new password</label>
                        <div class="pass-input-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password"
                                   class="form-input"
                                   placeholder="Repeat new password"
                                   required autocomplete="new-password">
                            <button type="button" class="pass-toggle-btn" data-target="confirm_password" aria-label="Toggle confirm password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px">
                        Update Password
                    </button>
                </form>
            </div>
        </section>

    </div><!-- /.profile-grid -->

</div><!-- /.profile-page -->

<script>
// Password toggle buttons on profile page
document.querySelectorAll('.pass-toggle-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var targetId = btn.dataset.target;
        var input = document.getElementById(targetId);
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.style.opacity = input.type === 'text' ? '0.5' : '1';
    });
});
</script>
