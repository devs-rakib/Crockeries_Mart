<?php
namespace App\Controllers\admin;

use App\Models\Banner;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\FileUploader;
use App\Helpers\CSRF;

class BannerController
{
    private Banner $bannerModel;

    public function __construct()
    {
        Auth::requirePermission('manage_banners');
        $this->bannerModel = new Banner();
    }

    public function index(): void
    {
        try {
            $banners = $this->bannerModel->getAll();

            $data = [
                'pageTitle' => 'Banners & Sliders',
                'banners' => $banners,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/banners/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Banner list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load banners');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function create(): void
    {
        try {
            $data = [
                'pageTitle' => 'Add Banner',
                'banner' => null,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/banners/create.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Banner create form error: " . $e->getMessage());
            Session::flash('error', 'Failed to load form');
            Response::redirect(APP_URL . '/admin/banners');
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

            $title = Sanitizer::clean($_POST['title'] ?? '');
            $description = Sanitizer::clean($_POST['description'] ?? '');
            $buttonText = Sanitizer::clean($_POST['button_text'] ?? 'Shop Now');
            $link = Sanitizer::clean($_POST['link'] ?? '');
            $type = Sanitizer::clean($_POST['type'] ?? 'hero_slider');
            $position = (int) ($_POST['position'] ?? 0);
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($title)) {
                Session::flash('error', 'Banner title is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/banners/create');
                return;
            }

            $validTypes = ['hero_slider', 'middle_banner', 'sidebar_banner'];
            if (!in_array($type, $validTypes)) {
                Session::flash('error', 'Invalid banner type');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/banners/create');
                return;
            }

            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = FileUploader::upload($_FILES['image'], 'banners', 'banner_');
                if (!$image) {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . '/admin/banners/create');
                    return;
                }
            } else {
                Session::flash('error', 'Banner image is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/banners/create');
                return;
            }

            $data = [
                'title' => $title,
                'image' => $image,
                'description' => $description,
                'button_text' => $buttonText,
                'link' => $link,
                'type' => $type,
                'position' => $position,
                'status' => $status,
            ];

            $bannerId = $this->bannerModel->create($data);

            if ($bannerId) {
                Session::flash('success', 'Banner created successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/banners');
            } else {
                Session::flash('error', 'Failed to create banner');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . '/admin/banners/create');
            }
        } catch (\Exception $e) {
            error_log("Banner store error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while creating banner');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . '/admin/banners/create');
        }
    }

    public function edit(int $id): void
    {
        try {
            $banner = $this->bannerModel->getById($id);
            if (!$banner) {
                Session::flash('error', 'Banner not found');
                Response::redirect(APP_URL . '/admin/banners');
                return;
            }

            $data = [
                'pageTitle' => 'Edit Banner',
                'banner' => $banner,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/banners/edit.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Banner edit error: " . $e->getMessage());
            Session::flash('error', 'Failed to load banner');
            Response::redirect(APP_URL . '/admin/banners');
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

            $banner = $this->bannerModel->getById($id);
            if (!$banner) {
                Session::flash('error', 'Banner not found');
                Response::redirect(APP_URL . '/admin/banners');
                return;
            }

            $title = Sanitizer::clean($_POST['title'] ?? '');
            $description = Sanitizer::clean($_POST['description'] ?? '');
            $buttonText = Sanitizer::clean($_POST['button_text'] ?? 'Shop Now');
            $link = Sanitizer::clean($_POST['link'] ?? '');
            $type = Sanitizer::clean($_POST['type'] ?? 'hero_slider');
            $position = (int) ($_POST['position'] ?? 0);
            $status = (int) ($_POST['status'] ?? 1);

            if (empty($title)) {
                Session::flash('error', 'Banner title is required');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/banners/{$id}/edit");
                return;
            }

            $validTypes = ['hero_slider', 'middle_banner', 'sidebar_banner'];
            if (!in_array($type, $validTypes)) {
                Session::flash('error', 'Invalid banner type');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/banners/{$id}/edit");
                return;
            }

            $image = $banner['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $newImage = FileUploader::upload($_FILES['image'], 'banners', 'banner_');
                if ($newImage) {
                    if ($banner['image']) {
                        FileUploader::delete($banner['image']);
                    }
                    $image = $newImage;
                } else {
                    Session::flash('error', 'Failed to upload image. Allowed types: JPEG, PNG, GIF, WebP');
                    Session::flashInput($_POST);
                    Response::redirect(APP_URL . "/admin/banners/{$id}/edit");
                    return;
                }
            }

            $data = [
                'title' => $title,
                'image' => $image,
                'description' => $description,
                'button_text' => $buttonText,
                'link' => $link,
                'type' => $type,
                'position' => $position,
                'status' => $status,
            ];

            $result = $this->bannerModel->update($id, $data);

            if ($result) {
                Session::flash('success', 'Banner updated successfully');
                Session::clearOldInput();
                Response::redirect(APP_URL . '/admin/banners');
            } else {
                Session::flash('error', 'Failed to update banner');
                Session::flashInput($_POST);
                Response::redirect(APP_URL . "/admin/banners/{$id}/edit");
            }
        } catch (\Exception $e) {
            error_log("Banner update error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while updating banner');
            Session::flashInput($_POST);
            Response::redirect(APP_URL . "/admin/banners/{$id}/edit");
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

            $banner = $this->bannerModel->getById($id);
            if (!$banner) {
                Session::flash('error', 'Banner not found');
                Response::redirect(APP_URL . '/admin/banners');
                return;
            }

            $result = $this->bannerModel->delete($id);

            if ($result) {
                if ($banner['image']) {
                    FileUploader::delete($banner['image']);
                }
                Session::flash('success', 'Banner deleted successfully');
            } else {
                Session::flash('error', 'Failed to delete banner');
            }
        } catch (\Exception $e) {
            error_log("Banner delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting banner');
        }

        Response::redirect(APP_URL . '/admin/banners');
    }

    public function toggleStatus(int $id): void
    {
        try {
            if (!CSRF::verify()) {
                Session::flash('error', 'Invalid request');
                Response::redirect(APP_URL . '/admin');
                return;
            }

            $banner = $this->bannerModel->getById($id);
            if (!$banner) {
                Session::flash('error', 'Banner not found');
                Response::redirect(APP_URL . '/admin/banners');
                return;
            }

            $newStatus = $banner['status'] == 1 ? 0 : 1;
            $this->bannerModel->update($id, ['status' => $newStatus]);

            Session::flash('success', 'Banner status updated');
        } catch (\Exception $e) {
            error_log("Banner toggle error: " . $e->getMessage());
            Session::flash('error', 'Failed to update status');
        }

        Response::redirect(APP_URL . '/admin/banners');
    }
}