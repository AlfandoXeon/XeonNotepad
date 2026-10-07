<?php
$hideSidebar = true;
?>
<div class="notes-page all-notes-page" id="allNotesPage">

    <!-- ── Page Header ──────────────────────────────────────────── -->
    <div class="notes-page-header" data-aos="fade-down" data-aos-duration="600">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                <a href="<?= APP_URL ?>/notes" class="btn-back-link" title="Back to Dashboard">
                    <span class="material-symbols-outlined text-sm" aria-hidden="true">arrow_back</span>
                    <span>Dashboard</span>
                </a>
            </div>
            <h1 class="notes-page-title">All Notes</h1>
            <p class="notes-page-subtitle">
                <?php $count = count($notes ?? []); ?>
                <span id="allNotesCountLabel"><?= $count ?> <?= $count === 1 ? 'note' : 'notes' ?></span> in your encrypted vault
            </p>
        </div>
        <div class="notes-page-header-actions">
            <button type="button" class="btn btn-ai-sparkle" id="homeAiModalTriggerBtn" title="Create note with Xeon AI">
                <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                <span>Create with Xeon AI</span>
            </button>
            <a href="<?= APP_URL ?>/note/new" class="btn btn-primary" id="newNoteHeaderBtn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                New Note
            </a>
        </div>
    </div>

    <!-- ── Live Filter & Controls Bar ────────────────────────────── -->
    <div class="all-notes-toolbar" data-aos="fade-up" data-aos-duration="600">
        <div class="all-notes-search-box">
            <span class="material-symbols-outlined text-muted" aria-hidden="true">search</span>
            <input
                type="text"
                id="allNotesFilterInput"
                class="all-notes-search-input"
                placeholder="Filter notes by title or snippet content..."
                autocomplete="off"
            >
            <button type="button" class="btn-clear-filter" id="clearFilterBtn" style="display:none;" title="Clear filter">
                <span class="material-symbols-outlined text-xs">close</span>
            </button>
        </div>

        <div class="all-notes-sort-wrap">
            <span class="material-symbols-outlined text-sm text-muted" aria-hidden="true">sort</span>
            <select id="allNotesSortSelect" class="form-input form-input-sm" style="width:auto; padding:6px 12px;">
                <option value="newest">Sort: Newest First</option>
                <option value="oldest">Sort: Oldest First</option>
                <option value="title_asc">Sort: Title (A-Z)</option>
                <option value="title_desc">Sort: Title (Z-A)</option>
            </select>
        </div>
    </div>

    <?php if (empty($notes)): ?>
    <!-- ── Empty state ─────────────────────────────────────────── -->
    <div class="notes-empty-state" data-aos="zoom-in">
        <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="No notes yet" width="80" height="80">
        <h2>Your notepad is empty</h2>
        <p>Start writing your first note or let Xeon AI generate one for you.</p>
        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:8px">
            <button type="button" class="btn btn-ai-sparkle" onclick="document.getElementById('homeAiModalTriggerBtn').click()">
                <span class="material-symbols-outlined" aria-hidden="true">auto_awesome</span>
                Generate with Xeon AI
            </button>
            <a href="<?= APP_URL ?>/note/new" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Blank note
            </a>
        </div>
    </div>

    <?php else: ?>
    <!-- ── Note cards grid ─────────────────────────────────────── -->
    <div class="notes-grid" id="allNotesGrid" role="list" aria-label="Your notes">

        <?php foreach ($notes as $idx => $note): ?>
        <article
            class="note-card"
            role="listitem"
            data-note-id="<?= (int)$note['id'] ?>"
            data-title="<?= htmlspecialchars(mb_strtolower($note['title'])) ?>"
            data-preview="<?= htmlspecialchars(mb_strtolower($note['preview'] ?? '')) ?>"
            data-date="<?= strtotime($note['updated_at']) ?>"
            data-aos="fade-up"
            data-aos-delay="<?= min(350, $idx * 30) ?>"
        >

            <div class="note-card-body">
                <a href="<?= APP_URL ?>/note/<?= (int)$note['id'] ?>" class="note-card-title-link" tabindex="0">
                    <h3 class="note-card-title"><?= htmlspecialchars($note['title']) ?></h3>
                </a>
                <?php if (!empty($note['preview'])): ?>
                    <p class="note-card-preview"><?= htmlspecialchars($note['preview']) ?></p>
                <?php endif; ?>
            </div>

            <div class="note-card-footer">
                <time class="note-card-date" datetime="<?= htmlspecialchars($note['updated_at']) ?>">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <?= date('M j, Y', strtotime($note['updated_at'])) ?>
                </time>
                <div class="note-card-actions">
                    <!-- Edit -->
                    <a href="<?= APP_URL ?>/note/<?= (int)$note['id'] ?>"
                       class="note-card-action-btn"
                       title="Edit note"
                       aria-label="Edit: <?= htmlspecialchars($note['title']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </a>
                    <!-- History -->
                    <a href="<?= APP_URL ?>/note/history/<?= (int)$note['id'] ?>"
                       class="note-card-action-btn"
                       title="View history"
                       aria-label="History of: <?= htmlspecialchars($note['title']) ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 .49-4.51"></path>
                        </svg>
                    </a>
                    <!-- Delete -->
                    <button
                       class="note-card-action-btn is-danger"
                       title="Delete note"
                       aria-label="Delete: <?= htmlspecialchars($note['title']) ?>"
                       onclick="window.showDeleteModal(<?= (int)$note['id'] ?>)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                        </svg>
                    </button>
                </div>
            </div>

        </article>
        <?php endforeach; ?>

    </div><!-- /.notes-grid -->

    <!-- No search match banner -->
    <div id="noMatchBanner" class="notes-empty-state" style="display:none; padding:40px 20px;">
        <span class="material-symbols-outlined text-muted" style="font-size:48px;" aria-hidden="true">search_off</span>
        <h2>No notes found</h2>
        <p>No notes matched your search query. Try another keyword or clear the filter.</p>
    </div>

    <?php endif; ?>

    <!-- ── Page Footer ─────────────────────────────────────────── -->
    <footer class="notes-footer">
        <p>Xeon Notepad - AlfandoXeon</p>
    </footer>

