<?php

namespace App\Services\Payments;

// Common contract for a hosted-checkout gateway (init → redirect → verify).
interface PaymentGateway
{
    /** Machine key: 'paystack' | 'cheqpay'. */
    public function key(): string;

    /** Human label for the UI. */
    public function label(): string;

    /** Are keys configured for this gateway? */
    public function configured(): bool;

    /**
     * Start a payment. Returns ['authorization_url' => ..., 'reference' => ...].
     * $amount is in whole currency units (e.g. naira); drivers convert as needed.
     */
    public function initialize(int $amount, string $email, string $reference, string $callbackUrl, array $meta = []): array;

    /** Verify a completed payment by reference. Returns ['status' => bool, 'amount' => int, 'raw' => array]. */
    public function verify(string $reference): array;

    /** Validate a webhook request and return its reference, or null if invalid. */
    public function parseWebhook(\Illuminate\Http\Request $request): ?string;
}
