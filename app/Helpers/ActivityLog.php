<?php
namespace App\Helpers;

use App\Helpers\Database;

class ActivityLog
{
    public static function log(string $action, ?string $target = null, ?string $details = null, ?int $userId = null): void
    {
        try {
            $db = Database::getInstance();
            $db->insert('activity_logs', [
                'user_id'    => $userId ?? Auth::id(),
                'action'     => $action,
                'target'     => $target,
                'details'    => $details,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            error_log('ActivityLog error: ' . $e->getMessage());
        }
    }

    public static function getRecent(int $limit = 50, ?int $userId = null): array
    {
        $db = Database::getInstance();
        $sql = "SELECT al.*, u.name as user_name
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id";

        $params = [];
        if ($userId) {
            $sql .= " WHERE al.user_id = ?";
            $params[] = $userId;
        }

        $sql .= " ORDER BY al.created_at DESC LIMIT {$limit}";

        return $db->fetchAll($sql, $params);
    }

    public static function getByUser(int $userId, int $limit = 50): array
    {
        return self::getRecent($limit, $userId);
    }

    public static function getForAdmin(int $page = 1, int $perPage = 30, string $search = ''): array
    {
        $db = Database::getInstance();
        $where = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(al.action LIKE ? OR al.target LIKE ? OR al.details LIKE ? OR u.name LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $db->fetch(
            "SELECT COUNT(*) as cnt FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id WHERE {$whereClause}",
            $params
        )['cnt'];

        $sql = "SELECT al.*, u.name as user_name, u.email as user_email
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE {$whereClause}
                ORDER BY al.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'logs'        => $db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }
}
