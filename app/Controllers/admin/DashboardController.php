<?php
namespace App\Controllers\admin;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;

class DashboardController
{
    private Product $productModel;
    private Order $orderModel;
    private User $userModel;
    private Category $categoryModel;
    private Brand $brandModel;

    public function __construct()
    {
        Auth::requirePermission('view_dashboard');
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->userModel = new User();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
    }

    public function index(): void
    {
        try {
            $stats = [
                'total_products' => $this->productModel->countAll(),
                'total_orders' => $this->orderModel->countAll(),
                'total_users' => $this->userModel->countAll(),
                'total_revenue' => $this->productModel->getTotalRevenue(),
                'pending_orders' => $this->orderModel->countByStatus('pending'),
                'processing_orders' => $this->orderModel->countByStatus('processing'),
                'completed_orders' => $this->orderModel->countByStatus('completed'),
                'cancelled_orders' => $this->orderModel->countByStatus('cancelled'),
            ];

            $recentOrders = $this->orderModel->getRecentOrders(10);
            $dailyRevenue = $this->orderModel->getDailyRevenue(7);
            $orderStatusCounts = $this->orderModel->getOrderStatusCounts();
            $stockReport = $this->orderModel->getStockReport();
            $newCustomers = $this->orderModel->getNewCustomers(7);
            $metrics = $this->orderModel->getEvaluationMetrics();
            $categories = $this->categoryModel->countAll();
            $brands = $this->brandModel->countAll();

            $data = [
                'pageTitle' => 'Admin Dashboard',
                'totalProducts' => $stats['total_products'],
                'totalOrders' => $stats['total_orders'],
                'totalUsers' => $stats['total_users'],
                'totalRevenue' => $stats['total_revenue'],
                'pendingOrders' => $stats['pending_orders'],
                'processingOrders' => $stats['processing_orders'],
                'completedOrders' => $stats['completed_orders'],
                'cancelledOrders' => $stats['cancelled_orders'],
                'recentOrders' => $recentOrders,
                'dailyRevenue' => $dailyRevenue,
                'orderStatusCounts' => $orderStatusCounts,
                'stockReport' => $stockReport,
                'newCustomers' => $newCustomers,
                'metrics' => $metrics,
                'categories' => $categories,
                'brands' => $brands,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/dashboard.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            Session::flash('error', 'Failed to load dashboard data');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function getRevenueChart(): void
    {
        try {
            $days = (int) ($_GET['days'] ?? 7);
            $dailyRevenue = $this->orderModel->getDailyRevenue($days);
            Response::json(['success' => true, 'data' => $dailyRevenue]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch chart data', 500);
        }
    }
}
