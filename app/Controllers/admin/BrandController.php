<?php
namespace App\Controllers\admin;

use App\Models\Brand;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\FileUploader;
use App\Helpers\CSRF;

class BrandController
{
    private Brand $brandModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->brandModel = new Brand();
    }

    public function index(): void
    {
        try {
            $db = \App\Helpers\Database::getInstance();
            $brands = $db->fetchAll(
                "SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id = b.id AND status = 1) as product_count
                 FROM brands b ORDER BY b.name ASC"
            );

            $data = [
                'pageTitle' => 'Brands',
                'brands' => $brands,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/brands/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Brand list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load brands');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function create(): void
    {
        try {
            $data = [
                'pageTitle' => 'Add Brand',
                'brand' => null,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/brands/create.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Brand create form error: " . $e->getMessage());
            Session::flash('error', 'Failed to load form');
            Response::redirect(APP_URL . '/admin/brands');
        }
    }

    public function store(): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $name = Sanitizer::clean($_POST['name'] ?? '');
            $slug = Sanitizer::slug($_POST['slug'] ?? $name);
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($name)) {
                Session::flash('error', 'Brand name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/brands/create');
                return;
            }

            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $logo = FileUploader::upload($_FILES['logo'], 'brands', 'brand_');
                if (!$logo) {
                    Session::flash('error', 'Failed to upload logo. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . '/admin/brands/create');
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'logo' => $logo,
                'status' => $status,
            ];

            $brandId = $this->brandModel->create($data);

            if ($brandId) {
                Session::flash('success', 'Brand created successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/brands');
            } else {
                Session::flash('error', 'Failed to create brand');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/brands/create');
            }
        } catch (\Throwable $e) {
            error_log("Brand store error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while creating brand');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . '/admin/brands/create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $brand = $this->brandModel->getById($id);
            if (!$brand) {
                Session::flash('error', 'Brand not found');
                Response::redirect(APP_URL . '/admin/brands');
                return;
            }

            $data = [
                'pageTitle' => 'Edit Brand',
                'brand' => $brand,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/brands/edit.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Brand edit error: " . $e->getMessage());
            Session::flash('error', 'Failed to load brand');
            Response::redirect(APP_URL . '/admin/brands');
        }
    }

    public function update(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $brand = $this->brandModel->getById($id);
            if (!$brand) {
                Session::flash('error', 'Brand not found');
                Response::redirect(APP_URL . '/admin/brands');
                return;
            }

            $name = Sanitizer::clean($_POST['name'] ?? '');
            $slug = Sanitizer::slug($_POST['slug'] ?? $name);
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($name)) {
                Session::flash('error', 'Brand name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/brands/edit/{$id}");
                return;
            }

            $logo = $brand['logo'];
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $newLogo = FileUploader::upload($_FILES['logo'], 'brands', 'brand_');
                if ($newLogo) {
                    if ($brand['logo']) {
                        FileUploader::delete($brand['logo']);
                    }
                    $logo = $newLogo;
                } else {
                    Session::flash('error', 'Failed to upload logo. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . "/admin/brands/edit/{$id}");
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'logo' => $logo,
                'status' => $status,
            ];

            $this->brandModel->update($id, $data);

            Session::flash('success', 'Brand updated successfully');
            Session::clearOldInput();
            Response::redirect(APP_URL . '/admin/brands');
        } catch (\Throwable $e) {
            error_log("Brand update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating brand');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . "/admin/brands/edit/{$id}");
        }
    }

    public function destroy(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $brand = $this->brandModel->getById($id);
            if (!$brand) {
                Session::flash('error', 'Brand not found');
                Response::redirect(APP_URL . '/admin/brands');
                return;
            }

            $result = $this->brandModel->delete($id);

            if ($result) {
                if ($brand['logo']) {
                    FileUploader::delete($brand['logo']);
                }
                Session::flash('success', 'Brand deleted successfully');
            } else {
                Session::flash('error', 'Failed to delete brand');
            }
        } catch (\Throwable $e) {
            error_log("Brand delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting brand');
        }

        Response::redirect(APP_URL . '/admin/brands');
    }

    public function toggleStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $brand = $this->brandModel->getById($id);
            if (!$brand) {
                Session::flash('error', 'Brand not found');
                Response::redirect(APP_URL . '/admin/brands');
                return;
            }

            $newStatus = $brand['status'] == 1 ? 0 : 1;
            $this->brandModel->update($id, ['status' => $newStatus]);

            Session::flash('success', 'Brand status updated');
        } catch (\Throwable $e) {
            error_log("Brand toggle error: " . $e->getMessage());
            Session::flash('error', 'Failed to update status');
        }

        Response::redirect(APP_URL . '/admin/brands');
    }
}