<?php
declare(strict_types=1);

/**
 * NoteController — CRUD, AJAX save/search/delete, and download.
 */
class NoteController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────

    public function index(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $notes = (new Note())->getAllByUser($user['id']);

        $this->view('main', 'notes/index', [
            'pageTitle'   => APP_NAME,
            'notes'       => $notes,
            'currentUser' => $user,
            'csrfToken'   => $this->generateCsrf(),
            'activeNote'  => null,
            'hideSidebar' => true,
        ]);
    }

    // ── New note editor (empty) ────────────────────────────────────────────

    public function create(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $notes = (new Note())->getAllByUser($user['id']);

        $this->view('main', 'notes/editor', [
            'pageTitle'   => 'New Note — ' . APP_NAME,
            'notes'       => $notes,
            'currentUser' => $user,
            'csrfToken'   => $this->generateCsrf(),
            'note'        => null,
            'activeNote'  => null,
        ]);
    }

    // ── Edit existing note ─────────────────────────────────────────────────

    public function edit(int $id): void
    {
        $this->requireAuth();
        $user      = $this->currentUser();
        $noteModel = new Note();
        $note      = $noteModel->findById($id, $user['id']);

        if (!$note) {
            $this->redirect('/');
        }

        $notes = $noteModel->getAllByUser($user['id']);

        $this->view('main', 'notes/editor', [
            'pageTitle'   => htmlspecialchars($note['title']) . ' — ' . APP_NAME,
            'notes'       => $notes,
            'currentUser' => $user,
            'csrfToken'   => $this->generateCsrf(),
            'note'        => $note,
            'activeNote'  => $id,
        ]);
    }

    // ── AJAX: Save (create or update) ─────────────────────────────────────

    public function save(): void
    {
        $this->requireAuth();

        // Verify CSRF for AJAX
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->json(['success' => false, 'message' => 'Security token invalid. Please refresh the page.'], 403);
        }

        $user      = $this->currentUser();
        $noteId    = (isset($_POST['note_id']) && $_POST['note_id'] !== '')
                   ? (int)$_POST['note_id']
                   : null;
        $title     = trim(strip_tags($_POST['title'] ?? ''));
        $content   = $this->sanitizeHtml($_POST['content'] ?? '');

        if ($title === '') $title = 'Untitled Note';
        if (mb_strlen($title) > 255) $title = mb_substr($title, 0, 255);

        $noteModel    = new Note();
        $historyModel = new NoteHistory();

        try {
            if ($noteId === null) {
                // ── CREATE ──────────────────────────────────────────────────
                $newId = $noteModel->create($user['id'], $title, $content);
                $historyModel->log($newId, $user['id'], 'created', 'Note created.');
                $this->json(['success' => true, 'noteId' => $newId, 'action' => 'created']);

            } else {
                // ── UPDATE ──────────────────────────────────────────────────
                $oldNote = $noteModel->findById($noteId, $user['id']);
                if (!$oldNote) {
                    $this->json(['success' => false, 'message' => 'Note not found.'], 404);
                }

                $updated = $noteModel->update($noteId, $user['id'], $title, $content);

                if ($updated) {
                    // Build human-readable diff description
                    $diffParts = [];
                    if ($oldNote['title'] !== $title) {
                        $oldT = mb_substr($oldNote['title'], 0, 60);
                        $newT = mb_substr($title, 0, 60);
                        $diffParts[] = "Title changed from \"{$oldT}\" to \"{$newT}\"";
                    }
                    if (strip_tags($oldNote['content']) !== strip_tags($content)) {
                        if ($historyModel->shouldLogEdit($noteId, 45)) {
                            $diffParts[] = 'Content updated';
                        }
                    }
                    if (!empty($diffParts)) {
                        $historyModel->log($noteId, $user['id'], 'edited', implode('; ', $diffParts));
                    }
                }

                $this->json(['success' => true, 'noteId' => $noteId, 'action' => 'updated']);
            }
        } catch (Throwable $e) {
            error_log('[NoteController::save] ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to save. Please try again.'], 500);
        }
    }

    // ── AJAX: Live search ─────────────────────────────────────────────────

    public function search(): void
    {
        $this->requireAuth();
        $user  = $this->currentUser();
        $query = trim(strip_tags($_GET['q'] ?? ''));
        $notes = (new Note())->searchByUser($user['id'], $query);

        $results = array_map(static fn($n) => [
            'id'         => (int)$n['id'],
            'title'      => htmlspecialchars($n['title']),
            'preview'    => htmlspecialchars($n['preview']),
            'updated_at' => $n['updated_at'],
        ], $notes);

        $this->json(['success' => true, 'notes' => array_values($results)]);
    }

    // ── Download ──────────────────────────────────────────────────────────

    public function download(): void
    {
        $this->requireAuth();
        $user   = $this->currentUser();
        $noteId = $this->getInt('id');
        $format = in_array($_GET['format'] ?? '', ['txt', 'html', 'md'], true) ? $_GET['format'] : 'txt';

        if ($noteId === 0) $this->redirect('/');

        $note = (new Note())->findById($noteId, $user['id']);
        if (!$note) $this->redirect('/');

        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $note['title']);
        $filename = mb_substr($filename ?: 'note', 0, 60);

        if ($format === 'html') {
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.html"');
            echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
               . '<title>' . htmlspecialchars($note['title']) . '</title>'
               . '<style>body{font-family:sans-serif;max-width:800px;margin:40px auto;'
               . 'padding:0 20px;line-height:1.7;color:#1a1a1a}'
               . 'h1{color:#FF8000;border-bottom:2px solid #FF8000;padding-bottom:8px}</style>'
               . '</head><body>'
               . '<h1>' . htmlspecialchars($note['title']) . '</h1>'
               . '<p style="color:#888;font-size:13px">Last updated: ' . htmlspecialchars($note['updated_at']) . '</p>'
               . '<hr>'
               . $note['content']
               . '</body></html>';
        } elseif ($format === 'md') {
            header('Content-Type: text/markdown; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.md"');
            $content = $note['content'];
            $isHtml = (bool)preg_match('/<\/?(p|br|div|h\d|ul|ol|li|blockquote|pre|code|strong|b|em|i)/i', $content);
            if ($isHtml) {
                $content = preg_replace('/<h1>(.*?)<\/h1>/i', "# $1\n\n", $content);
                $content = preg_replace('/<h2>(.*?)<\/h2>/i', "## $1\n\n", $content);
                $content = preg_replace('/<h3>(.*?)<\/h3>/i', "### $1\n\n", $content);
                $content = preg_replace('/<(?:strong|b)>(.*?)<\/(?:strong|b)>/i', '**$1**', $content);
                $content = preg_replace('/<(?:em|i)>(.*?)<\/(?:em|i)>/i', '*$1*', $content);
                $content = preg_replace('/<li>(.*?)<\/li>/i', "- $1\n", $content);
                $content = preg_replace('/<blockquote>(.*?)<\/blockquote>/i', "> $1\n\n", $content);
                $content = preg_replace('/<code>(.*?)<\/code>/i', '`$1`', $content);
                $content = preg_replace('/<br\s*\/?>/i', "\n", $content);
                $content = preg_replace('/<\/div>/i', "\n", $content);
                $content = preg_replace('/<\/p>/i', "\n\n", $content);
                $content = strip_tags($content);
            }
            echo "# " . $note['title'] . "\n\n" . trim($content) . "\n";
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.txt"');
            $line = str_repeat('=', mb_strlen($note['title']));
            echo $note['title'] . "\n" . $line . "\n";
            echo 'Last updated: ' . $note['updated_at'] . "\n\n";
            echo strip_tags(str_replace(['<br>', '<br/>', '<br />', '</div>', '</p>', '</li>', '</h1>', '</h2>', '</h3>'], "\n", $note['content']));
        }
        exit;
    }

    // ── AJAX: Delete (soft delete) ────────────────────────────────────────

    public function delete(): void
    {
        $this->requireAuth();
        $this->verifyCsrf();

        $user   = $this->currentUser();
        $noteId = (int)($_POST['note_id'] ?? 0);
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

        if ($noteId === 0) {
            $isAjax
                ? $this->json(['success' => false, 'message' => 'Invalid note ID.'])
                : $this->redirect('/');
        }

        $noteModel    = new Note();
        $historyModel = new NoteHistory();
        $deleted      = $noteModel->softDelete($noteId, $user['id']);

        if ($deleted) {
            $historyModel->log($noteId, $user['id'], 'deleted', 'Note deleted by user.');
        }

        $isAjax
            ? $this->json(['success' => $deleted])
            : $this->redirect('/');
    }
}
