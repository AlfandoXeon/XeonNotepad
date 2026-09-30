<?php
declare(strict_types=1);

/**
 * HistoryController — displays the change history timeline for a note.
 */
class HistoryController extends Controller
{
    public function index(int $noteId): void
    {
        $this->requireAuth();
        $user      = $this->currentUser();
        $noteModel = new Note();
        $note      = $noteModel->findById($noteId, $user['id']);

        // Redirect if note doesn't exist or doesn't belong to user
        if (!$note) {
            $this->redirect('/');
        }

        $history = (new NoteHistory())->getByNote($noteId, $user['id']);
        $notes   = $noteModel->getAllByUser($user['id']);

        $this->view('main', 'notes/history', [
            'pageTitle'   => 'History: ' . htmlspecialchars($note['title']) . ' — ' . APP_NAME,
            'note'        => $note,
            'history'     => $history,
            'notes'       => $notes,
            'currentUser' => $user,
            'csrfToken'   => $this->generateCsrf(),
            'activeNote'  => $noteId,
        ]);
    }
}
