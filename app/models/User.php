<?php
declare(strict_types=1);

/**
 * User model — handles authentication and user account data.
 * Passwords are stored as bcrypt hashes (cost 12). Never stored in plaintext.
 */
class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        return $this->one(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->one(
            'SELECT id, username, email, created_at FROM users WHERE id = ? LIMIT 1',
            [$id]
        );
    }

    public function emailExists(string $email): bool
    {
        return $this->one(
            'SELECT id FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))]
        ) !== null;
    }

    public function usernameExists(string $username): bool
    {
        return $this->one(
            'SELECT id FROM users WHERE username = ? LIMIT 1',
            [trim($username)]
        ) !== null;
    }

    /**
     * Create a new user account. Returns the new user ID.
     */
    public function create(string $username, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->query(
            'INSERT INTO users (username, email, password) VALUES (?, ?, ?)',
            [trim($username), strtolower(trim($email)), $hash]
        );
        return $this->lastId();
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Update a user's password by hashing it anew.
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->query(
            'UPDATE users SET password = ? WHERE id = ?',
            [$hash, $userId]
        );
        return $stmt->rowCount() > 0;
    }

    /**
     * Find a user by email including the password hash (for verification).
     */
    public function findByEmailFull(string $email): ?array
    {
        return $this->one(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))]
        );
    }
}
