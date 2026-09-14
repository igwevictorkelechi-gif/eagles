<?php

namespace App\Services\Payments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Paystack — https://paystack.com/docs/api/
class PaystackGateway implements PaymentGateway
{
    private string $secret;
    private string $public;

    public function __construct()
    {
        $this->secret = (string) config('services.paystack.secret');
        $this->public = (string) config('services.paystack.public');
    }

    public function key(): string { return 'paystack'; }
    public function label(): string { return 'Paystack'; }
    public function configured(): bool { return $this->secret !== ''; }

    public function initialize(int $amount, string $email, string $reference, string $callbackUrl, array $meta = []): array
    {
        $res = Http::withToken($this->secret)
            ->acceptJson()
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100,           // Paystack expects kobo
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'currency' => 'NGN',
                'metadata' => $meta,
            ]);

        if (! $res->successful() || ! ($res['status'] ?? false)) {
            throw new \RuntimeException('Paystack init failed: ' . $res->body());
        }

        return [
            'authorization_url' => $res['data']['authorization_url'],
            'reference' => $res['data']['reference'] ?? $reference,
        ];
    }

    public function verify(string $reference): array
    {
        $res = Http::withToken($this->secret)->acceptJson()
            ->get('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));

        $ok = $res->successful() && ($res['status'] ?? false) && ($res['data']['status'] ?? null) === 'success';

        return [
            'status' => $ok,
            'amount' => (int) (($res['data']['amount'] ?? 0) / 100),
            'raw' => $res->json() ?? [],
        ];
    }

    public function parseWebhook(Request $request): ?string
    {
        $signature = $request->header('x-paystack-signature');
        $computed = hash_hmac('sha512', $request->getContent(), $this->secret);
        if (! $signature || ! hash_equals($computed, $signature)) {
            return null;
        }
        $event = $request->json()->all();
        if (($event['event'] ?? null) !== 'charge.success') {
            return null;
        }
        return $event['data']['reference'] ?? null;
    }
}
