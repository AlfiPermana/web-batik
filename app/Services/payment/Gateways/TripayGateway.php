<?php
namespace App\Services\payment\Gateways;

use App\Services\payment\GatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TripayGateway implements GatewayInterface
{
    protected ?string $apiKey;
    protected ?string $privateKey;
    protected ?string $merchantCode;
    protected ?string $paymentUrl;
    protected ?string $statusUrl;

    public function __construct()
    {
        $this->apiKey = config('services.tripay.api_key') ?? '';
        $this->privateKey = config('services.tripay.merchant_key') ?? config('services.tripay.private_key') ?? '';
        $this->merchantCode = config('services.tripay.merchant_code') ?? '';
        $this->paymentUrl = config('services.tripay.payment_url') ?? '';
        $this->statusUrl = config('services.tripay.status_url') ?? '';
    }

    public function createTransaction(array $data): array
    {
        $merchantRef = $data['merchant_ref'];
        $amount = (int)$data['amount'];
        $method = $data['method']; // e.g., 'BCAVA', 'BRIVA', 'QRIS', 'DANACASH', 'OVOBANK', etc.

        Log::info('TripayGateway::createTransaction called', [
            'method' => $method,
            'merchant_ref' => $merchantRef,
            'amount' => $amount,
        ]);

        // Validasi method
        $allowedMethods = array_keys(config('payment.methods', []));
        Log::info('Checking allowed methods', [
            'allowed_methods' => $allowedMethods,
            'method_to_check' => $method,
            'is_allowed' => in_array($method, $allowedMethods),
        ]);

        if (!in_array($method, $allowedMethods)) {
            Log::warning("Invalid Tripay payment method: {$method}", [
                'allowed' => $allowedMethods,
            ]);
            return [
                'success' => false,
                'message' => "Invalid payment method: {$method}",
                'data' => null
            ];
        }

        // PENTING: Tripay signature adalah SHA256-HMAC dari: merchant_code + merchant_ref + amount (concatenated)
        // Signature TIDAK include order_items
        // merchant_code hanya digunakan untuk signature, JANGAN di-include dalam JSON payload
        $signatureString = $this->merchantCode . $merchantRef . $amount;
        $signature = hash_hmac('sha256', $signatureString, $this->privateKey);

        try {
            // Log signature details untuk debugging
            Log::debug('Tripay Signature Calculation', [
                'merchant_code' => $this->merchantCode,
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'signature_string' => $signatureString,
                'signature' => $signature,
            ]);

            // Build full payload - JANGAN include merchant_code dalam JSON
            $fullPayload = [
                'method' => $method,
                'merchant_ref' => $merchantRef,
                'amount' => $amount,
                'customer_name' => $data['customer_name'] ?? 'Customer',
                'customer_email' => $data['customer_email'] ?? '',
                'customer_phone' => $data['customer_phone'] ?? '',
                'callback_url' => config('services.tripay.callback_url'),
                'return_url' => $this->getReturnUrl($data),
                'signature' => $signature,
            ];

            // Tambah order_items (jika ada) SETELAH signature
            if (!empty($data['items'])) {
                $fullPayload['order_items'] = $data['items'];
            }

            Log::debug('Tripay API Request', [
                'url' => $this->paymentUrl,
                'method' => $method,
                'payload_keys' => array_keys($fullPayload),
                'payload' => $fullPayload,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($this->paymentUrl, $fullPayload);

            $result = $response->json();
            
            // Log response untuk debugging
            Log::debug('Tripay API Response Status', ['status' => $response->status(), 'successful' => $response->successful()]);
            Log::debug('Tripay API Response Body', $result);

            // Normalize Tripay response
            // IMPORTANT: Tripay field mapping:
            // - VA payment code: 'pay_code'
            // - QRIS image: 'qr_url' (bukan qr_image_url)
            // - Payment method name: 'payment_name'
            $data = $result['data'] ?? [];
            return [
                'success' => ($result['success'] ?? false) === true,
                'message' => $result['message'] ?? 'Payment transaction created',
                'data' => [
                    'reference' => $data['reference'] ?? null,
                    'checkout_url' => $data['checkout_url'] ?? null,
                    'merchant_ref' => $data['merchant_ref'] ?? $merchantRef,
                    'amount' => $data['amount'] ?? $amount,
                    'method' => $data['payment_method'] ?? $method,
                    'method_name' => $data['payment_name'] ?? config("payment.methods.{$method}.name", $method),
                    'payment_name' => $data['payment_name'] ?? null,
                    'status' => $data['status'] ?? 'UNPAID',
                    // For Virtual Account methods - Tripay uses 'pay_code' for VA number
                    'pay_code' => $data['pay_code'] ?? null,
                    'customer_id' => $data['pay_code'] ?? null, // Alias for compatibility
                    'account_number' => $data['pay_code'] ?? null, // Alias for compatibility
                    // For QRIS methods - Tripay uses 'qr_url'
                    'qr_url' => $data['qr_url'] ?? null,
                    'qr_image_url' => $data['qr_url'] ?? null, // Alias for compatibility
                    'qr_string' => $data['qr_string'] ?? null,
                    // Reference for convenience store
                    'reference_code' => $data['reference'] ?? null,
                    // Customer info
                    'customer_name' => $data['customer_name'] ?? null,
                    'customer_email' => $data['customer_email'] ?? null,
                    'customer_phone' => $data['customer_phone'] ?? null,
                    // Payment info
                    'amount_received' => $data['amount_received'] ?? null,
                    'total_fee' => $data['total_fee'] ?? null,
                    'fee_merchant' => $data['fee_merchant'] ?? null,
                    'fee_customer' => $data['fee_customer'] ?? null,
                    // Expiration info (Tripay)
                    'expired_time' => $data['expired_time'] ?? null,
                    'expired_at' => $data['expired_at'] ?? null,
                    // Instructions (for display)
                    'instructions' => $data['instructions'] ?? [],
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Tripay API Exception', [
                'error' => $e->getMessage(),
                'method' => $method,
                'amount' => $amount,
            ]);
            return [
                'success' => false, 
                'message' => 'Failed to create payment transaction: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function verifySignature(string $signature, string $payload): bool
    {
        $expected = hash_hmac('sha256', $payload, $this->privateKey);
        return hash_equals($expected, $signature);
    }

    public function checkTransactionStatus(string $reference): array
    {
        if ($reference === '') {
            return [
                'success' => false,
                'message' => 'Missing Tripay reference',
                'data' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(15)->get($this->statusUrl, [
                'reference' => $reference,
            ]);

            $result = $response->json();
            $rawData = $result['data'] ?? $result;
            $message = (string) ($result['message'] ?? ($rawData['message'] ?? ''));
            $normalizedMessage = strtoupper($message);
            $normalizedStatus = match (true) {
                str_contains($normalizedMessage, 'UNPAID') => 'UNPAID',
                str_contains($normalizedMessage, 'DIBAYAR'),
                preg_match('/\bPAID\b/', $normalizedMessage) === 1 => 'PAID',
                str_contains($normalizedMessage, 'EXPIRED'),
                str_contains($normalizedMessage, 'KADALUARSA') => 'EXPIRED',
                str_contains($normalizedMessage, 'FAILED'),
                str_contains($normalizedMessage, 'GAGAL') => 'FAILED',
                str_contains($normalizedMessage, 'REFUND') => 'REFUNDED',
                default => null,
            };
            $normalizedData = is_array($rawData) ? $rawData : [];
            if ($normalizedStatus) {
                $normalizedData['status'] = $normalizedStatus;
            }
            $normalizedData['reference'] = $normalizedData['reference'] ?? $reference;

            Log::debug('Tripay check-status response', [
                'reference' => $reference,
                'url' => $this->statusUrl,
                'status' => $response->status(),
                'successful' => $response->successful(),
                'response' => $result,
                'normalized_status' => $normalizedStatus,
            ]);

            return [
                'success' => $response->successful() && (($result['success'] ?? true) !== false),
                'message' => $result['message'] ?? 'Tripay status fetched',
                'data' => $normalizedData,
            ];
        } catch (\Exception $e) {
            Log::error('Tripay check-status exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to fetch Tripay status: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    public function handleWebhook(array $payload): bool
    {
        // Validasi signature dari Tripay
        if (!$this->verifySignature($payload['signature'], $payload['merchant_ref'] . $payload['order_id'] . $payload['amount'])) {
            return false;
        }

        // Cari order berdasarkan merchant_ref
        $order = \App\Models\Order::where('merchant_ref', $payload['merchant_ref'])->first();
        if (!$order) return false;

        // Update status berdasarkan code
        // referensi: https://tripay.co.id/developer#daftar-kode-status-pembayaran
        $statusMap = [
            'PAID' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            'PENDING' => 'pending',
        ];

        $status = $statusMap[$payload['status']] ?? 'unknown';

        DB::transaction(function () use ($order, $status, $payload) {
            $order->update(['status' => $status]);
            $order->statusHistory()->create([
                'status' => $status,
                'note' => json_encode($payload),
            ]);

            // Trigger event, job, atau kirim notifikasi jika perlu
            if ($status === 'paid') {
                // TODO: Implement OrderPaid event untuk mengirim email, update dashboard, dll
                // event(new \App\Events\OrderPaid($order));
                Log::info('Order payment confirmed', ['order_id' => $order->id, 'status' => $status]);
            }
        });

        return true;
    }

    /**
     * Generate return URL untuk redirect user setelah pembayaran selesai
     * Tripay akan redirect ke URL ini dengan status query parameter
     */
    private function getReturnUrl(array $data): string
    {
        // Jika ada custom return_url, gunakan itu
        if (!empty($data['return_url'])) {
            return $data['return_url'];
        }

        // Jika ada booking_id, redirect ke confirmation page
        if (!empty($data['booking_id'])) {
            return route('workshop.booking.confirmation', $data['booking_id']);
        }

        // Default: redirect ke home
        return url('/');
    }
}
