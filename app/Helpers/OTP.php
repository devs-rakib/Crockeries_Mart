<?php
namespace App\Helpers;

class OTP
{
    public static function generate(string $phone): string
    {
        $db = Database::getInstance();

        self::cleanup($db);

        $otp = str_pad(random_int(0, 999999), OTP_LENGTH, '0', STR_PAD_LEFT);

        $db->insert('otp_tokens', [
            'phone'      => $phone,
            'otp_code'   => $otp,
            'expires_at' => date('Y-m-d H:i:s', time() + OTP_EXPIRY_MINUTES * 60),
            'used'       => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $otp;
    }

    public static function verify(string $phone, string $otp): bool
    {
        $db = Database::getInstance();

        $token = $db->fetch(
            "SELECT * FROM otp_tokens 
             WHERE phone = ? AND otp_code = ? AND used = 0 AND expires_at > NOW() 
             ORDER BY id DESC LIMIT 1",
            [$phone, $otp]
        );

        if (!$token) {
            return false;
        }

        $db->update('otp_tokens', ['used' => 1], 'id = ?', [$token['id']]);

        return true;
    }

    public static function cleanup(?Database $db = null): void
    {
        $db = $db ?? Database::getInstance();
        $db->delete('otp_tokens', 'expires_at < NOW() OR used = 1');
    }
}
