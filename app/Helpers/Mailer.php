<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }

    public static function orderConfirmation(array $order, array $items): bool
    {
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

        return self::send($order['customer_email'] ?? $order['customer_phone'] . '@placeholder.com', "Order #{$order['order_number']} Confirmed - " . SITE_NAME, $html);
    }

    public static function orderStatusUpdate(array $order, string $newStatus): bool
    {
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

        return self::send($order['customer_email'] ?? $order['customer_phone'] . '@placeholder.com', "Order #{$order['order_number']} Status Updated - " . SITE_NAME, $html);
    }
}
