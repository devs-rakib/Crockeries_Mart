<?php
namespace App\Models;

use App\Helpers\Database;

class Banner
{
    private Database $db;
    private string $table = 'banners_sliders';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getActive(string $type = ''): array
    {
        $where = 'status = 1';
        $params = [];
        if ($type) {
            $where .= ' AND type = ?';
            $params[] = $type;
        }
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY position ASC",
            $params
        );
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

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY position ASC");
    }
}
