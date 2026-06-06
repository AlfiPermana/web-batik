<?php
namespace App\Services\payment;

use App\Services\payment\Gateways\TripayGateway;

class PaymentService
{
    protected GatewayInterface $gateway;

    public function __construct()
    {
        $this->gateway = new TripayGateway();
    }

    public function createPayment(array $data): array
    {
        return $this->gateway->createTransaction($data);
    }

    public function processWebhook(array $payload): bool
    {
        return $this->gateway->handleWebhook($payload);
    }

    public function getPaymentStatus(string $reference): array
    {
        if (method_exists($this->gateway, 'checkTransactionStatus')) {
            return $this->gateway->checkTransactionStatus($reference);
        }

        return [
            'success' => false,
            'message' => 'Gateway does not support transaction status check',
            'data' => null,
        ];
    }
}
