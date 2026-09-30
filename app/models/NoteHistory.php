<?php
declare(strict_types=1);

/**
 * NoteHistory model — encrypted audit log for note changes.
 *
 * Every create/edit/delete action is logged with an encrypted
 * human-readable description of what changed. History is owned
 * by both the note and the user (double FK check for security).
 */
class NoteHistory extends Model
{
    /**
     * Log a change to a note.
     *
     * @param int    $noteId      The note that was changed
     * @param int    $userId      The user who made the change
     * @param string $action      'created' | 'edited' | 'deleted'
     * @param string $description Human-readable description of the change
     */
    public function log(int $noteId, int $userId, string $action, string $description): void
    {
        $enc = Encryption::encrypt($description);
        $this->query(
            'INSERT INTO note_histories (note_id, user_id, action, diff_desc_enc, diff_desc_iv)
             VALUES (?, ?, ?, ?, ?)',
            [$noteId, $userId, $action, $enc['ciphertext'], $enc['iv']]
        );
    }

    /**
     * Get all history entries for a note, newest first.
     * Verifies note ownership through JOIN.
     *
     * @return array  Each entry has a decrypted 'description' field
     */
    public function getByNote(int $noteId, int $userId): array
    {
        $rows = $this->all(
            'SELECT nh.*
             FROM note_histories nh
             INNER JOIN notes n ON n.id = nh.note_id
             WHERE nh.note_id = ?
               AND n.user_id  = ?
             ORDER BY nh.created_at DESC',
            [$noteId, $userId]
        );

        return array_map(static function (array $row): array {
            $row['description'] = Encryption::decrypt($row['diff_desc_enc'], $row['diff_desc_iv']);
            return $row;
        }, $rows);
    }

    /**
     * Check if an 'edited' history log should be created or throttled.
     * Prevents database bloat during high-speed auto-saves.
     */
    public function shouldLogEdit(int $noteId, int $throttleSeconds = 45): bool
    {
        $row = $this->one(
            'SELECT created_at FROM note_histories
             WHERE note_id = ? AND action = "edited"
             ORDER BY id DESC LIMIT 1',
            [$noteId]
        );
        if (!$row) {
            return true;
        }
        return (time() - strtotime($row['created_at'])) >= $throttleSeconds;
    }
}

