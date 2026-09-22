<?php
namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Cache;

class Product
{
    private Database $db;
    private string $table = 'products';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE, string $sort = 'newest'): array
    {
        $where = ['p.status = 1'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = ?';
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = ?';
            $params[] = $filters['brand_id'];
        }
        if (!empty($filters['min_price'])) {
            $where[] = 'COALESCE(p.discount_price, p.price) >= ?';
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where[] = 'COALESCE(p.discount_price, p.price) <= ?';
            $params[] = $filters['max_price'];
        }
        if (!empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.short_description LIKE ?)';
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        if (!empty($filters['is_featured'])) {
            $where[] = 'p.is_featured = 1';
        }
        if (!empty($filters['is_offer'])) {
            $where[] = 'p.is_offer = 1';
        }

        $orderBy = match($sort) {
            'price_low'  => 'COALESCE(p.discount_price, p.price) ASC',
            'price_high' => 'COALESCE(p.discount_price, p.price) DESC',
            'popular'    => 'p.views_count DESC',
            'oldest'     => 'p.created_at ASC',
            default      => 'p.created_at DESC',
        };

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as cnt FROM {$this->table} p WHERE {$whereClause}";
        $total = $this->db->fetch($countSql, $params)['cnt'];

        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        $products = $this->db->fetchAll($sql, $params);

        return [
            'products'    => $products,
            'total'       => (int) $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function getById(int $id): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getBySlug(string $slug): ?array
    {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.slug = ? AND p.status = 1";
        return $this->db->fetch($sql, [$slug]);
    }

    public function getImages(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC",
            [$productId]
        );
    }

    public function getVariants(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? ORDER BY id ASC",
            [$productId]
        );
    }

    public function incrementViews(int $id): void
    {
        $this->db->query("UPDATE {$this->table} SET views_count = views_count + 1 WHERE id = ?", [$id]);
    }

    public function getRelated(int $productId, int $categoryId, int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.id != ? AND p.status = 1
             ORDER BY RAND() LIMIT ?",
            [$categoryId, $productId, $limit]
        );
    }

    public function getFeatured(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.is_featured = 1 AND p.status = 1
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getTopSelling(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, COALESCE(SUM(oi.quantity), 0) as total_sold
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN order_items oi ON p.id = oi.product_id
             WHERE p.status = 1
             GROUP BY p.id
             ORDER BY total_sold DESC LIMIT ?",
            [$limit]
        );
    }

    public function getNewArrivals(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = 1
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getBestDiscount(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
             ROUND(((p.price - p.discount_price) / p.price) * 100) as discount_percent
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.discount_price IS NOT NULL AND p.discount_price > 0 AND p.status = 1
             ORDER BY discount_percent DESC LIMIT ?",
            [$limit]
        );
    }

    public function getTodaysDeals(int $limit = 6): array
    {
        $deals = $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
             ROUND(((p.price - p.discount_price) / p.price) * 100) as discount_percent
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = 1 AND (p.is_offer = 1 OR (p.discount_price IS NOT NULL AND p.discount_price > 0))
             ORDER BY p.is_offer DESC, COALESCE(discount_percent, 0) DESC, p.views_count DESC
             LIMIT ?",
            [$limit]
        );

        if (count($deals) < $limit) {
            $existingIds = !empty($deals) ? array_column($deals, 'id') : [0];
            $placeholders = implode(',', array_fill(0, count($existingIds), '?'));
            $needed = $limit - count($deals);
            $params = array_merge($existingIds, [$needed]);
            $extra = $this->db->fetchAll(
                "SELECT p.*, c.name as category_name,
                 0 as discount_percent
                 FROM {$this->table} p
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE p.status = 1 AND p.id NOT IN ({$placeholders})
                 ORDER BY p.views_count DESC, p.created_at DESC
                 LIMIT ?",
                $params
            );
            $deals = array_merge($deals, $extra);
        }

        return $deals;
    }

    public function getByCategory(int $categoryId, int $limit = 8): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ? AND p.status = 1
             ORDER BY p.created_at DESC LIMIT ?",
            [$categoryId, $limit]
        );
    }

    public function search(string $query, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.slug, p.main_image, p.price, p.discount_price, c.name as category_name
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.status = 1 AND (p.name LIKE ? OR p.sku LIKE ?)
             ORDER BY p.views_count DESC LIMIT ?",
            ["%{$query}%", "%{$query}%", $limit]
        );
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
            $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} p WHERE {$whereClause}", $params)['cnt'];

        $sql = "SELECT p.*, c.name as category_name, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE {$whereClause}
                ORDER BY p.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'products'    => $this->db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function countAll(): int
    {
        return $this->db->count($this->table, 'status = 1');
    }

    public function getTotalRevenue(): float
    {
        $result = $this->db->fetch("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE payment_status = 'paid'");
        return (float) ($result['total'] ?? 0);
    }

    public function getOfferProducts(int $limit = 12): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name,
             ROUND(((p.price - p.discount_price) / p.price) * 100) as discount_percent
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.is_offer = 1 AND p.status = 1
             ORDER BY discount_percent DESC, p.views_count DESC
             LIMIT ?",
            [$limit]
        );
    }
}
