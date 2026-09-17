<?php
namespace App\Models;

use App\Helpers\Database;

class Setting
{
    private Database $db;
    private string $table = 'site_settings';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function get(string $key, string $default = ''): string
    {
        $result = $this->db->fetch(
            "SELECT setting_value FROM {$this->table} WHERE setting_key = ?",
            [$key]
        );
        return $result['setting_value'] ?? $default;
    }

    public function set(string $key, string $value): bool
    {
        $exists = $this->db->fetch(
            "SELECT id FROM {$this->table} WHERE setting_key = ?",
            [$key]
        );

        if ($exists) {
            return $this->db->update($this->table, ['setting_value' => $value], 'setting_key = ?', [$key]) > 0;
        } else {
            return $this->db->insert($this->table, ['setting_key' => $key, 'setting_value' => $value]) > 0;
        }
    }

    public function getAll(): array
    {
        $results = $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY id ASC");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public function updateMultiple(array $data): bool
    {
        $success = true;
        foreach ($data as $key => $value) {
            if ($this->set($key, $value) === false) {
                $success = false;
            }
        }
        return $success;
    }
}
