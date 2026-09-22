<?php
namespace App\Helpers;

class Auth
{
    private static ?array $permissions = null;

    const ROLE_ADMIN    = 1;
    const ROLE_MANAGER  = 2;
    const ROLE_STAFF    = 3;
    const ROLE_USER     = 4;

    const ROLE_NAMES = [
        self::ROLE_ADMIN   => 'Admin',
        self::ROLE_MANAGER => 'Manager',
        self::ROLE_STAFF   => 'Staff',
        self::ROLE_USER    => 'User',
    ];

    public static function login(array $user): void
    {
        Session::set('user_id', $user['id']);
        Session::set('user_name', $user['name']);
        Session::set('user_email', $user['email']);
        Session::set('user_role_id', (int) $user['role_id']);
        Session::set('logged_in', true);
        self::loadPermissions((int) $user['role_id']);
        Session::regenerate();
    }

    public static function logout(): void
    {
        self::$permissions = null;
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::get('logged_in', false) === true && Session::has('user_role_id');
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function name(): string
    {
        return Session::get('user_name', 'Guest');
    }

    public static function email(): ?string
    {
        return Session::get('user_email');
    }

    public static function role(): int
    {
        return (int) Session::get('user_role_id', self::ROLE_USER);
    }

    public static function roleName(): string
    {
        return self::ROLE_NAMES[self::role()] ?? 'Unknown';
    }

    public static function admin(): bool
    {
        return self::check() && self::role() === self::ROLE_ADMIN;
    }

    public static function manager(): bool
    {
        return self::check() && self::role() === self::ROLE_MANAGER;
    }

    public static function staff(): bool
    {
        return self::check() && self::role() === self::ROLE_STAFF;
    }

    public static function isManagement(): bool
    {
        return self::check() && in_array(self::role(), [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_STAFF]);
    }

    public static function hasPermission(string $permission): bool
    {
        if (!self::check()) return false;

        if (self::admin()) return true;

        if (self::$permissions === null) {
            self::loadPermissions(self::role());
        }

        return in_array($permission, self::$permissions ?? []);
    }

    public static function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (self::hasPermission($perm)) return true;
        }
        return false;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please login first');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::admin()) {
            Session::flash('error', 'Access denied');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function requireRole(int ...$roles): void
    {
        if (!self::check() || !in_array(self::role(), $roles)) {
            Session::flash('error', 'Access denied');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function requirePermission(string $permission): void
    {
        if (!self::hasPermission($permission)) {
            Session::flash('error', 'You do not have permission to access this page');
            header('Location: ' . APP_URL . '/admin/dashboard');
            exit;
        }
    }

    public static function getDashboardUrl(): string
    {
        if (!self::check()) return APP_URL . '/login';

        return match(self::role()) {
            self::ROLE_ADMIN   => APP_URL . '/admin/dashboard',
            self::ROLE_MANAGER => APP_URL . '/admin/dashboard',
            self::ROLE_STAFF   => APP_URL . '/admin/dashboard',
            default            => APP_URL,
        };
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    private static function loadPermissions(int $roleId): void
    {
        if ($roleId === self::ROLE_ADMIN) {
            self::$permissions = ['*'];
            return;
        }

        try {
            $db = Database::getInstance();
            $rows = $db->fetchAll(
                "SELECT p.name FROM permissions p
                 INNER JOIN role_permissions rp ON rp.permission_id = p.id
                 WHERE rp.role_id = ?",
                [$roleId]
            );
            self::$permissions = array_column($rows, 'name');
        } catch (\Throwable $e) {
            self::$permissions = [];
        }
    }
}
