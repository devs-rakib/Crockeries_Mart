<?php
namespace App\Models;

use App\Helpers\Database;
use App\Helpers\Auth;

class User
{
    private Database $db;
    private string $table = 'users';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByEmail(string $email): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE email = ?", [$email]);
    }

    public function getByPhone(string $phone): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE phone = ?", [$phone]);
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function create(array $data): int
    {
        return $this->db->insert($this->table, $data);
    }

    public function createAuto(string $name, string $phone, ?string $email = null): int
    {
        return $this->db->insert($this->table, [
            'name'       => $name,
            'phone'      => $phone,
            'email'      => $email,
            'password'   => Auth::hashPassword(bin2hex(random_bytes(16))),
            'role'       => 'customer',
            'status'     => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
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
            $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereClause = implode(' AND ', $where);
        $offset = ($page - 1) * $perPage;

        $total = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$whereClause}", $params)['cnt'];

        $sql = "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";

        return [
            'users'       => $this->db->fetchAll($sql, $params),
            'total'       => (int) $total,
            'page'        => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function countAll(): int
    {
        return $this->db->count($this->table, "role = 'customer'");
    }

    public function toggleStatus(int $id): int
    {
        $user = $this->getById($id);
        if (!$user) return 0;
        $newStatus = $user['status'] == 1 ? 0 : 1;
        return $this->db->update($this->table, ['status' => $newStatus], 'id = ?', [$id]);
    }

    public function setResetToken(string $email): ?string
    {
        $user = $this->getByEmail($email);
        if (!$user) return null;

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY_MINUTES * 60);

        $this->db->insert('password_resets', [
            'email'      => $email,
            'token'      => hash('sha256', $token),
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public function getByResetToken(string $token): ?array
    {
        $hashedToken = hash('sha256', $token);
        return $this->db->fetch(
            "SELECT pr.*, u.id as user_id, u.name, u.email
             FROM password_resets pr
             JOIN users u ON u.email = pr.email
             WHERE pr.token = ? AND pr.expires_at > NOW()
             ORDER BY pr.id DESC LIMIT 1",
            [$hashedToken]
        );
    }

    public function clearResetToken(string $email): void
    {
        $this->db->delete('password_resets', 'email = ?', [$email]);
    }

    public function updateProfile(int $id, array $data): int
    {
        return $this->db->update($this->table, $data, 'id = ?', [$id]);
    }

    public function updatePassword(int $id, string $newPassword): int
    {
        return $this->db->update($this->table, [
            'password' => Auth::hashPassword($newPassword),
        ], 'id = ?', [$id]);
    }

    public function isProfileIncomplete(array $user): bool
    {
        return str_starts_with($user['name'], 'User ')
            || empty($user['email'])
            || empty($user['address']);
    }

    public function getRecentUsers(int $days = 7): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, email, phone, created_at
             FROM {$this->table}
             WHERE role = 'customer' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY created_at DESC LIMIT 10",
            [$days]
        );
    }
}
