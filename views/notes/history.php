<div class="history-page">

    <!-- Back link -->
    <a href="<?= APP_URL ?>/note/<?= (int)$note['id'] ?>" class="history-back" aria-label="Back to note">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to note
    </a>

    <h1>Change History</h1>
    <p class="history-subtitle"><?= htmlspecialchars($note['title']) ?></p>

    <?php if (empty($history)): ?>
        <div class="history-empty" role="status">
            <span class="material-symbols-outlined history-empty-icon" aria-hidden="true">history_toggle_off</span>
            <p>No changes recorded yet.</p>
            <p style="font-size:13px;margin-top:4px">Changes are logged automatically every time you save your note.</p>
        </div>

    <?php else: ?>
        <div class="timeline" role="list" aria-label="Change history">
            <?php foreach ($history as $entry): ?>
            <div class="timeline-item" role="listitem">
                <div class="timeline-dot <?= htmlspecialchars($entry['action']) ?>" aria-hidden="true"></div>
                <div class="timeline-card">
                    <div class="timeline-meta">
                        <span class="timeline-badge badge-<?= htmlspecialchars($entry['action']) ?>">
                            <?= ucfirst(htmlspecialchars($entry['action'])) ?>
                        </span>
                        <time class="timeline-time" datetime="<?= htmlspecialchars($entry['created_at']) ?>">
                            <?= date('M j, Y — g:i A', strtotime($entry['created_at'])) ?>
                        </time>
                    </div>
                    <p class="timeline-desc"><?= htmlspecialchars($entry['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
