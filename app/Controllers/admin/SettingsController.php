<?php
namespace App\Controllers\admin;

use App\Models\Setting;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;

class SettingsController
{
    private Setting $settingModel;
    private string $backupDir;

    public function __construct()
    {
        Auth::requirePermission('manage_settings');
        $this->settingModel = new Setting();
        $this->backupDir = APP_ROOT . '/storage/backups';
    }

    public function index(): void
    {
        try {
            $settings = $this->settingModel->getAll();
            $backups = $this->getBackups();

            $data = [
                'pageTitle' => 'Site Settings',
                'settings'  => $settings,
                'backups'   => $backups,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/settings/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Settings error: " . $e->getMessage());
            Session::flash('error', 'Failed to load settings');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function update(): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $settingsData = [
                'site_name'         => Sanitizer::clean($_POST['site_name'] ?? ''),
                'site_email'        => Sanitizer::clean($_POST['site_email'] ?? ''),
                'site_phone'        => Sanitizer::clean($_POST['site_phone'] ?? ''),
                'site_address'      => Sanitizer::clean($_POST['site_address'] ?? ''),
                'shipping_inside'   => (float) ($_POST['shipping_inside'] ?? 60),
                'shipping_outside'  => (float) ($_POST['shipping_outside'] ?? 120),
                'site_facebook'     => Sanitizer::clean($_POST['site_facebook'] ?? '#'),
                'site_youtube'      => Sanitizer::clean($_POST['site_youtube'] ?? '#'),
                'site_instagram'    => Sanitizer::clean($_POST['site_instagram'] ?? '#'),
            ];

            $this->settingModel->updateMultiple($settingsData);

            Session::flash('success', 'Settings updated successfully');
            Response::redirect(APP_URL . '/admin/settings');
        } catch (\Throwable $e) {
            error_log("Settings update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating settings');
            Response::redirect(APP_URL . '/admin/settings');
        }
    }

    public function backup(): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin/settings');
                return;
            }

            if (!is_dir($this->backupDir)) {
                mkdir($this->backupDir, 0755, true);
            }

            $timestamp = date('Ymd_His');
            $filename = 'crockeriesmart_' . $timestamp . '.sql';
            $filepath = $this->backupDir . '/' . $filename;

            $host = DB_HOST;
            $name = DB_NAME;
            $user = DB_USER;
            $pass = DB_PASS;

            $cmd = sprintf(
                '"C:\\xampp\\mysql\\bin\\mysqldump.exe" --user=%s --password=%s --host=%s --single-transaction --routines --triggers --result-file="%s" %s 2>&1',
                escapeshellarg($user),
                escapeshellarg($pass),
                escapeshellarg($host),
                $filepath,
                escapeshellarg($name)
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
                $error = implode("\n", $output);
                error_log("Backup failed: " . $error);
                Session::flash('error', 'Backup failed: ' . ($error ?: 'Unknown error'));
                Response::redirect(APP_URL . '/admin/settings');
                return;
            }

            $this->cleanOldBackups(30);
            Session::flash('success', "Backup created: {$filename} (" . $this->formatSize(filesize($filepath)) . ")");
            Response::redirect(APP_URL . '/admin/settings');
        } catch (\Throwable $e) {
            error_log("Backup error: " . $e->getMessage());
            Session::flash('error', 'Failed to create backup');
            Response::redirect(APP_URL . '/admin/settings');
        }
    }

    public function download(string $filename): void
    {
        $filename = basename($filename);
        $filepath = $this->backupDir . '/' . $filename;

        if (!file_exists($filepath) || !str_ends_with($filename, '.sql')) {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
            return;
        }

        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache');
        readfile($filepath);
        exit;
    }

    public function deleteBackup(string $filename): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin/settings');
                return;
            }

            $filename = basename($filename);
            $filepath = $this->backupDir . '/' . $filename;

            if (file_exists($filepath) && str_ends_with($filename, '.sql')) {
                unlink($filepath);
                Session::flash('success', "Deleted: {$filename}");
            } else {
                Session::flash('error', 'Backup file not found');
            }
        } catch (\Throwable $e) {
            error_log("Delete backup error: " . $e->getMessage());
            Session::flash('error', 'Failed to delete backup');
        }

        Response::redirect(APP_URL . '/admin/settings');
    }

    private function getBackups(): array
    {
        $backups = [];

        if (!is_dir($this->backupDir)) {
            return $backups;
        }

        $files = glob($this->backupDir . '/crockeriesmart_*.sql');
        usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

        foreach ($files as $file) {
            $name = basename($file);
            $backups[] = [
                'name'     => $name,
                'size'     => filesize($file),
                'size_fmt' => $this->formatSize(filesize($file)),
                'date'     => date('d M Y, h:i A', filemtime($file)),
            ];
        }

        return $backups;
    }

    private function cleanOldBackups(int $days): void
    {
        $files = glob($this->backupDir . '/crockeriesmart_*.sql');
        $cutoff = time() - ($days * 86400);

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
