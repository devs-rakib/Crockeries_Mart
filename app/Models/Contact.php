<?php
namespace App\Models;

use App\Helpers\Database;

class Contact
{
    private Database $db;
    private string $table = 'contacts';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count($this->table, '1=1');

        $contacts = $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}"
        );

        return [
            'contacts'    => $contacts,
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function markAsRead(int $id): bool
    {
        return $this->db->update($this->table, ['status' => 1], 'id = ?', [$id]) > 0;
    }

    public function delete(int $id): int
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function countUnread(): int
    {
        return $this->db->count($this->table, 'status = 0');
    }
}