</div><!-- /.notes-page -->

<!-- ── Home Xeon AI Generator Modal ─────────────────────────────── -->
<div class="modal-overlay" id="homeAiModal" role="dialog" aria-modal="true" aria-labelledby="homeAiModalTitle">
    <div class="modal modal-lg ai-modal-card">
        <div class="ai-modal-header">
            <div class="ai-modal-header-title">
                <span class="material-symbols-outlined ai-sparkle-icon" aria-hidden="true">auto_awesome</span>
                <div>
                    <h3 id="homeAiModalTitle" class="ai-modal-heading">Xeon AI &mdash; New Note</h3>
                    <span style="font-size:12px; color:var(--text-muted);">Groq-accelerated intelligence</span>
                </div>
            </div>
            <button type="button" class="btn-icon" id="closeHomeAiModal" aria-label="Close modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="ai-modal-body">
            <!-- Template Selection -->
            <div class="form-group">
                <label class="form-label" for="homeAiTemplateSelect">Note Template</label>
                <select id="homeAiTemplateSelect" class="form-input">
                    <option value="">Custom Freeform Prompt</option>
                    <option value="meeting_notes">Meeting Notes & Action Items</option>
                    <option value="study_summary">Study & Lecture Summary</option>
                    <option value="todo_list">Prioritized Action Checklist</option>
                    <option value="project_plan">Project Plan & Roadmap</option>
                    <option value="technical_doc">Technical Documentation</option>
                    <option value="brainstorming">Brainstorming & Mindmap</option>
                    <option value="formal_email">Formal Email & Letter</option>
                    <option value="creative_draft">Article / Creative Draft</option>
                </select>
            </div>

            <!-- Prompt / Instructions -->
            <div class="form-group">
                <label class="form-label" for="homeAiPromptArea">Topic / Instructions</label>
                <textarea
                    id="homeAiPromptArea"
                    class="form-input"
                    rows="3"
                    placeholder="Describe what you want to write about..."
                    style="resize:vertical;"
                ></textarea>
            </div>

            <!-- Tone selection -->
            <div class="form-group">
                <label class="form-label">Voice & Tone</label>
                <div class="ai-tone-chips" role="radiogroup">
                    <button type="button" class="tone-chip active" data-tone="balanced">Balanced</button>
                    <button type="button" class="tone-chip" data-tone="professional">Professional</button>
                    <button type="button" class="tone-chip" data-tone="casual">Casual</button>
                    <button type="button" class="tone-chip" data-tone="academic">Academic</button>
                    <button type="button" class="tone-chip" data-tone="concise">Concise</button>
                </div>
            </div>

            <!-- Generation Loading Box -->
            <div class="ai-loading-box" id="homeAiLoading" style="display:none;">
                <div class="ai-loading-spinner"></div>
                <div class="ai-loading-status" id="homeAiStatusText">Xeon AI is thinking and structuring your note...</div>
            </div>

            <!-- Generation Preview Box -->
            <div class="ai-preview-box" id="homeAiPreviewBox" style="display:none;">
                <div class="ai-preview-header">
                    <span class="ai-preview-title" id="homeAiPreviewTitle">Generated Note Preview</span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button type="button" class="btn-skip-typing" id="homeAiSkipTypingBtn" style="display:none;" title="Skip typing animation">
                            <span class="material-symbols-outlined text-xs">fast_forward</span>
                            <span>Skip</span>
                        </button>
                        <span class="badge-status-active" id="homeAiReadyBadge" style="font-size:11px;">Ready</span>
                    </div>
                </div>
                <div class="ai-preview-content" id="homeAiPreviewContent"></div>
            </div>
        </div>

        <div class="modal-actions ai-modal-actions">
            <button type="button" class="btn btn-ghost" id="cancelHomeAiModal">Cancel</button>
            <button type="button" class="btn btn-primary" id="homeAiGenerateBtn">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">auto_awesome</span>
                <span id="homeAiGenerateBtnText">Generate Note</span>
            </button>
            <button type="button" class="btn btn-success" id="homeAiSaveOpenBtn" style="display:none;">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">check</span>
                <span>Save & Open Note</span>
            </button>
        </div>
    </div>
