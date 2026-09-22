<?php
namespace App\Controllers\admin;

use App\Models\Category;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\FileUploader;
use App\Helpers\CSRF;

class CategoryController
{
    private Category $categoryModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        try {
            $categories = $this->categoryModel->getAllAdmin();

            $data = [
                'pageTitle' => 'Categories',
                'categories' => $categories,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/categories/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Category list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load categories');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function create(): void
    {
        try {
            $parentCategories = $this->categoryModel->getAllAdmin();

            $data = [
                'pageTitle' => 'Add Category',
                'parentCategories' => $parentCategories,
                'allCategories' => $parentCategories,
                'category' => null,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/categories/create.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Category create form error: " . $e->getMessage());
            Session::flash('error', 'Failed to load form');
            Response::redirect(APP_URL . '/admin/categories');
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
            $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;
            $iconClass = Sanitizer::clean($_POST['icon_class'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $status = (int) ($_POST['status'] ?? 1);
            $position = (int) ($_POST['position'] ?? 0);

            if (empty($name)) {
                Session::flash('error', 'Category name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/categories/create');
                return;
            }

            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = FileUploader::upload($_FILES['image'], 'categories', 'cat_');
                if (!$image) {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . '/admin/categories/create');
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $parentId,
                'icon_class' => $iconClass,
                'image' => $image,
                'is_featured' => $isFeatured,
                'status' => $status,
                'position' => $position,
            ];

            $categoryId = $this->categoryModel->create($data);

            if ($categoryId) {
                Session::flash('success', 'Category created successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/categories');
            } else {
                Session::flash('error', 'Failed to create category');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/categories/create');
            }
        } catch (\Throwable $e) {
            error_log("Category store error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while creating category');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . '/admin/categories/create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $category = $this->categoryModel->getById($id);
            if (!$category) {
                Session::flash('error', 'Category not found');
                Response::redirect(APP_URL . '/admin/categories');
                return;
            }

            $parentCategories = $this->categoryModel->getAllAdmin();

            $data = [
                'pageTitle' => 'Edit Category',
                'category' => $category,
                'parentCategories' => $parentCategories,
                'allCategories' => $parentCategories,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/categories/edit.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Category edit error: " . $e->getMessage());
            Session::flash('error', 'Failed to load category');
            Response::redirect(APP_URL . '/admin/categories');
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

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                Session::flash('error', 'Category not found');
                Response::redirect(APP_URL . '/admin/categories');
                return;
            }

            $name = Sanitizer::clean($_POST['name'] ?? '');
            $slug = Sanitizer::slug($_POST['slug'] ?? $name);
            $parentId = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;
            $iconClass = Sanitizer::clean($_POST['icon_class'] ?? '');
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $status = (int) ($_POST['status'] ?? 1);
            $position = (int) ($_POST['position'] ?? 0);

            if (empty($name)) {
                Session::flash('error', 'Category name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/categories/edit/{$id}");
                return;
            }

            $image = $category['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImage = FileUploader::upload($_FILES['image'], 'categories', 'cat_');
                if ($newImage) {
                    if ($category['image']) {
                        FileUploader::delete($category['image']);
                    }
                    $image = $newImage;
                } else {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . "/admin/categories/edit/{$id}");
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $parentId,
                'icon_class' => $iconClass,
                'image' => $image,
                'is_featured' => $isFeatured,
                'status' => $status,
                'position' => $position,
            ];

            $result = $this->categoryModel->update($id, $data);

            if ($result) {
                Session::flash('success', 'Category updated successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/categories');
            } else {
                Session::flash('error', 'Failed to update category');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/categories/edit/{$id}");
            }
        } catch (\Throwable $e) {
            error_log("Category update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating category');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . "/admin/categories/edit/{$id}");
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

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                Session::flash('error', 'Category not found');
                Response::redirect(APP_URL . '/admin/categories');
                return;
            }

            $result = $this->categoryModel->delete($id);

            if ($result === 0) {
                Session::flash('error', 'Cannot delete category with subcategories');
            } else {
                if ($category['image']) {
                    FileUploader::delete($category['image']);
                }
                Session::flash('success', 'Category deleted successfully');
            }
        } catch (\Throwable $e) {
            error_log("Category delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting category');
        }

        Response::redirect(APP_URL . '/admin/categories');
    }

    public function toggleStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $category = $this->categoryModel->getById($id);
            if (!$category) {
                Session::flash('error', 'Category not found');
                Response::redirect(APP_URL . '/admin/categories');
                return;
            }

            $newStatus = $category['status'] == 1 ? 0 : 1;
            $this->categoryModel->update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

            Session::flash('success', 'Category status updated');
        } catch (\Throwable $e) {
            error_log("Category toggle error: " . $e->getMessage());
            Session::flash('error', 'Failed to update status');
        }

        Response::redirect(APP_URL . '/admin/categories');
    }
}