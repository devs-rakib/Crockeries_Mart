<?php
namespace App\Controllers;

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
        $this->ticketModel = new SupportTicket();
    }

    public function index(): void
    {
        Auth::requireLogin();

        $tickets = $this->ticketModel->getByUserId(Auth::id());

        $data = [
            'pageTitle' => 'My Support Tickets',
            'tickets'   => $tickets,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/support.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::verify()) {
            Response::json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        $message = trim($_POST['message'] ?? '');
        $orderNumber = trim($_POST['order_number'] ?? '');

        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            Response::json(['success' => false, 'message' => 'Please fill all required fields']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['success' => false, 'message' => 'Please enter a valid email address']);
            return;
        }

        $userId = Auth::check() ? Auth::id() : null;

        $ticketId = $this->ticketModel->create([
            'user_id'      => $userId,
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'subject'      => $subject,
            'category'     => $category,
            'message'      => $message,
            'order_number' => $orderNumber ?: null,
            'status'       => 'open',
            'priority'     => 'medium',
        ]);

        if ($ticketId) {
            Response::json(['success' => true, 'message' => 'Ticket submitted successfully! We will respond shortly.', 'ticket_id' => $ticketId]);
        } else {
            Response::json(['success' => false, 'message' => 'Failed to submit ticket. Please try again.']);
        }
    }

    public function view(int $id): void
    {
        Auth::requireLogin();

        $ticket = $this->ticketModel->getById($id);
        if (!$ticket || $ticket['user_id'] != Auth::id()) {
            Session::flash('error', 'Ticket not found');
            Response::redirect(APP_URL . '/support');
            return;
        }

        $replies = $this->ticketModel->getReplies($id);

        $data = [
            'pageTitle' => 'Ticket #' . $ticket['id'],
            'ticket'    => $ticket,
            'replies'   => $replies,
        ];

        require APP_ROOT . '/views/layouts/header.php';
        require APP_ROOT . '/views/support_view.php';
        require APP_ROOT . '/views/layouts/footer.php';
    }

    public function reply(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::verify()) {
            Response::json(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        Auth::requireLogin();

        $ticket = $this->ticketModel->getById($id);
        if (!$ticket || $ticket['user_id'] != Auth::id()) {
            Response::json(['success' => false, 'message' => 'Ticket not found'], 404);
            return;
        }

        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            Response::json(['success' => false, 'message' => 'Message cannot be empty']);
            return;
        }

        $this->ticketModel->addReply($id, 'customer', Auth::name(), $message);
        $this->ticketModel->updateStatus($id, 'open');

        Response::json(['success' => true, 'message' => 'Reply sent successfully']);
    }
}
