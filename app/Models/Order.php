<?php
namespace App\Models;

use App\Helpers\Database;

class Order
{
    private Database $db;
    private string $table = 'orders';

    const STATUSES = [
        'pending'           => ['label' => 'Pending',           'color' => 'bg-warning text-dark', 'icon' => 'bi-clock'],
        'confirmed'         => ['label' => 'Confirmed',         'color' => 'bg-info',              'icon' => 'bi-check-circle'],
        'processing'        => ['label' => 'Processing',        'color' => 'bg-primary',           'icon' => 'bi-gear'],
        'shipped'           => ['label' => 'Shipped',           'color' => 'bg-info',              'icon' => 'bi-truck'],
        'out_for_delivery'  => ['label' => 'Out for Delivery',  'color' => 'bg-warning text-dark', 'icon' => 'bi-box-seam'],
        'delivered'         => ['label' => 'Delivered',         'color' => 'bg-success',           'icon' => 'bi-check-circle-fill'],
        'cancelled'         => ['label' => 'Cancelled',         'color' => 'bg-danger',            'icon' => 'bi-x-circle'],
        'returned'          => ['label' => 'Returned',          'color' => 'bg-secondary',         'icon' => 'bi-arrow-return-left'],
        'refunded'          => ['label' => 'Refunded',          'color' => 'bg-info',              'icon' => 'bi-cash'],
        'failed'            => ['label' => 'Failed',            'color' => 'bg-danger',            'icon' => 'bi-exclamation-circle'],
    ];

    const NORMAL_FLOW = ['pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery', 'delivered'];

    const PAYMENT_STATUSES = ['pending', 'paid', 'failed', 'refunded'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public static function getStatusInfo(string $status): array
    {
        return self::STATUSES[$status] ?? ['label' => ucfirst($status), 'color' => 'bg-secondary', 'icon' => 'bi-circle'];
    }

    public static function getAllStatuses(): array
    {
        return self::STATUSES;
    }

    public static function canCancel(string $status): bool
    {
        return in_array($status, ['pending', 'confirmed']);
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

    public function updateTransactionId(int $id, string $transactionId): int
    {
        return $this->db->update($this->table, ['transaction_id' => $transactionId], 'id = ?', [$id]);
    }

    public function getByTransactionId(string $transactionId): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE transaction_id = ?", [$transactionId]);
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

    public function getByUserId(int $userId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
             FROM {$this->table} o WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function getOrderStatusCounts(): array
    {
        $result = $this->db->fetchAll(
            "SELECT order_status, COUNT(*) as count FROM {$this->table} GROUP BY order_status"
        );
        $counts = [];
        foreach (array_keys(self::STATUSES) as $s) {
            $counts[$s] = 0;
        }
        foreach ($result as $row) {
            $counts[$row['order_status']] = (int) $row['count'];
        }
        return $counts;
    }

    public function getStockReport(): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.sku, p.stock_quantity,
                    COALESCE(SUM(CASE WHEN o.order_status != 'cancelled' THEN oi.quantity ELSE 0 END), 0) as total_sold,
                    p.stock_quantity + COALESCE(SUM(CASE WHEN o.order_status != 'cancelled' THEN oi.quantity ELSE 0 END), 0) as original_stock
             FROM products p
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             WHERE p.status = 1
             GROUP BY p.id
             ORDER BY p.stock_quantity ASC
             LIMIT 20"
        );
    }

    public function getNewCustomers(int $days = 7): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.name, u.email, u.phone, u.created_at
             FROM users u
             WHERE u.role_id = 4 AND u.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY u.created_at DESC LIMIT 10",
            [$days]
        );
    }

    public function getEvaluationMetrics(): array
    {
        $totalOrders = $this->countAll();
        $completedOrders = $this->countByStatus('completed');
        $totalRevenue = (float) ($this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table} WHERE payment_status = 'paid'"
        )['total'] ?? 0);

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / max($completedOrders, 1) : 0;
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        $lastMonthRevenue = (float) ($this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table}
             WHERE payment_status = 'paid' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        )['total'] ?? 0);
        $prevMonthRevenue = (float) ($this->db->fetch(
            "SELECT COALESCE(SUM(total_amount), 0) as total FROM {$this->table}
             WHERE payment_status = 'paid' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND created_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)"
        )['total'] ?? 0);

        $revenueGrowth = $prevMonthRevenue > 0 ? round((($lastMonthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1) : 0;

        return [
            'total_revenue'     => $totalRevenue,
            'avg_order_value'   => round($avgOrderValue, 2),
            'completion_rate'   => $completionRate,
            'revenue_growth'    => $revenueGrowth,
            'total_orders'      => $totalOrders,
            'completed_orders'  => $completedOrders,
        ];
    }
}
