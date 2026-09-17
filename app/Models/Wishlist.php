<?php
namespace App\Models;

use App\Helpers\Database;

class Wishlist
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function toggle(int $userId, int $productId): bool
    {
        $existing = $this->db->fetch(
            "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        if ($existing) {
            $this->db->delete('wishlists', 'id = ?', [$existing['id']]);
            return false;
        } else {
            $this->db->insert('wishlists', [
                'user_id'    => $userId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            return true;
        }
    }

    public function isWishlisted(int $userId, int $productId): bool
    {
        $result = $this->db->fetch(
            "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        return $result !== null;
    }

    public function getCount(int $userId): int
    {
        return $this->db->count('wishlists', 'user_id = ?', [$userId]);
    }

    public function getItems(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT w.*, p.name, p.slug, p.main_image, p.price, p.discount_price,
             c.name as category_name
             FROM wishlists w
             LEFT JOIN products p ON w.product_id = p.id
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE w.user_id = ? AND p.status = 1
             ORDER BY w.created_at DESC",
            [$userId]
        );
    }
}
