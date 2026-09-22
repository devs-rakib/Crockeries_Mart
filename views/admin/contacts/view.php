<?php
use App\Helpers\Sanitizer;
use App\Helpers\CSRF;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0"><?= Sanitizer::clean($contact['subject'] ?? 'Contact Message') ?></h4>
        <small class="text-muted">From <?= Sanitizer::clean($contact['name']) ?> - <?= Sanitizer::timeAgo($contact['created_at']) ?></small>
    </div>
    <a href="<?= ADMIN_URL ?>contacts" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Contacts
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Message</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <?= nl2br(Sanitizer::clean($contact['message'])) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Contact Details</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong> <?= Sanitizer::clean($contact['name']) ?></p>
                <p class="mb-2"><strong>Email:</strong> <a href="mailto:<?= Sanitizer::clean($contact['email']) ?>"><?= Sanitizer::clean($contact['email']) ?></a></p>
                <?php if (!empty($contact['phone'])): ?>
                    <p class="mb-2"><strong>Phone:</strong> <?= Sanitizer::clean($contact['phone']) ?></p>
                <?php endif; ?>
                <p class="mb-0"><strong>Date:</strong> <?= date('d M Y, h:i A', strtotime($contact['created_at'])) ?></p>
            </div>
        </div>

        <div class="d-grid">
            <form method="POST" action="<?= ADMIN_URL ?>contacts/destroy/<?= (int)$contact['id'] ?>" onsubmit="return confirm('Are you sure you want to delete this message?');">
                <?= CSRF::field() ?>
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-trash me-1"></i> Delete Message
                </button>
            </form>
        </div>
    </div>
</div>
