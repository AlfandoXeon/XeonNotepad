/* ================================================================
   Xeon Notepad — editor.js
   Dual-Mode Editor: Rich Text (WYSIWYG) & Markdown (GFM)
   High-speed auto-save (debounced & queued), live preview, word/char count,
   split view, syntax formatting shortcuts, and multi-format download
   ================================================================ */

(function () {
    'use strict';

    /* ── Config ───────────────────────────────────────────────────── */

    const DEBOUNCE_MS = 350; // High-speed: 350ms after keystroke before auto-save triggers

    const APP_URL  = window.XEON?.appUrl    || '';
    const getCSRF  = () => window.XEON?.csrfToken
                        || document.querySelector('meta[name="csrf-token"]')?.content
                        || '';
    let noteId     = window.XEON?.noteId    || null;

    /* ── DOM refs ─────────────────────────────────────────────────── */

    const titleInput        = document.getElementById('noteTitle');
    const contentEl         = document.getElementById('noteContent');
    const saveIndicator     = document.getElementById('saveIndicator');
    const saveTextEl        = saveIndicator?.querySelector('.save-text');
    const wordCountEl       = document.getElementById('wordCount');
    const charCountEl       = document.getElementById('charCount');

    // Guard: only run on editor pages
    if (!titleInput || !contentEl) return;

    // Mode switch & groups
    const btnModeRich       = document.getElementById('btnModeRich');
    const btnModeMd         = document.getElementById('btnModeMd');
    const richToolbarGroup  = document.getElementById('richToolbarGroup');
    const mdToolbarGroup    = document.getElementById('mdToolbarGroup');

    // Markdown workspace & panes
    const markdownWorkspace = document.getElementById('markdownWorkspace');
    const noteMarkdown      = document.getElementById('noteMarkdown');
    const markdownPreview   = document.getElementById('markdownPreview');
    const mdViewWrite       = document.getElementById('mdViewWrite');
    const mdViewSplit       = document.getElementById('mdViewSplit');
    const mdViewPreview     = document.getElementById('mdViewPreview');

    // Markdown toolbar actions
    const mdBold            = document.getElementById('mdBold');
    const mdItalic          = document.getElementById('mdItalic');
    const mdStrike          = document.getElementById('mdStrike');
    const mdHeading         = document.getElementById('mdHeading');
    const mdCode            = document.getElementById('mdCode');
    const mdCodeBlock       = document.getElementById('mdCodeBlock');
    const mdQuote           = document.getElementById('mdQuote');
    const mdUl              = document.getElementById('mdUl');
    const mdOl              = document.getElementById('mdOl');
    const mdTask            = document.getElementById('mdTask');
    const mdLink            = document.getElementById('mdLink');
    const mdTable           = document.getElementById('mdTable');
    const mdHr              = document.getElementById('mdHr');

    // Download items
    const downloadBtn       = document.getElementById('downloadBtn');
    const downloadMenu      = document.getElementById('downloadMenu');
    const dlTxt             = document.getElementById('dlTxt');
    const dlHtml            = document.getElementById('dlHtml');
    const dlMd              = document.getElementById('dlMd');

    /* ── Converters: Turndown & Marked ────────────────────────────── */

    let turndownService = null;
    if (typeof TurndownService !== 'undefined') {
        turndownService = new TurndownService({
            headingStyle: 'atx',
            hr: '---',
            bulletListMarker: '-',
            codeBlockStyle: 'fenced',
            emDelimiter: '*'
        });
        // Prevent Turndown from escaping brackets like [ ] into \[ \]
        turndownService.escape = function (str) {
            return str;
        };
    }

    if (typeof marked !== 'undefined') {
        marked.setOptions({
            gfm: true,
            breaks: true
        });
    }

    /* ── State ────────────────────────────────────────────────────── */

    let currentMode = 'rich'; // 'rich' | 'markdown'
    let currentMdView = 'split'; // 'write' | 'split' | 'preview'

    function isHtmlString(str) {
        return /<\/?(p|br|h\d|ul|ol|li|blockquote|pre|code|strong|b|em|i|table|a|span|div)[^>]*>/i.test(str);
    }

    /* ── Save indicator ───────────────────────────────────────────── */

    let clearTimer = null;

    function setSaveState(state, elapsedMs = null) {
        if (!saveIndicator || !saveTextEl) return;
        clearTimeout(clearTimer);
        saveIndicator.className = 'save-indicator ' + state;
        if (state === 'saving') {
            saveTextEl.textContent = 'Saving…';
        } else if (state === 'saved') {
            const timeLabel = elapsedMs !== null ? `Saved (${elapsedMs}ms)` : 'Saved';
            saveTextEl.innerHTML = `
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="display:inline-block;vertical-align:middle;margin-right:2px" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>${timeLabel}</span>
            `;
            clearTimer = setTimeout(() => {
                saveIndicator.className = 'save-indicator';
                saveTextEl.textContent = '';
            }, 2500);
        } else {
            saveTextEl.textContent = '';
        }
    }

    /* ── Rich Text Toolbar: execCommand ───────────────────────────── */

    function execCmd(cmd, value = null) {
        contentEl.focus();
        document.execCommand(cmd, false, value);
        updateToolbarState();
        scheduleAutoSave();
    }

    // Wire format buttons
    richToolbarGroup?.querySelectorAll('.toolbar-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault(); // prevent blur
            execCmd(btn.dataset.cmd);
        });
    });

    // Heading select
    const headingSelect = document.getElementById('tbHeading');
    headingSelect?.addEventListener('change', () => {
        const val = headingSelect.value;
        execCmd('formatBlock', val || 'p');
        headingSelect.value = val;
    });

    // Text color
    const colorInput     = document.getElementById('tbColor');
    const colorIndicator = document.getElementById('colorIndicator');
    if (colorInput) {
        if (colorIndicator) colorIndicator.style.background = colorInput.value;
        colorInput.addEventListener('input', () => {
            const color = colorInput.value;
            if (colorIndicator) colorIndicator.style.background = color;
            execCmd('foreColor', color);
        });
    }

    // Update active state of rich text toolbar buttons
    function updateToolbarState() {
        if (currentMode !== 'rich') return;
        const cmdMap = {
            'tbBold':      'bold',
            'tbItalic':    'italic',
            'tbUnderline': 'underline',
            'tbStrike':    'strikeThrough',
            'tbUl':        'insertUnorderedList',
            'tbOl':        'insertOrderedList',
        };
        Object.entries(cmdMap).forEach(([id, cmd]) => {
            const btn = document.getElementById(id);
            if (!btn) return;
            const active = document.queryCommandState(cmd);
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-pressed', String(active));
        });
    }

    contentEl.addEventListener('keyup',   updateToolbarState);
    contentEl.addEventListener('mouseup', updateToolbarState);
    contentEl.addEventListener('focus',   updateToolbarState);

    /* ── Markdown Live Preview & Helpers ──────────────────────────── */

    function renderMarkdownPreview() {
        if (!markdownPreview || typeof marked === 'undefined') return;
        const mdText = noteMarkdown ? noteMarkdown.value : '';
        markdownPreview.innerHTML = marked.parse(mdText || '');
    }

    function insertOrWrapMd(before, after = '', defaultText = '') {
        if (!noteMarkdown) return;
        noteMarkdown.focus();
        const start = noteMarkdown.selectionStart;
        const end   = noteMarkdown.selectionEnd;
        const value = noteMarkdown.value;
        const selected = value.substring(start, end);

        if (selected) {
            const replacement = before + selected + after;
            noteMarkdown.setRangeText(replacement, start, end, 'select');
        } else {
            const textToInsert = before + defaultText + after;
            noteMarkdown.setRangeText(textToInsert, start, end, 'end');
            // Select the placeholder default text so user can immediately type over it
            if (defaultText) {
                noteMarkdown.setSelectionRange(start + before.length, start + before.length + defaultText.length);
            }
        }

        renderMarkdownPreview();
        updateCounts();
        scheduleAutoSave();
    }

    function prefixLinesMd(prefix) {
        if (!noteMarkdown) return;
        noteMarkdown.focus();
        const start = noteMarkdown.selectionStart;
        const end   = noteMarkdown.selectionEnd;
        const value = noteMarkdown.value;

        // Find the start of the current line
        const lineStart = value.lastIndexOf('\n', start - 1) + 1;
        // Find the end of the line
        let lineEnd = value.indexOf('\n', end);
        if (lineEnd === -1) lineEnd = value.length;

        const lines = value.substring(lineStart, lineEnd).split('\n');
        const prefixed = lines.map(l => prefix + l).join('\n');

        noteMarkdown.setRangeText(prefixed, lineStart, lineEnd, 'select');
        renderMarkdownPreview();
        updateCounts();
        scheduleAutoSave();
    }

    // Wire Markdown toolbar buttons
    mdBold?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('**', '**', 'bold text'); });
    mdItalic?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('*', '*', 'italic text'); });
    mdStrike?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('~~', '~~', 'strikethrough text'); });
    mdCode?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('`', '`', 'code'); });
    mdCodeBlock?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd("```\n", "\n```\n", "code block"); });
    mdQuote?.addEventListener('click', e => { e.preventDefault(); prefixLinesMd('> '); });
    mdUl?.addEventListener('click', e => { e.preventDefault(); prefixLinesMd('- '); });
    mdOl?.addEventListener('click', e => { e.preventDefault(); prefixLinesMd('1. '); });
    mdTask?.addEventListener('click', e => { e.preventDefault(); prefixLinesMd('- [ ] '); });
    mdLink?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('[', '](https://example.com)', 'link text'); });
    mdTable?.addEventListener('click', e => {
        e.preventDefault();
        insertOrWrapMd(
            '\n| Column 1 | Column 2 | Column 3 |\n' +
            '| --- | --- | --- |\n' +
            '| Item 1 | Item 2 | Item 3 |\n' +
            '| Item 4 | Item 5 | Item 6 |\n\n'
        );
    });
    mdHr?.addEventListener('click', e => { e.preventDefault(); insertOrWrapMd('\n\n---\n\n'); });

    mdHeading?.addEventListener('change', () => {
        const val = mdHeading.value;
        if (val) {
            prefixLinesMd(val);
            mdHeading.value = '';
        }
    });

    /* ── Markdown Sub-View Switching (Write / Split / Preview) ─────── */

    function setMdView(view) {
        currentMdView = view;
        if (markdownWorkspace) {
            markdownWorkspace.className = 'markdown-workspace view-' + view;
        }

        [mdViewWrite, mdViewSplit, mdViewPreview].forEach(btn => {
            if (!btn) return;
            const active = btn.dataset.view === view;
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-pressed', String(active));
        });

        localStorage.setItem('xeon_md_view', view);

        if (view === 'split' || view === 'preview') {
            renderMarkdownPreview();
        }
        if (view === 'write' || view === 'split') {
            noteMarkdown?.focus();
        }
    }

    mdViewWrite?.addEventListener('click',   () => setMdView('write'));
    mdViewSplit?.addEventListener('click',   () => setMdView('split'));
    mdViewPreview?.addEventListener('click', () => setMdView('preview'));

    /* ── Mode Switching: Rich Text <-> Markdown ───────────────────── */

    function setEditorMode(mode, doConvert = true) {
        if (!contentEl || !markdownWorkspace) return;

        if (mode === 'markdown') {
            if (doConvert && currentMode === 'rich') {
                const htmlContent = contentEl.innerHTML;
                if (htmlContent.trim()) {
                    const md = turndownService ? turndownService.turndown(htmlContent) : (contentEl.innerText || '');
                    noteMarkdown.value = md;
                }
            }
            currentMode = 'markdown';
            localStorage.setItem('xeon_editor_mode', 'markdown');

            // UI changes
            btnModeMd?.classList.add('active');
            btnModeMd?.setAttribute('aria-pressed', 'true');
            btnModeRich?.classList.remove('active');
            btnModeRich?.setAttribute('aria-pressed', 'false');

            if (richToolbarGroup) richToolbarGroup.style.display = 'none';
            if (mdToolbarGroup)   mdToolbarGroup.style.display = 'flex';

            contentEl.style.display = 'none';
            markdownWorkspace.style.display = 'flex';

            setMdView(currentMdView);
            renderMarkdownPreview();
            updateCounts();
            noteMarkdown?.focus();
        } else {
            // mode === 'rich'
            if (doConvert && currentMode === 'markdown') {
                const mdContent = noteMarkdown ? noteMarkdown.value : '';
                if (mdContent.trim()) {
                    contentEl.innerHTML = typeof marked !== 'undefined' ? marked.parse(mdContent) : mdContent;
                }
            }
            currentMode = 'rich';
            localStorage.setItem('xeon_editor_mode', 'rich');

            // UI changes
            btnModeRich?.classList.add('active');
            btnModeRich?.setAttribute('aria-pressed', 'true');
            btnModeMd?.classList.remove('active');
            btnModeMd?.setAttribute('aria-pressed', 'false');

            if (richToolbarGroup) richToolbarGroup.style.display = 'flex';
            if (mdToolbarGroup)   mdToolbarGroup.style.display = 'none';

            markdownWorkspace.style.display = 'none';
            contentEl.style.display = 'block';

            updateToolbarState();
            updateCounts();
            contentEl?.focus();
        }
    }

    btnModeRich?.addEventListener('click', () => {
        if (currentMode !== 'rich') {
            setEditorMode('rich', true);
            scheduleAutoSave();
        }
    });

    btnModeMd?.addEventListener('click', () => {
        if (currentMode !== 'markdown') {
            setEditorMode('markdown', true);
            scheduleAutoSave();
        }
    });

    /* ── Download dropdown ────────────────────────────────────────── */

    downloadBtn?.addEventListener('click', e => {
        e.stopPropagation();
        const open = downloadMenu.classList.toggle('open');
        downloadBtn.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('click', () => {
        downloadMenu?.classList.remove('open');
        downloadBtn?.setAttribute('aria-expanded', 'false');
    });

    function doDownload(format) {
        if (!noteId) {
            xeonToast('Not saved yet', 'Save the note first, then download it.', 'warning');
            return;
        }
        window.location.href = APP_URL + '/note/download?id=' + noteId + '&format=' + format;
        downloadMenu?.classList.remove('open');
    }

    dlTxt?.addEventListener('click',  e => { e.preventDefault(); doDownload('txt');  });
    dlHtml?.addEventListener('click', e => { e.preventDefault(); doDownload('html'); });
    dlMd?.addEventListener('click',   e => { e.preventDefault(); doDownload('md');   });

    /* ── Word & character count ───────────────────────────────────── */

    function updateCounts() {
        let text = '';
        if (currentMode === 'markdown') {
            text = noteMarkdown ? noteMarkdown.value : '';
        } else {
            text = contentEl.innerText || '';
        }
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const chars = text.length;
        if (wordCountEl) wordCountEl.textContent = words + (words === 1 ? ' word' : ' words');
        if (charCountEl) charCountEl.textContent = chars + (chars === 1 ? ' char' : ' chars');
    }

    /* ── Auto-save (high-speed debounced & queued) ───────────────── */

    let saveTimer         = null;
    let isSaving          = false;
    let hasPendingChanges = false;

    function scheduleAutoSave() {
        clearTimeout(saveTimer);
        setSaveState('saving');
        saveTimer = setTimeout(saveNote, DEBOUNCE_MS);
    }

    async function saveNote() {
        clearTimeout(saveTimer);

        if (isSaving) {
            hasPendingChanges = true;
            return;
        }

        const title = (titleInput.value || '').trim() || 'Untitled Note';
        let content = '';
        let plainSnippet = '';

        if (currentMode === 'markdown') {
            content = noteMarkdown ? noteMarkdown.value : '';
            plainSnippet = content;
        } else {
            content = contentEl.innerHTML;
            plainSnippet = contentEl.innerText || '';
        }

        isSaving = true;
        hasPendingChanges = false;
        setSaveState('saving');

        const fd = new FormData();
        fd.append('csrf_token', getCSRF());
        fd.append('title',      title);
        fd.append('content',    content);
        if (noteId !== null) fd.append('note_id', noteId);

        const startTime = performance.now();

        try {
            const res  = await fetch(APP_URL + '/note/save', {
                method:  'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body:    fd,
            });

            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();

            if (data.success) {
                const elapsed = Math.round(performance.now() - startTime);
                setSaveState('saved', elapsed);

                // If a new note was created, update state & URL
                if (!noteId && data.noteId) {
                    noteId = data.noteId;
                    if (window.XEON) window.XEON.noteId = noteId;
                    history.replaceState({}, '', APP_URL + '/note/' + noteId);
                    revealEditorControls(noteId);
                }

                // Live-sync sidebar note item
                updateSidebarItem(noteId, title, plainSnippet);
            } else {
                setSaveState('');
                xeonToast('Save failed', data.message || 'Could not save note.', 'error');
            }
        } catch {
            setSaveState('');
            xeonToast('Connection error', 'Could not reach the server. Changes may be unsaved.', 'error');
        } finally {
            isSaving = false;
            // If user typed while request was in-flight, immediately save newest content!
            if (hasPendingChanges) {
                hasPendingChanges = false;
                saveNote();
            }
        }
    }

    /**
     * Live-sync note title and preview inside the sidebar note list.
     */
    function updateSidebarItem(id, title, plainText) {
        if (!id) return;
        const item = document.querySelector(`.sidebar .note-item[data-note-id="${id}"]`);
        const snippet = plainText.trim().replace(/\s+/g, ' ').slice(0, 100);

        if (item) {
            const tEl = item.querySelector('.note-item-title');
            const pEl = item.querySelector('.note-item-preview');
            const mEl = item.querySelector('.note-item-meta');
            if (tEl) tEl.textContent = title;
            if (pEl) pEl.textContent = snippet;
            if (mEl) mEl.textContent = 'Just now';
        } else {
            // New note: prepend into note list if sidebar is present
            const noteList = document.getElementById('noteList');
            if (noteList) {
                const emptyEl = noteList.querySelector('.note-list-empty');
                if (emptyEl) emptyEl.remove();

                const a = document.createElement('a');
                a.href = APP_URL + '/note/' + id;
                a.className = 'note-item active';
                a.dataset.noteId = id;
                a.setAttribute('role', 'listitem');
                a.setAttribute('aria-current', 'page');
                a.title = title;

                const divTitle = document.createElement('div');
                divTitle.className = 'note-item-title';
                divTitle.textContent = title;
                a.appendChild(divTitle);

                if (snippet) {
                    const divPrev = document.createElement('div');
                    divPrev.className = 'note-item-preview';
                    divPrev.textContent = snippet;
                    a.appendChild(divPrev);
                }

                const divMeta = document.createElement('div');
                divMeta.className = 'note-item-meta';
                divMeta.textContent = 'Just now';
                a.appendChild(divMeta);

                noteList.prepend(a);

                const countEl = document.querySelector('.sidebar-note-count');
                if (countEl) {
                    const count = noteList.querySelectorAll('.note-item').length;
                    countEl.textContent = count + (count === 1 ? ' note' : ' notes');
                }
            }
        }
    }

    /**
     * After first save of a new note, enable history link and delete button.
     */
    function revealEditorControls(id) {
        // Delete button
        const delBtn = document.getElementById('deleteNoteBtn');
        if (!delBtn) {
            const footer = document.querySelector('.editor-footer');
            if (footer) {
                const btn = document.createElement('button');
                btn.className    = 'btn-delete-note';
                btn.id           = 'deleteNoteBtn';
                btn.dataset.noteId = id;
                btn.setAttribute('aria-label', 'Delete this note');
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                    </svg>
                    Delete note`;
                btn.addEventListener('click', () => window.showDeleteModal?.(id));
                footer.appendChild(btn);
            }
        } else {
            delBtn.dataset.noteId = id;
        }

        // History link in toolbar
        const toolbar = document.getElementById('editorToolbar');
        if (toolbar && !toolbar.querySelector('[href*="history"]')) {
            const a = document.createElement('a');
            a.className = 'toolbar-btn';
            a.href      = APP_URL + '/note/history/' + id;
            a.setAttribute('data-tooltip', 'View change history');
            a.setAttribute('aria-label',   'View change history');
            a.title     = 'Change history';
            a.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <polyline points="1 4 1 10 7 10"></polyline>
                <path d="M3.51 15a9 9 0 1 0 .49-4.51"></path>
            </svg>`;
            (toolbar.querySelector('.toolbar-end-actions') || toolbar).appendChild(a);
        }
    }

    /* ── Event listeners ──────────────────────────────────────────── */

    // Rich text input
    contentEl.addEventListener('input', () => {
        updateCounts();
        scheduleAutoSave();
    });

    // Markdown input
    noteMarkdown?.addEventListener('input', () => {
        renderMarkdownPreview();
        updateCounts();
        scheduleAutoSave();
    });

    // Handle Tab key in Markdown textarea to insert 2 spaces
    noteMarkdown?.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const start = noteMarkdown.selectionStart;
            const end   = noteMarkdown.selectionEnd;
            noteMarkdown.setRangeText('  ', start, end, 'end');
            renderMarkdownPreview();
            updateCounts();
            scheduleAutoSave();
        }
    });

    titleInput.addEventListener('input', scheduleAutoSave);

    // Immediate save on blur (no waiting for timer when clicking outside)
    titleInput.addEventListener('blur', () => {
        if (saveTimer) {
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    contentEl.addEventListener('blur', () => {
        if (saveTimer) {
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    noteMarkdown?.addEventListener('blur', () => {
        if (saveTimer) {
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    // Save immediately if tab becomes hidden or window unloads
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && saveTimer) {
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    window.addEventListener('beforeunload', () => {
        if (saveTimer) {
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    // Ctrl+S / Cmd+S → immediate save
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            clearTimeout(saveTimer);
            saveNote();
        }
    });

    // Paste in Rich Text: strip external formatting, insert as plain text
    contentEl.addEventListener('paste', e => {
        e.preventDefault();
        const text = e.clipboardData.getData('text/plain');
        document.execCommand('insertText', false, text);
    });

    /* ── Keyboard shortcuts ───────────────────────────────────────── */

    contentEl.addEventListener('keydown', e => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'b': e.preventDefault(); execCmd('bold');          break;
                case 'i': e.preventDefault(); execCmd('italic');        break;
                case 'u': e.preventDefault(); execCmd('underline');     break;
            }
        }
    });

    noteMarkdown?.addEventListener('keydown', e => {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'b': e.preventDefault(); insertOrWrapMd('**', '**', 'bold text');   break;
                case 'i': e.preventDefault(); insertOrWrapMd('*', '*', 'italic text');     break;
            }
        }
    });

    /* ── Proportional Scroll Sync for Split View ──────────────────── */

    let isScrollingPane = false;
    noteMarkdown?.addEventListener('scroll', () => {
        if (currentMode !== 'markdown' || currentMdView !== 'split' || isScrollingPane) return;
        isScrollingPane = true;
        const scrollPct = noteMarkdown.scrollTop / (noteMarkdown.scrollHeight - noteMarkdown.clientHeight || 1);
        markdownPreview.scrollTop = scrollPct * (markdownPreview.scrollHeight - markdownPreview.clientHeight);
        setTimeout(() => { isScrollingPane = false; }, 40);
    });

    markdownPreview?.addEventListener('scroll', () => {
        if (currentMode !== 'markdown' || currentMdView !== 'split' || isScrollingPane) return;
        isScrollingPane = true;
        const scrollPct = markdownPreview.scrollTop / (markdownPreview.scrollHeight - markdownPreview.clientHeight || 1);
        noteMarkdown.scrollTop = scrollPct * (noteMarkdown.scrollHeight - noteMarkdown.clientHeight);
        setTimeout(() => { isScrollingPane = false; }, 40);
    });

    /* ── Init ─────────────────────────────────────────────────────── */

    const initialRaw = contentEl.innerHTML || '';
    const hasHtml = isHtmlString(initialRaw);
    const savedMode = localStorage.getItem('xeon_editor_mode');
    const savedView = localStorage.getItem('xeon_md_view') || 'split';
    currentMdView = savedView;

    // If content is raw markdown (e.g. non-empty text without HTML tags)
    // or user preference was markdown, initialize in Markdown mode
    const shouldStartMd = savedMode === 'markdown' || (!hasHtml && initialRaw.trim().length > 0 && initialRaw.includes('#'));

    if (shouldStartMd) {
        if (hasHtml) {
            noteMarkdown.value = turndownService ? turndownService.turndown(initialRaw) : (contentEl.innerText || '');
        } else {
            noteMarkdown.value = initialRaw;
        }
        setEditorMode('markdown', false);
    } else {
        if (!hasHtml && initialRaw.trim().length > 0) {
            // Raw text/markdown loaded into rich editor: render as html
            contentEl.innerHTML = typeof marked !== 'undefined' ? marked.parse(initialRaw) : initialRaw;
        }
        setEditorMode('rich', false);
    }

    updateCounts();
    updateToolbarState();

    // Focus for new notes
    if (!noteId) {
        if (currentMode === 'markdown') {
            noteMarkdown?.focus();
        } else {
            contentEl.focus();
        }
    }

})();
