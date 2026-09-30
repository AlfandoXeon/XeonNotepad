/* ================================================================
   Xeon Notepad — app.js
   Global utilities: theme toggle, toast system, logout,
   password toggle/strength, delete modal
   ================================================================ */

(function () {
    'use strict';

    /* ── Theme ────────────────────────────────────────────────────── */

    const THEME_KEY = 'xeon-theme';

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        const sun  = document.getElementById('themeIconSun');
        const moon = document.getElementById('themeIconMoon');
        if (sun && moon) {
            sun.style.display  = theme === 'dark' ? 'block' : 'none';
            moon.style.display = theme === 'dark' ? 'none'  : 'block';
        }
    }

    function initTheme() {
        const saved = localStorage.getItem(THEME_KEY) || 'light';
        applyTheme(saved);
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        const next    = current === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        localStorage.setItem(THEME_KEY, next);
    }

    /* ── Toast System ─────────────────────────────────────────────── */

    const ICONS = {
        success: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`,
        error:   `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
        info:    `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
        warning: `<svg class="toast-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
    };

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    /**
     * Show a toast notification.
     * @param {string} title
     * @param {string} message  (optional)
     * @param {'success'|'error'|'info'|'warning'} type
     * @param {number}  duration  ms (0 = sticky)
     */
    window.xeonToast = function (title, message, type = 'info', duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            ${ICONS[type] || ICONS.info}
            <div class="toast-body">
                <div class="toast-title">${escHtml(title)}</div>
                ${message ? `<div class="toast-message">${escHtml(message)}</div>` : ''}
            </div>
            <button class="toast-close" aria-label="Close notification">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        const dismiss = () => {
            toast.classList.add('hiding');
            setTimeout(() => toast.remove(), 210);
        };

        toast.querySelector('.toast-close').addEventListener('click', dismiss);
        container.appendChild(toast);

        if (duration > 0) setTimeout(dismiss, duration);
    };

    /* ── Password Toggle ──────────────────────────────────────────── */

    function initPasswordToggles() {
        document.querySelectorAll('#togglePassword').forEach(btn => {
            const wrapper = btn.closest('.auth-input-wrapper');
            const input   = wrapper?.querySelector('input[type="password"], input[type="text"]');
            if (!input) return;

            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.style.opacity = show ? '0.5' : '1';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });

            btn.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); btn.click(); }
            });
        });
    }

    /* ── Password Strength ────────────────────────────────────────── */

    function initPasswordStrength() {
        const input     = document.getElementById('password');
        const indicator = document.getElementById('strengthIndicator');
        const textEl    = document.getElementById('strengthText');
        const barIds    = ['bar1', 'bar2', 'bar3', 'bar4'];
        const bars      = barIds.map(id => document.getElementById(id)).filter(Boolean);

        if (!input || !indicator || bars.length === 0) return;

        input.addEventListener('input', () => {
            const val = input.value;
            if (!val) { indicator.style.display = 'none'; return; }
            indicator.style.display = 'flex';

            // Score 0–5
            let score = 0;
            if (val.length >= 8)  score++;
            if (val.length >= 12) score++;
            if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^a-zA-Z0-9]/.test(val)) score++;

            const level  = score < 2 ? 'weak' : score < 4 ? 'medium' : 'strong';
            const labels = { weak: 'Weak', medium: 'Fair', strong: 'Strong' };
            const colors = { weak: 'var(--error)', medium: 'var(--warning)', strong: 'var(--success)' };

            bars.forEach((bar, i) => {
                bar.className = 'strength-bar' + (i < score ? ' ' + level : '');
            });
            if (textEl) {
                textEl.textContent = labels[level];
                textEl.style.color = colors[level];
            }
        });
    }

    /* ── Logout ───────────────────────────────────────────────────── */

    function initLogout() {
        const logoutBtn  = document.getElementById('logoutBtn');
        const logoutForm = document.getElementById('logoutForm');
        if (logoutBtn && logoutForm) {
            logoutBtn.addEventListener('click', () => logoutForm.submit());
        }
    }

    /* ── Delete Modal ─────────────────────────────────────────────── */

    function initDeleteModal() {
        const overlay    = document.getElementById('deleteModal');
        const cancelBtn  = document.getElementById('cancelDelete');
        const confirmBtn = document.getElementById('confirmDelete');
        const deleteNoteBtn = document.getElementById('deleteNoteBtn');

        if (!overlay) return;

        let pendingNoteId = null;

        /** Call this to open the delete confirmation modal */
        window.showDeleteModal = function (noteId) {
            pendingNoteId = noteId;
            overlay.classList.add('active');
        };

        // Open from editor's delete button
        if (deleteNoteBtn) {
            deleteNoteBtn.addEventListener('click', () => {
                showDeleteModal(deleteNoteBtn.dataset.noteId);
            });
        }

        // Close handlers
        cancelBtn?.addEventListener('click',   () => overlay.classList.remove('active'));
        overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('active'); });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                overlay.classList.remove('active');
            }
        });

        // Confirm delete
        confirmBtn?.addEventListener('click', async () => {
            if (!pendingNoteId) return;

            confirmBtn.disabled    = true;
            confirmBtn.textContent = 'Deleting…';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const formData  = new FormData();
                formData.append('note_id',    pendingNoteId);
                formData.append('csrf_token', csrfToken);

                const appUrl = document.querySelector('meta[name="app-url"]')?.content || '';
                const res    = await fetch(appUrl + '/note/delete', {
                    method:  'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body:    formData,
                });
                const data = await res.json();

                if (data.success) {
                    xeonToast('Note deleted', 'Your note has been removed.', 'success');
                    setTimeout(() => { window.location.href = appUrl + '/'; }, 900);
                } else {
                    xeonToast('Error', data.message || 'Could not delete note.', 'error');
                }
            } catch {
                xeonToast('Error', 'Network error. Please try again.', 'error');
            } finally {
                confirmBtn.disabled    = false;
                confirmBtn.textContent = 'Delete Note';
                overlay.classList.remove('active');
            }
        });
    }

    /* ── Sidebar Resizer (Microsoft Splitter Style) ─────────────── */

    function initSidebarResizer() {
        const resizer   = document.getElementById('sidebarResizer');
        const app       = document.getElementById('app');
        const toggleBtn = document.getElementById('sidebarToggleBtn');

        if (!app) return;

        const STORAGE_KEY_W         = 'xeon_sidebar_w';
        const STORAGE_KEY_COLLAPSED = 'xeon_sidebar_collapsed';
        const DEFAULT_W             = 260;
        const MIN_W                 = 180;

        const getMaxW = () => Math.min(650, Math.floor(window.innerWidth * 0.55));

        // Restore saved width
        const savedW = localStorage.getItem(STORAGE_KEY_W);
        if (savedW) {
            const parsed = parseInt(savedW, 10);
            if (!isNaN(parsed) && parsed >= MIN_W && parsed <= getMaxW()) {
                app.style.setProperty('--sidebar-w', parsed + 'px');
            }
        }

        // Restore collapsed state (on mobile devices, collapse by default unless user opened it)
        const storedCollapsed = localStorage.getItem(STORAGE_KEY_COLLAPSED);
        const isMobileScreen  = window.innerWidth <= 768;
        if (storedCollapsed === 'true' || (storedCollapsed === null && isMobileScreen)) {
            app.classList.add('sidebar-collapsed');
            if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
        }

        // Sidebar toggle button
        if (toggleBtn) {
            toggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isCollapsed = app.classList.toggle('sidebar-collapsed');
                toggleBtn.setAttribute('aria-expanded', String(!isCollapsed));
                localStorage.setItem(STORAGE_KEY_COLLAPSED, String(isCollapsed));
            });
        }

        // On mobile, tap outside sidebar to close it
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && !app.classList.contains('sidebar-collapsed')) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.contains(e.target) && !toggleBtn?.contains(e.target)) {
                    app.classList.add('sidebar-collapsed');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                    localStorage.setItem(STORAGE_KEY_COLLAPSED, 'true');
                }
            }
        });

        // Keyboard shortcut: Ctrl+\ / Cmd+\ to toggle sidebar
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === '\\') {
                e.preventDefault();
                if (toggleBtn) toggleBtn.click();
            }
        });

        if (!resizer) return;

        let isDragging = false;
        let startX     = 0;
        let startW     = DEFAULT_W;

        function onPointerDown(e) {
            if (e.button !== 0) return;
            isDragging = true;
            startX     = e.clientX;
            startW     = parseInt(getComputedStyle(app).getPropertyValue('--sidebar-w'), 10) || DEFAULT_W;

            try { resizer.setPointerCapture(e.pointerId); } catch (_) {}
            document.body.classList.add('is-resizing');
            e.preventDefault();
        }

        function onPointerMove(e) {
            if (!isDragging) return;
            const delta = e.clientX - startX;
            let newW    = startW + delta;

            if (newW < MIN_W) newW = MIN_W;
            const maxW = getMaxW();
            if (newW > maxW)  newW = maxW;

            app.style.setProperty('--sidebar-w', newW + 'px');
        }

        function onPointerUp(e) {
            if (!isDragging) return;
            isDragging = false;
            document.body.classList.remove('is-resizing');
            try { resizer.releasePointerCapture(e.pointerId); } catch (_) {}

            const finalW = parseInt(getComputedStyle(app).getPropertyValue('--sidebar-w'), 10);
            if (!isNaN(finalW)) {
                localStorage.setItem(STORAGE_KEY_W, String(finalW));
            }
        }

        resizer.addEventListener('pointerdown', onPointerDown);
        window.addEventListener('pointermove', onPointerMove);
        window.addEventListener('pointerup', onPointerUp);
        window.addEventListener('pointercancel', onPointerUp);

        // Double-click to reset width to default 260px
        resizer.addEventListener('dblclick', () => {
            app.style.setProperty('--sidebar-w', DEFAULT_W + 'px');
            localStorage.setItem(STORAGE_KEY_W, String(DEFAULT_W));
            if (typeof window.xeonToast === 'function') {
                window.xeonToast('Sidebar reset', 'Sidebar width reset to default (260px)', 'info', 2000);
            }
        });
    }

    /* ── Init ─────────────────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', () => {
        initTheme();

        const themeToggle = document.getElementById('themeToggle');
        themeToggle?.addEventListener('click', toggleTheme);

        initPasswordToggles();
        initPasswordStrength();
        initLogout();
        initDeleteModal();
        initSidebarResizer();
    });

})();
