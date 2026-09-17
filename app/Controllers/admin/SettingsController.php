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

    public function __construct()
    {
        Auth::requireAdmin();
        $this->settingModel = new Setting();
    }

    public function index(): void
    {
        try {
            $settings = $this->settingModel->getAll();

            $data = [
                'pageTitle' => 'Site Settings',
                'settings'  => $settings,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/settings/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            error_log("Settings update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating settings');
            Response::redirect(APP_URL . '/admin/settings');
        }
    }
}
