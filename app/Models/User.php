<?php
namespace App\Models;

use App\Helpers\Database;

class User
{
    private Database $db;
    private string $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByEmail(string $email): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        return $this->db->insert($this->table, $data);
    }

    public function update(int $id, array $data): int
    {
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int
    {
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function getAllAdmin(int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $where = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$whereClause}", $params)['cnt'];

        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";

        return [
            'users'       => $this->db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function countAll(): int
    {
        return $this->db->count($this->table, "role = 'customer'");
    }

    public function toggleStatus(int $id): int
    {
        $user = $this->getById($id);
        if (!$user) return 0;
        $newStatus = $user['status'] == 1 ? 0 : 1;
        return $this->db->update($this->table, ['status' => $newStatus], 'id = ?', [$id]);
    }
}
