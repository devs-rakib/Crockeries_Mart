<?php
namespace App\Models;

use App\Helpers\Database;

class Brand
{
    private Database $db;
    private string $table = 'brands';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE status = 1 ORDER BY name ASC"
        );
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function getBySlug(string $slug): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE slug = ? AND status = 1", [$slug]);
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

    public function countAll(): int
    {
        return $this->db->count($this->table, 'status = 1');
    }
}
