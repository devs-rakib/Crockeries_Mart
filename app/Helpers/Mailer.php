<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private static function configure(PHPMailer $mail): void
    {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->CharSet    = 'UTF-8';
        $mail->Port       = SMTP_PORT;

        if (SMTP_ENCRYPTION === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
    }

    private static function logError(string $context, Exception $e): void
    {
        $message = sprintf(
            "[%s] %s: %s in %s:%d",
            date('Y-m-d H:i:s'),
            $context,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        error_log($message);
    }

    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        if (empty(SMTP_USER) || empty(SMTP_PASS)) {
            error_log("[Mailer] SMTP credentials not configured. Email to {$to} skipped.");
            return false;
        }

        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log("[Mailer] Invalid email address: {$to}. Skipping.");
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            self::configure($mail);

            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            self::logError("Failed to send email to {$to}", $e);
            return false;
        }
    }

    public static function sendOTP(string $to, string $otp, string $purpose = 'verification'): bool
    {
        $purposeLabel = ucfirst($purpose);
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #ff3838; color: white; padding: 20px; text-align: center;'>
                <h1>{$purposeLabel} Code</h1>
            </div>
            <div style='padding: 30px; background: #f8f9fa; text-align: center;'>
                <p style='font-size: 16px; color: #333;'>Your {$purposeLabel} code is:</p>
                <div style='font-size: 36px; font-weight: 800; color: #ff3838; letter-spacing: 8px; margin: 20px 0;'>{$otp}</div>
                <p style='font-size: 14px; color: #666;'>This code expires in " . OTP_EXPIRY_MINUTES . " minutes.</p>
                <p style='font-size: 13px; color: #999; margin-top: 20px;'>If you didn't request this code, please ignore this email.</p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center;'>
                <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
            </div>
        </div>";

        return self::send($to, "{$purposeLabel} Code - " . SITE_NAME, $html);
    }

    public static function sendPasswordReset(string $to, string $token): bool
    {
        $resetUrl = APP_URL . '/reset-password?token=' . urlencode($token);

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #ff3838; color: white; padding: 20px; text-align: center;'>
                <h1>Password Reset</h1>
            </div>
            <div style='padding: 30px; background: #f8f9fa;'>
                <p style='font-size: 16px; color: #333;'>You requested a password reset for your " . SITE_NAME . " account.</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetUrl}' style='display: inline-block; padding: 14px 40px; background: #ff3838; color: white; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 700;'>Reset Password</a>
                </div>
                <p style='font-size: 14px; color: #666;'>Or copy and paste this link in your browser:</p>
                <p style='font-size: 13px; color: #0066cc; word-break: break-all;'>{$resetUrl}</p>
                <p style='font-size: 13px; color: #999; margin-top: 20px;'>This link expires in " . PASSWORD_RESET_EXPIRY_MINUTES . " minutes.</p>
                <p style='font-size: 13px; color: #999;'>If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center;'>
                <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
            </div>
        </div>";

        return self::send($to, "Password Reset - " . SITE_NAME, $html);
    }

    public static function orderConfirmation(array $order, array $items): bool
    {
        if (empty($order['customer_email']) || !filter_var($order['customer_email'], FILTER_VALIDATE_EMAIL)) {
            error_log("[Mailer] Order #{$order['order_number']}: No valid customer email. Skipping.");
            return false;
        }

        $itemsHtml = '';
        foreach ($items as $item) {
            $itemsHtml .= "<tr>
                <td>{$item['product_name']}</td>
                <td>{$item['quantity']}</td>
                <td>৳{$item['unit_price']}</td>
                <td>৳{$item['subtotal']}</td>
            </tr>";
        }

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #ff3838; color: white; padding: 20px; text-align: center;'>
                <h1>Order Confirmed!</h1>
            </div>
            <div style='padding: 20px; background: #f8f9fa;'>
                <h2>Thank you for your order, " . htmlspecialchars($order['customer_name']) . "</h2>
                <p><strong>Order Number:</strong> {$order['order_number']}</p>
                <p><strong>Phone:</strong> {$order['customer_phone']}</p>
                <p><strong>Address:</strong> " . htmlspecialchars($order['shipping_address']) . "</p>
                <p><strong>Payment Method:</strong> {$order['payment_method']}</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <thead>
                        <tr style='background: #dee2e6;'>
                            <th style='padding: 10px; text-align: left;'>Product</th>
                            <th style='padding: 10px;'>Qty</th>
                            <th style='padding: 10px;'>Price</th>
                            <th style='padding: 10px;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>{$itemsHtml}</tbody>
                    <tfoot>
                        <tr style='border-top: 2px solid #dee2e6;'>
                            <td colspan='3' style='padding: 10px; text-align: right;'><strong>Shipping:</strong></td>
                            <td style='padding: 10px;'>৳{$order['shipping_cost']}</td>
                        </tr>
                        <tr style='background: #ff3838; color: white;'>
                            <td colspan='3' style='padding: 10px; text-align: right;'><strong>Total:</strong></td>
                            <td style='padding: 10px;'>৳{$order['total_amount']}</td>
                        </tr>
                    </tfoot>
                </table>
                <p>We will contact you shortly to confirm your order.</p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center;'>
                <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
            </div>
        </div>";

        return self::send($order['customer_email'], "Order #{$order['order_number']} Confirmed - " . SITE_NAME, $html);
    }

    public static function orderStatusUpdate(array $order, string $newStatus): bool
    {
        if (empty($order['customer_email']) || !filter_var($order['customer_email'], FILTER_VALIDATE_EMAIL)) {
            error_log("[Mailer] Order #{$order['order_number']}: No valid customer email for status update. Skipping.");
            return false;
        }

        $statusColors = [
            'pending'    => '#ffc107',
            'processing' => '#17a2b8',
            'completed'  => '#28a745',
            'cancelled'  => '#dc3545'
        ];
        $color = $statusColors[$newStatus] ?? '#6c757d';

        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #ff3838; color: white; padding: 20px; text-align: center;'>
                <h1>Order Status Update</h1>
            </div>
            <div style='padding: 20px; background: #f8f9fa;'>
                <p>Hello " . htmlspecialchars($order['customer_name']) . ",</p>
                <p>Your order <strong>#{$order['order_number']}</strong> status has been updated.</p>
                <div style='text-align: center; margin: 20px 0;'>
                    <span style='background: {$color}; color: white; padding: 10px 30px; border-radius: 5px; font-size: 18px; text-transform: uppercase;'>{$newStatus}</span>
                </div>
                <p>If you have any questions, please contact us at " . SITE_PHONE . "</p>
            </div>
            <div style='background: #333; color: white; padding: 15px; text-align: center;'>
                <p>&copy; " . date('Y') . " " . SITE_NAME . ". All rights reserved.</p>
            </div>
        </div>";

        return self::send($order['customer_email'], "Order #{$order['order_number']} Status Updated - " . SITE_NAME, $html);
    }

    public static function testConnection(): array
    {
        if (empty(SMTP_USER) || empty(SMTP_PASS)) {
            return ['success' => false, 'message' => 'SMTP credentials not configured. Set SMTP_USER and SMTP_PASS in .env'];
        }

        $mail = new PHPMailer(true);
        try {
            self::configure($mail);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function ($str, $level) {
                error_log("[Mailer Debug] {$str}");
            };

            $mail->smtpConnect();
            $mail->getSMTPInstance()->close();

            return ['success' => true, 'message' => 'SMTP connection successful'];
        } catch (Exception $e) {
            self::logError('SMTP connection test failed', $e);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
