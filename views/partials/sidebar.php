<aside class="sidebar" id="sidebar" aria-label="Notes navigation">

    <!-- New Note button -->
    <div class="sidebar-header">
        <a href="<?= APP_URL ?>/note/new" class="btn-new-note" id="newNoteBtn" aria-label="Create a new note">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Note
        </a>
    </div>

    <!-- Note count label -->
    <div class="sidebar-note-count" aria-live="polite" style="display:flex; justify-content:space-between; align-items:center;">
        <?php $cnt = count($notes ?? []); ?>
        <span><?= $cnt ?> <?= $cnt === 1 ? 'note' : 'notes' ?></span>
        <a href="<?= APP_URL ?>/listnote" style="color:var(--primary); font-size:11px; text-decoration:none; font-weight:600;" title="View all notes">View All</a>
    </div>

    <!-- Scrollable note list -->
    <div class="note-list" id="noteList" role="list" aria-label="Your notes">

        <?php if (empty($notes)): ?>
            <div class="note-list-empty" role="listitem">
                <span class="material-symbols-outlined note-list-empty-icon" aria-hidden="true">description</span>
                <p>No notes yet.<br>Create your first note!</p>
            </div>
        <?php else: ?>
            <?php foreach ($notes as $n): ?>
            <a
                href="<?= APP_URL ?>/note/<?= (int)$n['id'] ?>"
                class="note-item <?= (isset($activeNote) && $activeNote === (int)$n['id']) ? 'active' : '' ?>"
                data-note-id="<?= (int)$n['id'] ?>"
                role="listitem"
                aria-current="<?= (isset($activeNote) && $activeNote === (int)$n['id']) ? 'page' : 'false' ?>"
                title="<?= htmlspecialchars($n['title']) ?>"
            >
                <div class="note-item-title"><?= htmlspecialchars($n['title']) ?></div>
                <?php if (!empty($n['preview'])): ?>
                    <div class="note-item-preview"><?= htmlspecialchars($n['preview']) ?></div>
                <?php endif; ?>
                <div class="note-item-meta">
                    <?= date('M j, Y', strtotime($n['updated_at'])) ?>
                </div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</aside>
