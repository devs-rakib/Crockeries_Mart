<?php use App\Helpers\Sanitizer; ?>

<style>
    .page-hero { background: linear-gradient(135deg, var(--cm-dark) 0%, var(--cm-dark-light) 100%); color: #fff; padding: 60px 0 40px; text-align: center; }
    .page-hero h1 { font-size: 32px; font-weight: 800; }
    .page-content { padding: 60px 0; }
    .faq-item { background: var(--cm-white); border-radius: 8px; box-shadow: var(--cm-shadow); margin-bottom: 12px; overflow: hidden; }
    .faq-question {
        width: 100%; padding: 16px 20px; border: none; background: none;
        text-align: left; font-size: 15px; font-weight: 600; color: var(--cm-dark);
        cursor: pointer; display: flex; justify-content: space-between; align-items: center;
    }
    .faq-question i { transition: transform 0.3s; font-size: 14px; color: var(--cm-gray-500); }
    .faq-question.active i { transform: rotate(180deg); color: var(--cm-primary); }
    .faq-answer { padding: 0 20px 16px; font-size: 14px; color: #555; line-height: 1.7; display: none; }
    .faq-answer.show { display: block; }
</style>

<div class="page-hero">
    <div class="container" style="color: #fff;">
        <h1>Frequently Asked Questions</h1>
    </div>
</div>

<div class="page-content">
    <div class="container" style="max-width: 800px;">

        <div class="faq-item">
            <button class="faq-question">How do I place an order? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">Simply browse our shop, add items to your cart, and proceed to checkout. You can pay via Cash on Delivery or SSLCommerz (credit/debit card, net banking).</div>
        </div>

        <div class="faq-item">
            <button class="faq-question">What payment methods do you accept? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">We accept Cash on Delivery (COD), credit/debit cards, and net banking through our secure SSLCommerz payment gateway.</div>
        </div>

        <div class="faq-item">
            <button class="faq-question">How long does delivery take? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">Inside Dhaka: 1-2 business days. Outside Dhaka: 3-5 business days. Free shipping on orders above ৳3,000.</div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Can I return a product? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">Yes! We offer a 7-day return policy for damaged, defective, or incorrect items. The product must be in its original packaging and unused condition. <a href="<?= APP_URL ?>/pages/return-policy">Read full Return Policy →</a></div>
        </div>

        <div class="faq-item">
            <button class="faq-question">How do I track my order? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">Once your order is shipped, you can track it using your order number on our <a href="<?= APP_URL ?>/order/track">Order Tracking</a> page.</div>
        </div>

        <div class="faq-item">
            <button class="faq-question">Do I need an account to place an order? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">No! You can checkout as a guest. An account is automatically created when you place your first order using your phone number.</div>
        </div>

        <div class="faq-item">
            <button class="faq-question">How do I contact customer support? <i class="bi bi-chevron-down"></i></button>
            <div class="faq-answer">You can reach us at <?= SITE_PHONE ?> (call/WhatsApp) or email us at <?= SITE_EMAIL ?>. Our support team is available Saturday-Friday, 10:00 AM - 8:00 PM.</div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.faq-question').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var answer = this.nextElementSibling;
            var isOpen = answer.classList.contains('show');
            document.querySelectorAll('.faq-answer').forEach(function(a) { a.classList.remove('show'); });
            document.querySelectorAll('.faq-question').forEach(function(q) { q.classList.remove('active'); });
            if (!isOpen) {
                answer.classList.add('show');
                this.classList.add('active');
            }
        });
    });
});
</script>
