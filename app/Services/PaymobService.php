namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env("PAYMOB_API_KEY");
        $this->apiUrl = 'https://accept.paymobsolutions.com/api';
    }

    // 1. الحصول على Token
    public function getAuthToken()
    {
        $response = Http::post("{$this->apiUrl}/auth/tokens", [
            "api_key" => $this->apiKey
        ]);

        return $response->json()['token'] ?? null;
    }

    // 2. إنشاء Order
    public function createOrder($userData, $totalPrice)
    {
        $token = $this->getAuthToken();
        if (!$token) {
            throw new \Exception('Unable to authenticate with Paymob.');
        }

        $response = Http::post("{$this->apiUrl}/ecommerce/orders", [
            "auth_token" => $token,
            "delivery_needed" => false,
            "amount_cents" => $totalPrice * 100, // تحويل المبلغ إلى سنتات
            "currency" => 'EGP',
            "items" => []
        ]);

        return $response->json()['id'] ?? null;
    }
}