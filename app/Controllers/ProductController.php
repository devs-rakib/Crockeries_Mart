<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Review;
use App\Helpers\Session;
use App\Helpers\Sanitizer;
use App\Helpers\Response;

class ProductController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private Review $reviewModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->reviewModel = new Review();
    }

    public function shop(): void
    {
        $categoryId = (int) ($_GET['category'] ?? 0);
        $brandId = (int) ($_GET['brand'] ?? 0);
        $minPrice = (float) ($_GET['min_price'] ?? 0);
        $maxPrice = (float) ($_GET['max_price'] ?? 0);
        $sort = $_GET['sort'] ?? 'newest';
        $page = (int) ($_GET['page'] ?? 1);

        $isOffer = str_contains($_SERVER['REQUEST_URI'] ?? '', '/offer');

        $filters = [];
        if ($categoryId) $filters['category_id'] = $categoryId;
        if ($brandId) $filters['brand_id'] = $brandId;
        if ($minPrice) $filters['min_price'] = $minPrice;
        if ($maxPrice) $filters['max_price'] = $maxPrice;
        if ($isOffer) $filters['is_offer'] = 1;

        $result = $this->productModel->getAll($filters, $page, ITEMS_PER_PAGE, $sort);

        $data = [
            'pageTitle'  => $isOffer ? 'Special Offers' : 'Shop',
            'products'   => $result['products'],
            'pagination' => $result,
            'filters'    => $filters,
            'sort'       => $sort,
            'categories' => $this->categoryModel->getSidebarTree(),
            'brands'     => $this->brandModel->getAll(),
            'currentCategory' => $categoryId ? $this->categoryModel->getById($categoryId) : null,
            'currentBrand'    => $brandId ? $this->brandModel->getById($brandId) : null,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/shop.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function detail(string $slug): void
    {
        $product = $this->productModel->getBySlug($slug);
        if (!$product) {
            http_response_code(404);
            require APP_ROOT . '/views/404.php';
            return;
        }

        $this->productModel->incrementViews($product['id']);

        $reviews = $this->reviewModel->getByProduct($product['id']);
        $data = [
            'pageTitle'       => $product['name'],
            'product'         => $product,
            'images'          => $this->productModel->getImages($product['id']),
            'variants'        => $this->productModel->getVariants($product['id']),
            'relatedProducts' => $this->productModel->getRelated($product['id'], $product['category_id']),
            'reviews'         => $reviews,
            'avgRating'       => $this->reviewModel->getAverageRating($product['id']),
            'ratingDist'      => $this->reviewModel->getRatingDistribution($product['id']),
            'reviewCount'     => count($reviews),
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/product_detail.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function search(): void
    {
        $query = trim($_POST['query'] ?? $_GET['query'] ?? $_POST['q'] ?? $_GET['q'] ?? '');
        if (strlen($query) < 2) {
            Response::json(['success' => true, 'results' => [], 'products' => []]);
        }

        $products = $this->productModel->search($query, 10);
        $results = [];
        foreach ($products as &$p) {
            $results[] = [
                'id'          => $p['id'],
                'name'        => $p['name'],
                'slug'        => $p['slug'],
                'image'       => Sanitizer::image($p['main_image']),
                'main_image'  => Sanitizer::image($p['main_image']),
                'price'       => $p['price'],
                'discount_price' => $p['discount_price'],
                'category'    => $p['category_name'],
                'url'         => APP_URL . '/product/' . $p['slug'],
            ];
        }

        Response::json(['success' => true, 'results' => $results, 'products' => $results]);
    }

    public function filter(): void
    {
        $filters = [
            'category_id' => (int) ($_POST['category_id'] ?? $_GET['category_id'] ?? 0),
            'brand_id'    => (int) ($_POST['brand_id'] ?? $_GET['brand_id'] ?? 0),
            'min_price'   => (float) ($_POST['min_price'] ?? $_GET['min_price'] ?? 0),
            'max_price'   => (float) ($_POST['max_price'] ?? $_GET['max_price'] ?? 0),
        ];

        $filters = array_filter($filters, fn($v) => $v > 0);
        $sort = $_POST['sort'] ?? $_GET['sort'] ?? 'newest';
        $page = (int) ($_POST['page'] ?? $_GET['page'] ?? 1);

        $result = $this->productModel->getAll($filters, $page, ITEMS_PER_PAGE, $sort);

        $html = '';
        if (empty($result['products'])) {
            $html = '<div class="col-12 text-center py-5"><h5>No products found</h5></div>';
        } else {
            ob_start();
            foreach ($result['products'] as $product) {
                include APP_ROOT . '/views/partials/product_card.php';
            }
            $html = ob_get_clean();
        }

        Response::json([
            'html'         => $html,
            'total'        => $result['total'],
            'page'         => $result['page'],
            'total_pages'  => $result['total_pages'],
        ]);
    }
}
