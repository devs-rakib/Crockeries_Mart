<?php
namespace App\Controllers\admin;

use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\ActivityLog;

class ActivityLogController
{
    public function __construct()
    {
        Auth::requirePermission('view_activity_logs');
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $search = Sanitizer::clean($_GET['search'] ?? '');

            $result = ActivityLog::getForAdmin($page, 30, $search);

            $data = [
                'pageTitle'  => 'Activity Logs',
                'logs'       => $result['logs'],
                'total'      => $result['total'],
                'page'       => $result['page'],
                'totalPages' => $result['total_pages'],
                'search'     => $search,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/activity_logs/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Activity logs error: " . $e->getMessage());
            Session::flash('error', 'Failed to load activity logs');
            Response::redirect(APP_URL . '/admin/dashboard');
        }
    }
}
