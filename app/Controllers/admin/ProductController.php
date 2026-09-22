<?php
namespace App\Controllers\admin;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\FileUploader;
use App\Helpers\CSRF;

class ProductController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;

    public function __construct()
    {
        Auth::requirePermission('manage_products');
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $search = Sanitizer::clean($_GET['search'] ?? '');
            
            $result = $this->productModel->getAllAdmin($page, 20, $search);

            $data = [
                'pageTitle' => 'Products',
                'products' => $result['products'],
                'total' => $result['total'],
                'page' => $result['page'],
                'totalPages' => $result['total_pages'],
                'search' => $search,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/products/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Product list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load products');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function create(): void
    {
        try {
            $categories = $this->categoryModel->getAll();
            $brands = $this->brandModel->getAll();

            $data = [
                'pageTitle' => 'Add Product',
                'categories' => $categories,
                'brands' => $brands,
                'product' => null,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/products/create.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Product create form error: " . $e->getMessage());
            Session::flash('error', 'Failed to load form');
            Response::redirect(APP_URL . '/admin/products');
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
            $sku = Sanitizer::clean($_POST['sku'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $brandId = (int) ($_POST['brand_id'] ?? 0);
            $shortDescription = Sanitizer::clean($_POST['short_description'] ?? '');
            $longDescription = Sanitizer::clean($_POST['long_description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $discountPrice = !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : null;
            $stockQuantity = (int) ($_POST['stock_quantity'] ?? 0);
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isOffer = isset($_POST['is_offer']) ? 1 : 0;
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($name)) {
                Session::flash('error', 'Product name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/products/create');
                return;
            }

            if ($price <= 0) {
                Session::flash('error', 'Price must be greater than 0');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/products/create');
                return;
            }

            $mainImage = null;
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $mainImage = FileUploader::upload($_FILES['main_image'], 'products', 'product_');
                if (!$mainImage) {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . '/admin/products/create');
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'category_id' => $categoryId ?: null,
                'brand_id' => $brandId ?: null,
                'short_description' => $shortDescription,
                'long_description' => $longDescription,
                'price' => $price,
                'discount_price' => $discountPrice,
                'stock_quantity' => $stockQuantity,
                'main_image' => $mainImage,
                'is_featured' => $isFeatured,
                'is_offer' => $isOffer,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $productId = $this->productModel->create($data);

            if ($productId) {
                Session::flash('success', 'Product created successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/products');
            } else {
                Session::flash('error', 'Failed to create product');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/products/create');
            }
        } catch (\Throwable $e) {
            error_log("Product store error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while creating product');
            Response::redirect(APP_URL . '/admin/products/create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $product = $this->productModel->getById($id);
            if (!$product) {
                Session::flash('error', 'Product not found');
                Response::redirect(APP_URL . '/admin/products');
                return;
            }

            $categories = $this->categoryModel->getAll();
            $brands = $this->brandModel->getAll();

            $data = [
                'pageTitle' => 'Edit Product',
                'product' => $product,
                'categories' => $categories,
                'brands' => $brands,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/products/edit.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Throwable $e) {
            error_log("Product edit error: " . $e->getMessage());
            Session::flash('error', 'Failed to load product');
            Response::redirect(APP_URL . '/admin/products');
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

            $product = $this->productModel->getById($id);
            if (!$product) {
                Session::flash('error', 'Product not found');
                Response::redirect(APP_URL . '/admin/products');
                return;
            }

            $name = Sanitizer::clean($_POST['name'] ?? '');
            $slug = Sanitizer::slug($_POST['slug'] ?? $name);
            $sku = Sanitizer::clean($_POST['sku'] ?? '');
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $brandId = (int) ($_POST['brand_id'] ?? 0);
            $shortDescription = Sanitizer::clean($_POST['short_description'] ?? '');
            $longDescription = Sanitizer::clean($_POST['long_description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $discountPrice = !empty($_POST['discount_price']) ? (float) $_POST['discount_price'] : null;
            $stockQuantity = (int) ($_POST['stock_quantity'] ?? 0);
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
            $isOffer = isset($_POST['is_offer']) ? 1 : 0;
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($name)) {
                Session::flash('error', 'Product name is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/products/{$id}/edit");
                return;
            }

            if ($price <= 0) {
                Session::flash('error', 'Price must be greater than 0');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/products/{$id}/edit");
                return;
            }

            $mainImage = $product['main_image'];
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $newImage = FileUploader::upload($_FILES['main_image'], 'products', 'product_');
                if ($newImage) {
                    if ($product['main_image']) {
                        FileUploader::delete($product['main_image']);
                    }
                    $mainImage = $newImage;
                } else {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . "/admin/products/{$id}/edit");
                    return;
                }
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'category_id' => $categoryId ?: null,
                'brand_id' => $brandId ?: null,
                'short_description' => $shortDescription,
                'long_description' => $longDescription,
                'price' => $price,
                'discount_price' => $discountPrice,
                'stock_quantity' => $stockQuantity,
                'main_image' => $mainImage,
                'is_featured' => $isFeatured,
                'is_offer' => $isOffer,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $result = $this->productModel->update($id, $data);

            if ($result) {
                Session::flash('success', 'Product updated successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/products');
            } else {
                Session::flash('error', 'Failed to update product');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/products/{$id}/edit");
            }
        } catch (\Throwable $e) {
            error_log("Product update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating product');
            Response::redirect(APP_URL . "/admin/products/{$id}/edit");
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

            $product = $this->productModel->getById($id);
            if (!$product) {
                Session::flash('error', 'Product not found');
                Response::redirect(APP_URL . '/admin/products');
                return;
            }

            if ($product['main_image']) {
                FileUploader::delete($product['main_image']);
            }

            $result = $this->productModel->delete($id);

            if ($result) {
                Session::flash('success', 'Product deleted successfully');
            } else {
                Session::flash('error', 'Failed to delete product');
            }
        } catch (\Throwable $e) {
            error_log("Product delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting product');
        }

        Response::redirect(APP_URL . '/admin/products');
    }

    public function toggleStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $product = $this->productModel->getById($id);
            if (!$product) {
                Session::flash('error', 'Product not found');
                Response::redirect(APP_URL . '/admin/products');
                return;
            }

            $newStatus = $product['status'] == 1 ? 0 : 1;
            $this->productModel->update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

            Session::flash('success', 'Product status updated');
        } catch (\Throwable $e) {
            error_log("Product toggle error: " . $e->getMessage());
            Session::flash('error', 'Failed to update status');
        }

        Response::redirect(APP_URL . '/admin/products');
    }
}