</div>

<!-- Inline search & sort script for /listnote -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var filterInput = document.getElementById('allNotesFilterInput');
    var clearBtn    = document.getElementById('clearFilterBtn');
    var sortSelect  = document.getElementById('allNotesSortSelect');
    var grid        = document.getElementById('allNotesGrid');
    var countLabel  = document.getElementById('allNotesCountLabel');
    var noMatch     = document.getElementById('noMatchBanner');

    if (!grid) return;

    function applyFilterAndSort() {
        var query = (filterInput ? filterInput.value.trim().toLowerCase() : '');
        if (clearBtn) clearBtn.style.display = query ? 'inline-flex' : 'none';

        var cards = Array.from(grid.querySelectorAll('.note-card'));
        var visibleCount = 0;

        cards.forEach(function(card) {
            var title   = card.getAttribute('data-title') || '';
            var preview = card.getAttribute('data-preview') || '';
            var match   = !query || title.includes(query) || preview.includes(query);

            if (match) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countLabel) {
            countLabel.textContent = visibleCount + (visibleCount === 1 ? ' note' : ' notes');
        }

        if (noMatch) {
            noMatch.style.display = (visibleCount === 0 && cards.length > 0) ? 'flex' : 'none';
        }

        // Sorting
        var sortVal = sortSelect ? sortSelect.value : 'newest';
        cards.sort(function(a, b) {
            if (sortVal === 'newest') {
                return (parseInt(b.getAttribute('data-date') || '0', 10) - parseInt(a.getAttribute('data-date') || '0', 10));
            } else if (sortVal === 'oldest') {
                return (parseInt(a.getAttribute('data-date') || '0', 10) - parseInt(b.getAttribute('data-date') || '0', 10));
            } else if (sortVal === 'title_asc') {
                return (a.getAttribute('data-title') || '').localeCompare(b.getAttribute('data-title') || '');
            } else if (sortVal === 'title_desc') {
                return (b.getAttribute('data-title') || '').localeCompare(a.getAttribute('data-title') || '');
            }
            return 0;
        });

        cards.forEach(function(card) {
            grid.appendChild(card);
        });
    }

    if (filterInput) {
        filterInput.addEventListener('input', applyFilterAndSort);
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            filterInput.value = '';
            applyFilterAndSort();
            filterInput.focus();
        });
    }
    if (sortSelect) {
        sortSelect.addEventListener('change', applyFilterAndSort);
    }
});
</script>
