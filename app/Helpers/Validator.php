<?php
namespace App\Helpers;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (empty($this->data[$field]) && $this->data[$field] !== '0') {
            $this->errors[$field] = "{$label} is required";
        }
        return $this;
    }

    public function email(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} must be a valid email";
        }
        return $this;
    }

    public function phone(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && !preg_match('/^(\+88)?01[3-9]\d{8}$/', $this->data[$field])) {
            $this->errors[$field] = "{$label} must be a valid Bangladeshi phone number";
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $min) {
            $this->errors[$field] = "{$label} must be at least {$min} characters";
        }
        return $this;
    }

    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters";
        }
        return $this;
    }

    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: $field;
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "{$label} must be a number";
        }
        return $this;
    }

    public function min(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: $field;
        if (isset($this->data[$field]) && $this->data[$field] < $min) {
            $this->errors[$field] = "{$label} must be at least {$min}";
        }
        return $this;
    }

    public function unique(string $field, string $table, string $column = '', int $exceptId = 0, string $label = ''): self
    {
        $label = $label ?: $field;
        $column = $column ?: $field;
        if (!empty($this->data[$field])) {
            $db = Database::getInstance();
            $sql = "SELECT COUNT(*) as cnt FROM {$table} WHERE {$column} = ?";
            $params = [$this->data[$field]];
            if ($exceptId > 0) {
                $sql .= " AND id != ?";
                $params[] = $exceptId;
            }
            $result = $db->fetch($sql, $params);
            if ($result['cnt'] > 0) {
                $this->errors[$field] = "{$label} already exists";
            }
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
