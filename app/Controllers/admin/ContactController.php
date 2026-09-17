<?php
namespace App\Controllers\admin;

use App\Models\Contact;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;

class ContactController
{
    private Contact $contactModel;

    public function __construct()
    {
        Auth::requireAdmin();
        $this->contactModel = new Contact();
    }

    public function index(): void
    {
        try {
            $page = (int) ($_GET['page'] ?? 1);
            $result = $this->contactModel->getAll($page, 20);

            $data = [
                'pageTitle'  => 'Contacts',
                'contacts'   => $result['contacts'],
                'total'      => $result['total'],
                'page'       => $result['page'],
                'totalPages' => $result['total_pages'],
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/contacts/index.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Contact list error: " . $e->getMessage());
            Session::flash('error', 'Failed to load contacts');
            Response::redirect(APP_URL . '/admin');
        }
    }

    public function view(int $id): void
    {
        try {
            $contact = $this->contactModel->getById($id);
            if (!$contact) {
                Session::flash('error', 'Contact not found');
                Response::redirect(APP_URL . '/admin/contacts');
                return;
            }

            $this->contactModel->markAsRead($id);

            $data = [
                'pageTitle' => 'View Contact',
                'contact'   => $contact,
            ];

            require APP_ROOT . '/views/admin/admin_header.php';
            require APP_ROOT . '/views/admin/contacts/view.php';
            require APP_ROOT . '/views/admin/admin_footer.php';
        } catch (\Exception $e) {
            error_log("Contact view error: " . $e->getMessage());
            Session::flash('error', 'Failed to load contact');
            Response::redirect(APP_URL . '/admin/contacts');
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

            $contact = $this->contactModel->getById($id);
            if (!$contact) {
                Session::flash('error', 'Contact not found');
                Response::redirect(APP_URL . '/admin/contacts');
                return;
            }

            $this->contactModel->delete($id);
            Session::flash('success', 'Contact deleted successfully');
        } catch (\Exception $e) {
            error_log("Contact delete error: " . $e->getMessage());
            Session::flash('error', 'An error occurred while deleting contact');
        }

        Response::redirect(APP_URL . '/admin/contacts');
    }
}
