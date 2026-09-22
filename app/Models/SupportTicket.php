<?php
namespace App\Models;

use App\Helpers\Database;

class SupportTicket
{
    private Database $db;
    private string $table = 'support_tickets';

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

    public function getByUserId(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    public function getAll(int $page = 1, int $perPage = 20, string $status = '', string $search = ''): array
    {
        $where = ['1=1'];
        $params = [];

        if ($status) {
            $where[] = 'status = ?';
            $params[] = $status;
        }
        if ($search) {
            $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR order_number LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$whereClause}", $params)['cnt'];

        $sql = "SELECT * FROM {$this->table}
                WHERE {$whereClause}
                ORDER BY
                    CASE status WHEN 'open' THEN 0 WHEN 'replied' THEN 1 ELSE 2 END,
                    created_at DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return [
            'tickets'     => $this->db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function updateStatus(int $id, string $status): int
    {
        return $this->db->update($this->table, ['status' => $status], 'id = ?', [$id]);
    }

    public function countByStatus(string $status): int
    {
        return $this->db->count($this->table, 'status = ?', [$status]);
    }

    public function delete(int $id): int
    {
        $this->db->delete('support_replies', 'ticket_id = ?', [$id]);
        return $this->db->delete($this->table, 'id = ?', [$id]);
    }

    public function addReply(int $ticketId, string $sender, string $senderName, string $message): int
    {
        return $this->db->insert('support_replies', [
            'ticket_id'   => $ticketId,
            'sender'      => $sender,
            'sender_name' => $senderName,
            'message'     => $message,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function getReplies(int $ticketId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM support_replies WHERE ticket_id = ? ORDER BY created_at ASC",
            [$ticketId]
        );
    }
}
