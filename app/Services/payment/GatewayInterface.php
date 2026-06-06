<?php
namespace App\Services\payment;

interface GatewayInterface
{
    public function createTransaction(array $data): array;
    public function verifySignature(string $signature, string $payload): bool;
    public function handleWebhook(array $payload): bool;
}