<?php
namespace App\Models;

use App\Helpers\Database;

class Role
{
    private Database $db;
    private string $table = 'roles';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY id ASC");
    }

    public function getById(int $id): ?array
    {
        return $this->db->fetch("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }

    public function getPermissions(int $roleId): array
    {
        return $this->db->fetchAll(
            "SELECT p.* FROM permissions p
             INNER JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?
             ORDER BY p.id ASC",
            [$roleId]
        );
    }

    public function getPermissionNames(int $roleId): array
    {
        $perms = $this->getPermissions($roleId);
        return array_column($perms, 'name');
    }

    public function getAllPermissions(): array
    {
        return $this->db->fetchAll("SELECT * FROM permissions ORDER BY id ASC");
    }

    public function assignPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->delete('role_permissions', 'role_id = ?', [$roleId]);
        foreach ($permissionIds as $permId) {
            $this->db->insert('role_permissions', [
                'role_id'       => $roleId,
                'permission_id' => (int) $permId,
            ]);
        }
    }

    public function countUsers(int $roleId): int
    {
        return $this->db->count('users', 'role_id = ?', [$roleId]);
    }

    public function getRoleName(int $roleId): string
    {
        $role = $this->getById($roleId);
        return $role ? $role['name'] : 'Unknown';
    }
}
