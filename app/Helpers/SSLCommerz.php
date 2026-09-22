<?php
namespace App\Helpers;

class SSLCommerz
{
    private string $storeId;
    private string $storePass;
    private bool $sandbox;
    private string $baseUrl;

    public function __construct()
    {
        $this->storeId = SSLCOMMERZ_STORE_ID;
        $this->storePass = SSLCOMMERZ_STORE_PASS;
        $this->sandbox = SSLCOMMERZ_SANDBOX;
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.sslcommerz.com'
            : 'https://secure.sslcommerz.com';
    }

    public function createSession(array $orderData): array
    {
        $postData = [
            'store_id'         => $this->storeId,
            'store_passwd'     => $this->storePass,
            'total_amount'     => $orderData['total_amount'],
            'currency'         => $orderData['currency'] ?? 'BDT',
            'tran_id'          => $orderData['tran_id'],
            'success_url'      => $orderData['success_url'],
            'fail_url'         => $orderData['fail_url'],
            'cancel_url'       => $orderData['cancel_url'],
            'ipn_url'          => $orderData['ipn_url'] ?? '',
            'cus_name'         => $orderData['cus_name'] ?? '',
            'cus_email'        => $orderData['cus_email'] ?? '',
            'cus_phone'        => $orderData['cus_phone'] ?? '',
            'cus_add1'         => $orderData['cus_add1'] ?? '',
            'cus_city'         => $orderData['cus_city'] ?? '',
            'cus_state'        => $orderData['cus_state'] ?? '',
            'cus_postcode'     => $orderData['cus_postcode'] ?? '',
            'cus_country'      => $orderData['cus_country'] ?? 'Bangladesh',
            'ship_name'        => $orderData['ship_name'] ?? $orderData['cus_name'] ?? '',
            'ship_add1'        => $orderData['ship_add1'] ?? $orderData['cus_add1'] ?? '',
            'ship_city'        => $orderData['ship_city'] ?? '',
            'ship_state'       => $orderData['ship_state'] ?? '',
            'ship_postcode'    => $orderData['ship_postcode'] ?? '',
            'ship_country'     => $orderData['ship_country'] ?? 'Bangladesh',
            'product_name'     => $orderData['product_name'] ?? 'Order',
            'product_category' => $orderData['product_category'] ?? 'E-commerce',
            'product_profile'  => $orderData['product_profile'] ?? 'general',
            'value_a'          => $orderData['value_a'] ?? '',
            'value_b'          => $orderData['value_b'] ?? '',
            'value_c'          => $orderData['value_c'] ?? '',
            'value_d'          => $orderData['value_d'] ?? '',
        ];

        $url = $this->baseUrl . '/gwprocess/v4/api.php';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("SSLCommerz cURL error: {$error}");
        }

        if ($httpCode !== 200) {
            throw new \Exception("SSLCommerz HTTP error: {$httpCode}");
        }

        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("SSLCommerz invalid response: {$response}");
        }

        if (!isset($result['status']) || $result['status'] !== 'SUCCESS') {
            $errMsg = $result['failedreason'] ?? $result['message'] ?? 'Unknown error';
            throw new \Exception("SSLCommerz session error: {$errMsg}");
        }

        return $result;
    }

    public function validate(array $response): bool
    {
        $valId = $response['val_id'] ?? '';
        if (empty($valId)) {
            return false;
        }

        $postData = [
            'val_id'     => $valId,
            'store_id'   => $this->storeId,
            'store_passwd' => $this->storePass,
            'format'     => 'json',
        ];

        $url = $this->baseUrl . '/validator/api/validationserver.php';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        $data = json_decode($result, true);
        if (!$data) {
            return false;
        }

        return isset($data['status']) && $data['status'] === 'VALID';
    }

    public function getTestCredentials(): array
    {
        return [
            'store_id'   => 'testbox',
            'store_pass' => 'qwerty',
            'card'       => '4111111111111111',
            'cvv'        => '123',
            'expiry'     => 'any future date',
        ];
    }
}
