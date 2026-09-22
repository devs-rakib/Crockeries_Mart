<?php
namespace App\Controllers\admin;

use App\Models\SupportTicket;
use App\Helpers\Auth;
use App\Helpers\Session;
use App\Helpers\Response;
use App\Helpers\CSRF;

class SupportController
{
    private SupportTicket $ticketModel;

    public function __construct()
    {
        Auth::requirePermission('manage_support');
        $this->ticketModel = new SupportTicket();
    }

    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $this->ticketModel->getAll($page, 20, $status, $search);

        $counts = [
            'all'    => $this->ticketModel->countByStatus('open') + $this->ticketModel->countByStatus('replied') + $this->ticketModel->countByStatus('closed'),
            'open'   => $this->ticketModel->countByStatus('open'),
            'replied'=> $this->ticketModel->countByStatus('replied'),
            'closed' => $this->ticketModel->countByStatus('closed'),
        ];

        $data = [
            'pageTitle' => 'Support Tickets',
            'tickets'   => $result['tickets'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'totalPages'=> $result['total_pages'],
            'status'    => $status,
            'search'    => $search,
            'counts'    => $counts,
        ];

        require APP_ROOT . '/views/admin/admin_header.php';
        require APP_ROOT . '/views/admin/support/index.php';
        require APP_ROOT . '/views/admin/admin_footer.php';
    }

    public function view(int $id): void
    {
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            Session::flash('error', 'Ticket not found');
            Response::redirect(APP_URL . '/admin/support');
            return;
        }

        $replies = $this->ticketModel->getReplies($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->reply($id);
            return;
        }

        $this->ticketModel->updateStatus($id, 'replied');

        $data = [
            'pageTitle' => 'Ticket #' . $ticket['id'],
            'ticket'    => $ticket,
            'replies'   => $replies,
        ];

        require APP_ROOT . '/views/admin/admin_header.php';
        require APP_ROOT . '/views/admin/support/view.php';
        require APP_ROOT . '/views/admin/admin_footer.php';
    }

    private function reply(int $id): void
    {
        if (!CSRF::verify()) {
            Session::flash('error', 'Invalid request');
            Response::redirect(APP_URL . "/admin/support/view/{$id}");
            return;
        }

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            Session::flash('error', 'Reply message cannot be empty');
            Response::redirect(APP_URL . "/admin/support/view/{$id}");
            return;
        }

        $this->ticketModel->addReply($id, 'admin', Session::get('user_name', 'Admin'), $message);
        $this->ticketModel->updateStatus($id, 'replied');

        Session::flash('success', 'Reply sent successfully');
        Response::redirect(APP_URL . "/admin/support/view/{$id}");
    }

    public function updateStatus(int $id): void
    {
        if (!CSRF::verify()) {
            Session::flash('error', 'Invalid request');
            Response::redirect(APP_URL . '/admin/support');
            return;
        }

        $status = $_POST['status'] ?? 'open';
        $this->ticketModel->updateStatus($id, $status);

        Session::flash('success', 'Ticket status updated');
        Response::redirect(APP_URL . '/admin/support');
    }

    public function destroy(int $id): void
    {
        if (!CSRF::verify()) {
            Session::flash('error', 'Invalid request');
            Response::redirect(APP_URL . '/admin/support');
            return;
        }

        $this->ticketModel->delete($id);
        Session::flash('success', 'Ticket deleted');
        Response::redirect(APP_URL . '/admin/support');
    }
}
