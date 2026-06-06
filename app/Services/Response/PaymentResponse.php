<?php

namespace App\Services\Response;

class PaymentResponse
{
    protected bool $success;
    protected string $message;
    protected ?array $data;
    protected ?string $checkoutUrl;

    public function __construct(bool $success, string $message, ?array $data = null, ?string $checkoutUrl = null)
    {
        $this->success = $success;
        $this->message = $message;
        $this->data = $data;
        $this->checkoutUrl = $checkoutUrl;
    }

    public static function success(?string $checkoutUrl, array $data = []): self
    {
        return new self(true, 'Payment transaction created successfully', $data, $checkoutUrl);
    }

    public static function failed(string $message, array $data = []): self
    {
        return new self(false, $message, $data);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCheckoutUrl(): ?string
    {
        return $this->checkoutUrl;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'checkout_url' => $this->checkoutUrl,
        ];
    }
}
