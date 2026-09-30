<?php
$hideSidebar = true;
?>
<div class="notes-page" id="notesPage">

    <!-- ── Page header ──────────────────────────────────────────── -->
    <div class="notes-page-header">
        <div>
            <h1 class="notes-page-title">My Notes</h1>
            <p class="notes-page-subtitle">
                <?php $count = count($notes ?? []); ?>
                <?= $count ?> <?= $count === 1 ? 'note' : 'notes' ?> in your workspace
            </p>
        </div>
        <a href="<?= APP_URL ?>/note/new" class="btn btn-primary" id="newNoteHeaderBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Note
        </a>
    </div>

    <?php if (empty($notes)): ?>
    <!-- ── Empty state ─────────────────────────────────────────── -->
    <div class="notes-empty-state">
        <img src="<?= APP_URL ?>/logo/NotepadIcon.png" alt="No notes yet" width="80" height="80">
        <h2>Your notepad is empty</h2>
        <p>Start writing your first note — it will be encrypted and saved automatically.</p>
        <a href="<?= APP_URL ?>/note/new" class="btn btn-primary" style="margin-top:4px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Create my first note
        </a>
    </div>

    <?php else: ?>
    <!-- ── Note cards grid ─────────────────────────────────────── -->
    <div class="notes-grid" id="notesGrid" role="list" aria-label="Your notes">

        <?php foreach ($notes as $note): ?>
        <article class="note-card" role="listitem" data-note-id="<?= (int)$note['id'] ?>">

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
    <?php endif; ?>

    <!-- ── Homepage Footer ──────────────────────────────────────── -->
    <footer class="notes-footer">
        <p>Xeon Notepad - AlfandoXeon</p>
    </footer>

</div><!-- /.notes-page -->
