<?php
namespace App\Models;

use App\Helpers\Database;

class Order
{
    private Database $db;
    private string $table = 'orders';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        return $this->db->insert($this->table, $data);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function getByNumber(string $orderNumber): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE order_number = ?", [$orderNumber]);
    }

    public function getItems(int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name as product_name, p.main_image, p.slug as product_slug
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public function createItems(int $orderId, array $items): void
    {
        foreach ($items as $item) {
            $this->db->insert('order_items', [
                'order_id'   => $orderId,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal'   => $item['subtotal'],
            ]);
        }
    }

    public function generateOrderNumber(): string
    {
        $prefix = 'CM';
        $date = date('ymd');
        $lastOrder = $this->db->fetch(
            "SELECT order_number FROM {$this->table} WHERE order_number LIKE ? ORDER BY id DESC LIMIT 1",
            ["{$prefix}{$date}%"]
        );
        if ($lastOrder) {
            $lastNum = (int) substr($lastOrder['order_number'], -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }
        return $prefix . $date . $newNum;
    }

    public function updateStatus(int $id, string $status): int
    {
        return $this->db->update($this->table, ['order_status' => $status], 'id = ?', [$id]);
    }

    public function updatePaymentStatus(int $id, string $status): int
    {
        return $this->db->update($this->table, ['payment_status' => $status], 'id = ?', [$id]);
    }

    public function addStatusHistory(int $orderId, string $status, string $note = ''): void
    {
        $this->db->insert('order_status_history', [
            'order_id'   => $orderId,
            'status'     => $status,
            'note'       => $note,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getStatusHistory(int $orderId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at DESC",
            [$orderId]
        );
    }

    public function getAllAdmin(int $page = 1, int $perPage = 20, string $status = '', string $search = ''): array
    {
        $where = ['1=1'];
        $params = [];

        if ($status) {
            $where[] = 'o.order_status = ?';
            $params[] = $status;
        }
        if ($search) {
            $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.customer_phone LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} o WHERE {$whereClause}", $params)['cnt'];

        $sql = "SELECT o.* FROM {$this->table} o
                WHERE {$whereClause}
                ORDER BY o.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'orders'      => $this->db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function countAll(): int
    {
        return $this->db->count($this->table);
    }

    public function countByStatus(string $status): int
    {
        return $this->db->count($this->table, 'order_status = ?', [$status]);
    }

    public function getRecentOrders(int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getDailyRevenue(int $days = 7): array
    {
        return $this->db->fetchAll(
            "SELECT DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders
             FROM {$this->table}
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY) AND payment_status = 'paid'
             GROUP BY DATE(created_at) ORDER BY date ASC",
            [$days]
        );
    }

    public function delete(int $id): int
    {
        $this->db->delete('order_status_history', 'order_id = ?', [$id]);
        $this->db->delete('order_items', 'order_id = ?', [$id]);
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }
}
