<?php
declare(strict_types=1);

/**
 * Note model — CRUD for notes with transparent AES-256-GCM encryption.
 *
 * All title and content data is encrypted before INSERT/UPDATE and
 * decrypted on SELECT. The raw encrypted columns are never exposed
 * outside this class. Even the database owner cannot read note contents.
 */
class Note extends Model
{
    // ── Read ──────────────────────────────────────────────────────────────

    /**
     * Return all non-deleted notes for a user, newest first.
     * Each row has decrypted 'title', 'content', and 'preview' fields.
     */
    public function getAllByUser(int $userId): array
    {
        $rows = $this->all(
            'SELECT * FROM notes WHERE user_id = ? AND is_deleted = 0 ORDER BY updated_at DESC',
            [$userId]
        );
        return array_map([$this, 'decryptRow'], $rows);
    }

    /**
     * Find a single note by ID and owner, or null if not found / wrong owner.
     */
    public function findById(int $id, int $userId): ?array
    {
        $row = $this->one(
            'SELECT * FROM notes WHERE id = ? AND user_id = ? AND is_deleted = 0 LIMIT 1',
            [$id, $userId]
        );
        return $row ? $this->decryptRow($row) : null;
    }

    /**
     * Search notes by title and content (client-side decryption search).
     * Returns all matching notes, or all notes if query is empty.
     */
    public function searchByUser(int $userId, string $query): array
    {
        $all = $this->getAllByUser($userId);
        if ($query === '') return $all;

        $q = mb_strtolower($query);
        return array_values(array_filter($all, static function (array $note) use ($q): bool {
            return str_contains(mb_strtolower($note['title']), $q)
                || str_contains(mb_strtolower($note['preview']), $q);
        }));
    }

    // ── Write ─────────────────────────────────────────────────────────────

    /**
     * Create a new note. Returns the new note's ID.
     */
    public function create(int $userId, string $title, string $content): int
    {
        $encTitle   = Encryption::encrypt($title ?: 'Untitled Note');
        $encContent = Encryption::encrypt($content);

        $this->query(
            'INSERT INTO notes (user_id, title_enc, content_enc, title_iv, content_iv) VALUES (?, ?, ?, ?, ?)',
            [
                $userId,
                $encTitle['ciphertext'],
                $encContent['ciphertext'],
                $encTitle['iv'],
                $encContent['iv'],
            ]
        );
        return $this->lastId();
    }

    /**
     * Update title and content for an existing note.
     * Returns true if a row was actually modified.
     */
    public function update(int $id, int $userId, string $title, string $content): bool
    {
        $encTitle   = Encryption::encrypt($title ?: 'Untitled Note');
        $encContent = Encryption::encrypt($content);

        $stmt = $this->query(
            'UPDATE notes
             SET title_enc   = ?,
                 content_enc = ?,
                 title_iv    = ?,
                 content_iv  = ?,
                 updated_at  = NOW()
             WHERE id = ? AND user_id = ? AND is_deleted = 0',
            [
                $encTitle['ciphertext'],
                $encContent['ciphertext'],
                $encTitle['iv'],
                $encContent['iv'],
                $id,
                $userId,
            ]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Soft-delete a note (sets is_deleted = 1).
     */
    public function softDelete(int $id, int $userId): bool
    {
        $stmt = $this->query(
            'UPDATE notes SET is_deleted = 1, updated_at = NOW() WHERE id = ? AND user_id = ?',
            [$id, $userId]
        );
        return $stmt->rowCount() > 0;
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function decryptRow(array $row): array
    {
        $row['title']   = Encryption::decrypt($row['title_enc'],   $row['title_iv']);
        $row['content'] = Encryption::decrypt($row['content_enc'], $row['content_iv']);
        $plain          = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</div>', '</p>', '</li>'], ' ', $row['content']));
        $row['preview'] = mb_substr(preg_replace('/\s+/', ' ', trim($plain)), 0, 120);
        return $row;
    }
}
