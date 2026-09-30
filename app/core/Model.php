<?php
declare(strict_types=1);

/**
 * Model — base class for all models.
 * Provides a thin PDO wrapper with prepared-statement helpers.
 */
class Model
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Execute a prepared statement and return the PDOStatement.
     */
    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row or null.
     */
    protected function one(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    protected function all(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Return last auto-increment ID.
     */
    protected function lastId(): int
    {
        return (int)$this->db->lastInsertId();
    }
}
