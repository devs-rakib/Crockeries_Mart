<?php
namespace App\Models;

use App\Helpers\Database;

class Review
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByProduct(int $productId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.name as user_name
             FROM reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.product_id = ? AND r.status = 1
             ORDER BY r.created_at DESC",
            [$productId]
        );
    }

    public function getAverageRating(int $productId): float
    {
        $result = $this->db->fetch(
            "SELECT COALESCE(AVG(rating), 0) as avg_rating, COUNT(*) as total_reviews
             FROM reviews WHERE product_id = ? AND status = 1",
            [$productId]
        );
        return round((float) ($result['avg_rating'] ?? 0), 1);
    }

    public function getRatingDistribution(int $productId): array
    {
        $dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $results = $this->db->fetchAll(
            "SELECT rating, COUNT(*) as cnt FROM reviews WHERE product_id = ? AND status = 1 GROUP BY rating",
            [$productId]
        );
        foreach ($results as $row) {
            $dist[(int) $row['rating']] = (int) $row['cnt'];
        }
        return $dist;
    }

    public function create(array $data): int
    {
        return $this->db->insert('reviews', $data);
    }

    public function countAll(): int
    {
        return $this->db->count('reviews');
    }
}
