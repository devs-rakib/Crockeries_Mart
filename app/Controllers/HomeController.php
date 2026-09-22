<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Helpers\Session;

class HomeController
{
    private Product $productModel;
    private Category $categoryModel;
    private Brand $brandModel;
    private Banner $bannerModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->brandModel = new Brand();
        $this->bannerModel = new Banner();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getFeatured();

        $categoryProducts = [];
        foreach ($categories as $cat) {
            $products = $this->productModel->getByCategory($cat['id'], 8);
            if (!empty($products)) {
                $categoryProducts[] = [
                    'category' => $cat,
                    'products' => $products,
                ];
            }
        }

        $data = [
            'pageTitle'         => 'Home',
            'heroSliders'       => $this->bannerModel->getActive('hero_slider'),
            'categories'        => $categories,
            'sidebarCategories' => $this->categoryModel->getSidebarTree(),
            'categoryProducts'  => $categoryProducts,
            'featuredProducts'  => $this->productModel->getFeatured(8),
            'offerProducts'     => $this->productModel->getOfferProducts(12),
            'todaysDeals'       => $this->productModel->getTodaysDeals(6),
            'brands'            => $this->brandModel->getAll(),
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/home.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }
}
