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

    /* ── Xeon Preferences Helper ─────────────────────────────────── */

    window.getXeonPreferences = function() {
        var defaults = {
            editorMode:    'rich',
            mdView:        'split',
            fontFamily:    'misans',
            fontSize:      '16',
            autoSaveSpeed: '350',
            aiTone:        'balanced',
            customGroqKey: ''
        };
        try {
            var saved = JSON.parse(localStorage.getItem('xeon-preferences') || '{}');
            return Object.assign(defaults, saved);
        } catch (e) {
            return defaults;
        }
    };

    /* ── Reusable AI Typewriter Engine ────────────────────────────── */

    window.runAiTypewriter = function(element, fullText, options) {
        options = options || {};
        var speed = options.speed || 10;
        var chunkSize = options.chunkSize || (fullText.length > 1400 ? 5 : (fullText.length > 600 ? 3 : 1));
        var onComplete = options.onComplete || function() {};

        var index = 0;
        var timer = null;
        var isDone = false;

        element.innerHTML = '';
        var textNode = document.createTextNode('');
        var caret = document.createElement('span');
        caret.className = 'typewriter-caret';
        caret.setAttribute('aria-hidden', 'true');
        element.appendChild(textNode);
        element.appendChild(caret);

        function finish() {
            if (isDone) return;
            isDone = true;
            if (timer) clearTimeout(timer);
            textNode.nodeValue = fullText;
            if (caret.parentNode) caret.remove();
            element.scrollTop = element.scrollHeight;
            onComplete();
        }

        function step() {
            if (isDone) return;
            index += chunkSize;
            if (index >= fullText.length) {
                finish();
                return;
            }

            textNode.nodeValue = fullText.substring(0, index);
            element.scrollTop = element.scrollHeight;

            var lastChar = fullText.charAt(index - 1);
            var delay = speed;
            if (lastChar === '\n') delay = speed * 2.2;
            else if (lastChar === '.' || lastChar === '!' || lastChar === '?') delay = speed * 1.8;

            timer = setTimeout(step, delay);
        }

        step();

        return {
            skip: finish,
            cancel: function() {
                isDone = true;
                if (timer) clearTimeout(timer);
            }
        };
    };

    /* ── Home Page AI Animated Typing Placeholder ─────────────────── */

    function initHomeAiTypingPlaceholder() {
        var input = document.getElementById('homeAiQuickInput');
        if (!input) return;

        var prompts = [
            'Study summary: Operating Systems & Virtual Memory...',
            'Meeting minutes: Sprint retrospective and key action items...',
            'Project architecture: AES-256-GCM encrypted cloud notepad...',
            'Action checklist: Launch prep & security vulnerability audit...',
            'Technical documentation: REST endpoints and CSRF tokens...',
            'Brainstorming: Future concepts of private local-first AI...'
        ];

        var pIndex = 0;
        var cIndex = 0;
        var isDeleting = false;
        var isPaused = false;
        var timer = null;

        input.addEventListener('focus', function() {
            isPaused = true;
            if (timer) clearTimeout(timer);
        });

        input.addEventListener('blur', function() {
            if (!input.value.trim()) {
                isPaused = false;
                timer = setTimeout(loop, 400);
            }
        });

        function loop() {
            if (isPaused || input.value.trim().length > 0) return;

            var current = prompts[pIndex];
            if (isDeleting) {
                cIndex--;
                input.setAttribute('placeholder', current.substring(0, cIndex));
                if (cIndex === 0) {
                    isDeleting = false;
                    pIndex = (pIndex + 1) % prompts.length;
                    timer = setTimeout(loop, 400);
                    return;
                }
                timer = setTimeout(loop, 22);
            } else {
                cIndex++;
                input.setAttribute('placeholder', current.substring(0, cIndex));
                if (cIndex === current.length) {
                    isDeleting = true;
                    timer = setTimeout(loop, 2400);
                    return;
                }
                timer = setTimeout(loop, 40);
            }
        }

        timer = setTimeout(loop, 1200);
    }

    /* ── Home Xeon AI Generator Modal ─────────────────────────────── */

    function initHomeAi() {
        var modal           = document.getElementById('homeAiModal');
        var triggerBtn      = document.getElementById('homeAiModalTriggerBtn');
        var quickInput      = document.getElementById('homeAiQuickInput');
        var quickSubmitBtn  = document.getElementById('homeAiQuickSubmitBtn');
        var closeBtn        = document.getElementById('closeHomeAiModal');
        var cancelBtn       = document.getElementById('cancelHomeAiModal');
        var templateSelect  = document.getElementById('homeAiTemplateSelect');
        var promptArea      = document.getElementById('homeAiPromptArea');
        var generateBtn     = document.getElementById('homeAiGenerateBtn');
        var generateBtnText = document.getElementById('homeAiGenerateBtnText');
        var saveOpenBtn     = document.getElementById('homeAiSaveOpenBtn');
        var loadingBox      = document.getElementById('homeAiLoading');
        var previewBox      = document.getElementById('homeAiPreviewBox');
        var previewTitle    = document.getElementById('homeAiPreviewTitle');
        var previewContent  = document.getElementById('homeAiPreviewContent');
        var skipBtn         = document.getElementById('homeAiSkipTypingBtn');
        var homeTypewriter  = null;

        if (!modal) return;

        var selectedTone = 'balanced';
        var generatedTitle = '';
        var generatedContent = '';

        var openModal = function(initialPrompt, initialTemplate) {
            var prefs = window.getXeonPreferences();
            selectedTone = prefs.aiTone || 'balanced';

            // Sync tone chips
            modal.querySelectorAll('.tone-chip').forEach(function(c) {
                c.classList.toggle('active', c.getAttribute('data-tone') === selectedTone);
            });

            if (promptArea) promptArea.value = initialPrompt || '';
            if (templateSelect) templateSelect.value = initialTemplate || '';

            previewBox.style.display = 'none';
            loadingBox.style.display = 'none';
            saveOpenBtn.style.display = 'none';
            generateBtnText.textContent = 'Generate Note';
            generateBtn.disabled = false;

            modal.classList.add('active');
            if (promptArea && !initialPrompt) {
                setTimeout(function() { promptArea.focus(); }, 100);
            }
        };

        var closeModal = function() {
            modal.classList.remove('active');
        };

        triggerBtn?.addEventListener('click', function() {
            openModal('', '');
        });

        quickSubmitBtn?.addEventListener('click', function() {
            var text = quickInput?.value.trim() || '';
            openModal(text, '');
            if (text) {
                // Auto trigger generation if prompt provided
                setTimeout(function() { generateBtn?.click(); }, 150);
            }
        });

        quickInput?.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                quickSubmitBtn?.click();
            }
        });

        // Quick template chips on home card
        document.querySelectorAll('.home-template-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                var tmpl = chip.getAttribute('data-template') || '';
                var text = quickInput?.value.trim() || '';
                openModal(text, tmpl);
                setTimeout(function() { generateBtn?.click(); }, 150);
            });
        });

        closeBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Tone chips
        modal.querySelectorAll('.tone-chip').forEach(function(chip) {
            chip.addEventListener('click', function() {
                modal.querySelectorAll('.tone-chip').forEach(function(c) { c.classList.remove('active'); });
                chip.classList.add('active');
                selectedTone = chip.getAttribute('data-tone') || 'balanced';
            });
        });

        // Generate button action
        generateBtn?.addEventListener('click', function() {
            var prompt   = promptArea?.value.trim() || '';
            var template = templateSelect?.value || '';
            var prefs    = window.getXeonPreferences();
            var appUrl   = document.querySelector('meta[name="app-url"]')?.content || '';
            var csrf     = document.querySelector('meta[name="csrf-token"]')?.content || '';

            if (!prompt && !template) {
                if (typeof window.xeonToast === 'function') {
                    window.xeonToast('Input Required', 'Please enter a prompt or choose a template.', 'warning');
                }
                return;
            }

            loadingBox.style.display = 'flex';
            previewBox.style.display = 'none';
            saveOpenBtn.style.display = 'none';
            generateBtn.disabled = true;
            generateBtnText.textContent = 'Generating...';

            var formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('action', template ? 'template' : 'generate');
            formData.append('prompt', prompt);
            formData.append('template_type', template);
            formData.append('tone', selectedTone);
            if (prefs.customGroqKey) {
                formData.append('custom_api_key', prefs.customGroqKey);
            }

            fetch(appUrl + '/ai/generate', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                loadingBox.style.display = 'none';
                generateBtn.disabled = false;
                generateBtnText.textContent = 'Regenerate';

                if (!data.success) {
                    if (typeof window.xeonToast === 'function') {
                        window.xeonToast('Xeon AI Error', data.message || 'Generation failed.', 'error', 6000);
                    }
                    return;
                }

                generatedTitle = data.title || 'AI Generated Note';
                generatedContent = data.content || '';

                previewTitle.textContent = generatedTitle;
                previewBox.style.display = 'block';
                saveOpenBtn.style.display = 'inline-flex';
                if (skipBtn) skipBtn.style.display = 'inline-flex';

                if (homeTypewriter) homeTypewriter.cancel();
                homeTypewriter = window.runAiTypewriter(previewContent, generatedContent, {
                    speed: 10,
                    onComplete: function() {
                        if (skipBtn) skipBtn.style.display = 'none';
                    }
                });

                if (skipBtn) {
                    skipBtn.onclick = function() {
                        if (homeTypewriter) homeTypewriter.skip();
                    };
                }
                previewContent.onclick = function() {
                    if (homeTypewriter) homeTypewriter.skip();
                };

                if (typeof window.xeonToast === 'function') {
                    window.xeonToast('Generated with Xeon AI', 'Your note draft is ready. Click "Save & Open Note" to start editing.', 'success');
                }
            })
            .catch(function(err) {
                loadingBox.style.display = 'none';
                generateBtn.disabled = false;
                generateBtnText.textContent = 'Generate Note';
                if (typeof window.xeonToast === 'function') {
                    window.xeonToast('Network Error', 'Failed to communicate with Xeon AI server.', 'error');
                }
                console.error(err);
            });
        });

        // Save & Open Note action
        saveOpenBtn?.addEventListener('click', function() {
            var appUrl = document.querySelector('meta[name="app-url"]')?.content || '';
            var csrf   = document.querySelector('meta[name="csrf-token"]')?.content || '';

            if (!generatedContent) return;

            saveOpenBtn.disabled = true;
            saveOpenBtn.innerHTML = '<span class="material-symbols-outlined text-sm ai-sparkle-spin">sync</span> Saving...';

            var formData = new FormData();
            formData.append('csrf_token', csrf);
            formData.append('title', generatedTitle || 'AI Generated Note');
            formData.append('content', generatedContent);

            fetch(appUrl + '/note/save', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success && data.noteId) {
                    window.location.href = appUrl + '/note/' + data.noteId;
                } else {
                    saveOpenBtn.disabled = false;
                    saveOpenBtn.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Save & Open Note';
                    if (typeof window.xeonToast === 'function') {
                        window.xeonToast('Save Failed', data.message || 'Could not save note.', 'error');
                    }
                }
            })
            .catch(function() {
                saveOpenBtn.disabled = false;
                saveOpenBtn.innerHTML = '<span class="material-symbols-outlined text-sm">check</span> Save & Open Note';
            });
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
        initHomeAi();
        initHomeAiTypingPlaceholder();

        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 600,
                once: true,
                offset: 40,
                easing: 'ease-out-cubic'
            });
        }
    });

})();

