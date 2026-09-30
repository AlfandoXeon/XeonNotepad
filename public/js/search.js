/* ================================================================
   Xeon Notepad — search.js
   Live search with animated dropdown, result highlighting,
   and keyboard navigation (Escape, Ctrl+K)
   ================================================================ */

(function () {
    'use strict';

    const searchInput    = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');

    if (!searchInput || !searchDropdown) return;

    const APP_URL = document.querySelector('meta[name="app-url"]')?.content || '';

    let debounceTimer = null;
    let lastQuery     = null;

    /* ── Helpers ──────────────────────────────────────────────────── */

    function debounce(fn, delay) {
        return (...args) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fn(...args), delay);
        };
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    /** Wrap query matches in <mark> for highlighting */
    function highlight(text, query) {
        if (!query) return escHtml(text);
        const safe  = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp('(' + safe + ')', 'gi');
        return escHtml(text).replace(regex, '<mark>$1</mark>');
    }

    function hideDropdown() {
        searchDropdown.classList.remove('visible');
        searchDropdown.innerHTML = '';
        lastQuery = null;
        searchInput.setAttribute('aria-expanded', 'false');
    }

    /* ── Render results ───────────────────────────────────────────── */

    function renderResults(notes, query) {
        if (!notes || notes.length === 0) {
            searchDropdown.innerHTML = `
                <div style="padding:14px 16px;text-align:center;font-size:13px;color:var(--text-muted)">
                    No results for <strong>"${escHtml(query)}"</strong>
                </div>
            `;
            searchDropdown.classList.add('visible');
            searchInput.setAttribute('aria-expanded', 'true');
            return;
        }

        const items = notes.slice(0, 8).map(note => `
            <a
                class="search-result-item"
                href="${APP_URL}/note/${note.id}"
                role="option"
                aria-selected="false"
            >
                <div class="search-result-title">${highlight(note.title, query)}</div>
                ${note.preview ? `<div class="search-result-preview">${escHtml(note.preview)}</div>` : ''}
            </a>
        `).join('');

        searchDropdown.innerHTML = items;
        searchDropdown.classList.add('visible');
        searchInput.setAttribute('aria-expanded', 'true');
    }

    /* ── Fetch search results ─────────────────────────────────────── */

    async function doSearch(query) {
        const q = query.trim();

        if (q === '') {
            hideDropdown();
            return;
        }

        if (q === lastQuery) return;
        lastQuery = q;

        try {
            const url = APP_URL + '/note/search?q=' + encodeURIComponent(q);
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) renderResults(data.notes, q);
        } catch {
            // Silent fail — search is non-critical
        }
    }

    /* ── Live homepage grid filter ────────────────────────────────── */

    function filterNotesGrid(query) {
        const grid = document.getElementById('notesGrid');
        if (!grid) return;
        const q = query.trim().toLowerCase();
        const cards = grid.querySelectorAll('.note-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const title   = card.querySelector('.note-card-title')?.textContent.toLowerCase() || '';
            const preview = card.querySelector('.note-card-preview')?.textContent.toLowerCase() || '';
            const matches = !q || title.includes(q) || preview.includes(q);
            card.style.display = matches ? 'flex' : 'none';
            if (matches) visibleCount++;
        });

        let noMatchEl = document.getElementById('gridNoMatchMsg');
        if (visibleCount === 0 && q !== '') {
            if (!noMatchEl) {
                noMatchEl = document.createElement('div');
                noMatchEl.id = 'gridNoMatchMsg';
                noMatchEl.style.cssText = 'grid-column: 1 / -1; padding: 40px 20px; text-align: center; color: var(--text-muted); font-size: 14px;';
                grid.appendChild(noMatchEl);
            }
            noMatchEl.innerHTML = `No notes found matching "<strong>${escHtml(query)}</strong>"`;
            noMatchEl.style.display = 'block';
        } else if (noMatchEl) {
            noMatchEl.style.display = 'none';
        }
    }

    const debouncedSearch = debounce(doSearch, 280);

    /* ── Events ───────────────────────────────────────────────────── */

    searchInput.addEventListener('input', () => {
        filterNotesGrid(searchInput.value);
        debouncedSearch(searchInput.value);
    });

    // Escape clears search
    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            hideDropdown();
            searchInput.value = '';
            searchInput.blur();
        }
        // Keyboard navigate results with arrow keys
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const first = searchDropdown.querySelector('a');
            first?.focus();
        }
    });

    // Arrow key navigation within dropdown
    searchDropdown.addEventListener('keydown', e => {
        const items = [...searchDropdown.querySelectorAll('a')];
        const idx   = items.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            items[idx + 1]?.focus();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (idx <= 0) searchInput.focus();
            else items[idx - 1]?.focus();
        } else if (e.key === 'Escape') {
            hideDropdown();
            searchInput.value = '';
            searchInput.focus();
        }
    });

    // Close dropdown on outside click
    document.addEventListener('click', e => {
        if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    // Ctrl+K / Cmd+K → focus search
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

})();
