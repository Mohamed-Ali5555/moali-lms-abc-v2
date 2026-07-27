<?php

namespace Modules\Theme\App\Http\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Payment_gateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class PaymobService
{
    protected $apiKey;
    protected $apiUrl;
    protected $iframeId;
    protected $integrationId;

    public function __construct()
    {
        try {
            $gateway = Payment_gateway::where('identifier', 'paymob')->first();

            if (!$gateway) {
                throw new \Exception("Paymob payment gateway configuration not found in database.");
            }

            $keys = json_decode($gateway->keys, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $keys = json_decode(Crypt::decrypt($gateway->keys), true);
            }

            $this->apiKey = $keys['api_key'] ?? null;
            $this->iframeId = $keys['iframe_id'] ?? null;
            $this->integrationId = $keys['integration_id'] ?? null;
            $this->apiUrl = 'https://accept.paymobsolutions.com/api';
            if (empty($this->apiKey)) {
                throw new \Exception("Paymob API key is missing in gateway configuration.");
            }
            if (empty($this->iframeId)) {
                throw new \Exception("Paymob iframe ID is missing in gateway configuration.");
            }
            if (empty($this->integrationId)) {
                throw new \Exception("Paymob integration ID is missing in gateway configuration.");
            }

        } catch (\Exception $e) {
            Log::error("Paymob Service Initialization Failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * الحصول على token المصادقة من Paymob
     * 
     * @return string|null
     * @throws \Exception
     */
    public function getAuthToken()
    {
        try {
            $response = Http::timeout(30)->post("{$this->apiUrl}/auth/tokens", [
                'api_key' => $this->apiKey
            ]);

            if ($response->failed()) {
                Log::error("Paymob Auth Failed - Status: {$response->status()}, Response: {$response->body()}");
                throw new \Exception("Paymob authentication failed.");
            }

            $token = $response->json()['token'] ?? null;

            if (empty($token)) {
                throw new \Exception("Empty token received from Paymob.");
            }

            return $token;

        } catch (\Exception $e) {
            Log::error("Paymob GetAuthToken Error: " . $e->getMessage());
            throw new \Exception("Failed to get authentication token from Paymob.");
        }
    }

    /**
     * إنشاء طلب جديد في Paymob
     * 
     * @param array $userData
     * @param float $totalPrice
     * @return string|null
     * @throws \Exception
     */
    public function createOrder($userData, $totalPrice)
    {
        try {
            if (!is_array($userData)) {
                throw new \Exception("User data must be an array.");
            }

            if ($totalPrice <= 0) {
                throw new \Exception("Total price must be greater than 0.");
            }
            
            $token = $this->getAuthToken();
            
            $response = Http::timeout(30)->post("{$this->apiUrl}/ecommerce/orders", [
                'auth_token' => $token,
                'delivery_needed' => false,
                'amount_cents' => $totalPrice * 100,
                'currency' => 'EGP',
                'items' => [],
            ]);

            

            if ($response->failed()) {
                Log::error("Paymob Order Creation Failed - Status: {$response->status()}, Response: {$response->body()}");
                throw new \Exception("Failed to create Paymob order.");
            }

            $orderId = $response->json()['id'] ?? null;

            if (empty($orderId)) {
                throw new \Exception("Empty order ID received from Paymob.");
            }

            return $orderId;

        } catch (\Exception $e) {
            Log::error("Paymob CreateOrder Error: " . $e->getMessage());
            throw new \Exception("Failed to create order: " . $e->getMessage());
        }
    }

    /**
     * توليد مفتاح الدفع
     * 
     * @param string $orderId
     * @param array $userData
     * @param float $totalPrice
     * @param string $uuid
     * @return string|null
     * @throws \Exception
     */
    public function generatePaymentKey($orderId, $userData, $totalPrice, $uuid)
    {
        try {
            if (empty($orderId)) {
                throw new \Exception("Order ID is required.");
            }

            if (!is_array($userData)) {
                throw new \Exception("User data must be an array.");
            }

            if ($totalPrice <= 0) {
                throw new \Exception("Total price must be greater than 0.");
            }

            $token = $this->getAuthToken();
            $billingData = [
                'first_name'      => $userData['name'] ?? '',
                'last_name'       => $userData['name'] ?? '',
                'email'           => $userData['email'] ?? '',
                'phone_number'    => $userData['phone'] ?? '',
                'apartment'       => 'NA',
                'floor'           => 'NA',
                'street'          => 'NA',
                'building'        => 'NA',
                'shipping_method' => 'NA',
                'postal_code'     => 'NA',
                'city'            => 'NA',
                'country'         => 'EG',
                'state'           => 'NA',
            ];

            $response = Http::timeout(30)->post("{$this->apiUrl}/acceptance/payment_keys", [
                'auth_token'           => $token,
                'amount_cents'         => $totalPrice * 100,
                'expiration'           => 3600,
                'order_id'             => $orderId,
                'billing_data'         => $billingData,
                'currency'             => 'EGP',
                'integration_id'       => $this->integrationId,
                'lock_order_when_paid' => false,
            ]);

            if ($response->failed()) {
                Log::error("Paymob Payment Key Generation Failed - Status: {$response->status()}, Response: {$response->body()}");
                throw new \Exception("Failed to generate payment key.");
            }

            $paymentKey = $response->json()['token'] ?? null;

            if (empty($paymentKey)) {
                throw new \Exception("Empty payment key received from Paymob.");
            }

            return $paymentKey;

        } catch (\Exception $e) {
            Log::error("Paymob GeneratePaymentKey Error: " . $e->getMessage());
            throw new \Exception("Failed to generate payment key: " . $e->getMessage());
        }
    }

    /**
     * إنشاء رابط الدفع النهائي
     * 
     * @param array $userData
     * @param float $totalPrice
     * @param string $uuid
     * @return string
     * @throws \Exception
     */
    public function createWalletPayment($userData, $totalPrice, $uuid)
    {
        try {
            if (!is_array($userData)) {
                throw new \Exception("User data must be an array.");
            }

            if (empty($userData['email'])) {
                throw new \Exception("User email is required.");
            }

            if ($totalPrice <= 0) {
                throw new \Exception("Total price must be greater than 0.");
            }

            $orderId = $this->createOrder($userData, $totalPrice);
            $paymentKey = $this->generatePaymentKey($orderId, $userData, $totalPrice, $uuid);

            // بناء رابط الدفع
            $redirectUrl = "https://accept.paymob.com/api/acceptance/iframes/"
                . $this->iframeId
                . "?payment_token=" . $paymentKey;

            Log::info("Paymob Payment Link Generated", [
                'order_id' => $orderId,
                'user_email' => $userData['email'],
                'amount' => $totalPrice
            ]);

            return $redirectUrl;

        } catch (\Exception $e) {
            Log::error("Paymob CreateWalletPayment Error: " . $e->getMessage());
            throw new \Exception("Failed to create payment link: " . $e->getMessage());
        }
    }
}