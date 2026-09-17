<?php
namespace App\Models;

use App\Helpers\Database;

class Category
{
    private Database $db;
    private string $table = 'categories';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status = 1) as product_count
             FROM {$this->table} c WHERE c.status = 1 ORDER BY c.position ASC, c.name ASC"
        );
    }

    public function getFeatured(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status = 1) as product_count
             FROM {$this->table} c WHERE c.is_featured = 1 AND c.status = 1 ORDER BY c.position ASC"
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

    public function getChildren(int $parentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE parent_id = ? AND status = 1 ORDER BY position ASC",
            [$parentId]
        );
    }

    public function getTree(): array
    {
        $all = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE status = 1 ORDER BY position ASC, name ASC"
        );

        $tree = [];
        $lookup = [];
        foreach ($all as $cat) {
            $cat['children'] = [];
            $lookup[$cat['id']] = $cat;
        }
        foreach ($lookup as $cat) {
            if ($cat['parent_id'] && isset($lookup[$cat['parent_id']])) {
                $lookup[$cat['parent_id']]['children'][] = &$lookup[$cat['id']];
            } else {
                $tree[] = &$lookup[$cat['id']];
            }
        }
        return $tree;
    }

    public function getSidebarTree(): array
    {
        $categories = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id AND status = 1) as product_count,
             (SELECT COUNT(*) FROM categories WHERE parent_id = c.id AND status = 1) as has_children
             FROM {$this->table} c WHERE c.parent_id IS NULL AND c.status = 1 ORDER BY c.position ASC"
        );

        foreach ($categories as &$cat) {
            $cat['children'] = $this->getChildren($cat['id']);
        }
        return $categories;
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
        $childCount = $this->db->count($this->table, 'parent_id = ?', [$id]);
        if ($childCount > 0) {
            return 0;
        }
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function getAllAdmin(): array
    {
        return $this->db->fetchAll(
            "SELECT c.*, p.name as parent_name, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
             FROM {$this->table} c
             LEFT JOIN categories p ON c.parent_id = p.id
             ORDER BY c.position ASC, c.name ASC"
        );
    }

    public function countAll(): int
    {
        return $this->db->count($this->table, 'status = 1');
    }
}
