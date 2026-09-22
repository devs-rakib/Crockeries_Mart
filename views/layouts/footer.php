<?php
/**
 * Footer Layout - CrokersesMart
 * @var mixed $data Extracted view variables
 */
?>

<!-- Footer Main -->
<footer class="site-footer">
    <div class="container">
        <div class="row">
            <!-- About Us -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-widget">
                    <h4 class="footer-title">About Us</h4>
                    <div class="footer-about">
                        <a href="<?= APP_URL ?>/" class="footer-logo">
                            <img src="<?= APP_URL ?>/assets/images/logo-icon.svg" alt="" style="width:36px;height:36px;color:var(--white)">
                            <span style="font-weight:700; font-size:20px; color:white"><em style="font-style:normal">Crokerses </em>Mart</span>
                        </a>
                        <p>We are committed to providing you with the best quality crockery and kitchenware at affordable prices. Shop with confidence.</p>
                        <div class="footer-social">
                            <a href="<?= SITE_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="<?= SITE_YOUTUBE ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                            <a href="<?= SITE_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-widget">
                    <h4 class="footer-title">Customer Service</h4>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>/account/profile">My Account</a></li>
                        <li><a href="<?= APP_URL ?>/order/track">Order Tracking</a></li>
                        <li><a href="<?= APP_URL ?>/pages/return-policy">Return Policy</a></li>
                        <li><a href="<?= APP_URL ?>/pages/shipping-info">Shipping Info</a></li>
                        <li><a href="<?= APP_URL ?>/pages/faq">FAQ</a></li>
                        <li><a href="<?= APP_URL ?>/support">Support Center</a></li>
                        <li><a href="<?= APP_URL ?>/pages/terms">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-widget">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?= APP_URL ?>/shop">Shop All</a></li>
                        <li><a href="<?= APP_URL ?>/shop?category=new-arrivals">New Arrivals</a></li>
                        <li><a href="<?= APP_URL ?>/shop?category=best-sellers">Best Sellers</a></li>
                        <li><a href="<?= APP_URL ?>/shop?category=sale">Sale Items</a></li>
                        <li><a href="<?= APP_URL ?>/shop?category=combos">Combo Offers</a></li>
                        <li><a href="<?= APP_URL ?>/blog">Blog</a></li>
                    </ul>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="footer-widget">
                    <h4 class="footer-title">Contact Info</h4>
                    <ul class="footer-contact">
                        <li>
                            <i class="bi bi-geo-alt"></i>
                            <span><?= SITE_ADDRESS ?></span>
                        </li>
                        <li>
                            <i class="bi bi-telephone"></i>
                            <a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a>
                        </li>
                        <li>
                            <i class="bi bi-envelope"></i>
                            <a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a>
                        </li>
                        <li>
                            <i class="bi bi-clock"></i>
                            <span>Sat - Fri: 10:00 AM - 8:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Payment & Copyright -->
<section class="footer-payment">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-6">
                <div class="copyright">
                    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All Rights Reserved.</p>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="payment-methods">
                    <span>We Accept:</span>
                    <img src="<?= APP_URL ?>/assets/images/payment/sslcommerz.svg" alt="SSLCommerz" class="payment-icon" title="SSLCommerz" width="50" height="28">
                    <img src="<?= APP_URL ?>/assets/images/payment/visa.svg" alt="Visa" class="payment-icon" title="Visa" width="50" height="28">
                    <img src="<?= APP_URL ?>/assets/images/payment/mastercard.svg" alt="Mastercard" class="payment-icon" title="Mastercard" width="50" height="28">
                    <img src="<?= APP_URL ?>/assets/images/payment/cod.svg" alt="Cash on Delivery" class="payment-icon" title="Cash on Delivery" width="50" height="28">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-chevron-up"></i>
</button>

<!-- Swiper 11 JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<script src="<?= APP_URL ?>/assets/js/cart.js"></script>
<script src="<?= APP_URL ?>/assets/js/search.js"></script>

</body>
</html>
