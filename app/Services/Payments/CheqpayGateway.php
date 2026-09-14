<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// CheqPay — hosted checkout (init → redirect → verify).
//
// CheqPay's endpoints and field names are configurable in config/services.php
// so this driver can be aligned to their current API docs without code changes.
// Defaults follow the common Nigerian-gateway shape (bearer secret; init returns
// a checkout URL + reference; verify by reference; webhook signed with the secret).
class CheqpayGateway implements PaymentGateway
{
    private string $secret;
    private string $public;
    private string $base;

    public function __construct()
    {
        $this->secret = (string) config('services.cheqpay.secret');
        $this->public = (string) config('services.cheqpay.public');
        $this->base = rtrim((string) config('services.cheqpay.base_url'), '/');
    }

    public function key(): string { return 'cheqpay'; }
    public function label(): string { return 'CheqPay'; }
    public function configured(): bool { return $this->secret !== '' && $this->base !== ''; }

    public function initialize(int $amount, string $email, string $reference, string $callbackUrl, array $meta = []): array
    {
        $path = config('services.cheqpay.init_path', '/transactions/initialize');
        $res = Http::withToken($this->secret)->acceptJson()->post($this->base . $path, [
            'email' => $email,
            'amount' => $amount,
            'reference' => $reference,
            'currency' => config('services.cheqpay.currency', 'NGN'),
            'callback_url' => $callbackUrl,
            'redirect_url' => $callbackUrl,
            'metadata' => $meta,
        ]);

        if (! $res->successful()) {
            throw new \RuntimeException('CheqPay init failed: ' . $res->body());
        }
        $data = $res->json('data') ?? $res->json() ?? [];

        // Accept the common field names for the hosted checkout URL.
        $url = $data['authorization_url'] ?? $data['checkout_url'] ?? $data['payment_url'] ?? $data['link'] ?? null;
        if (! $url) {
            throw new \RuntimeException('CheqPay init: no checkout URL in response: ' . $res->body());
        }

        return [
            'authorization_url' => $url,
            'reference' => $data['reference'] ?? $reference,
        ];
    }

    public function verify(string $reference): array
    {
        $path = config('services.cheqpay.verify_path', '/transactions/verify/');
        $res = Http::withToken($this->secret)->acceptJson()->get($this->base . $path . rawurlencode($reference));
        $data = $res->json('data') ?? $res->json() ?? [];
        $status = strtolower((string) ($data['status'] ?? $data['payment_status'] ?? ''));
        $ok = $res->successful() && in_array($status, ['success', 'successful', 'completed', 'paid'], true);

        return [
            'status' => $ok,
            'amount' => (int) ($data['amount'] ?? 0),
            'raw' => $res->json() ?? [],
        ];
    }

    public function parseWebhook(Request $request): ?string
    {
        // Signature header name is configurable; default to a common one.
        $header = config('services.cheqpay.signature_header', 'x-cheqpay-signature');
        $signature = $request->header($header);
        $computed = hash_hmac('sha512', $request->getContent(), $this->secret);
        // Some gateways use sha256; accept either to be tolerant.
        $computed256 = hash_hmac('sha256', $request->getContent(), $this->secret);
        if (! $signature || ! (hash_equals($computed, $signature) || hash_equals($computed256, $signature))) {
            return null;
        }
        $event = $request->json()->all();
        $data = $event['data'] ?? $event;
        return $data['reference'] ?? null;
    }
}
