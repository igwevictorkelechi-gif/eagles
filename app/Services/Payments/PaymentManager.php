<?php

namespace App\Services\Payments;

class PaymentManager
{
    /** @return array<string,PaymentGateway> */
    public static function all(): array
    {
        return [
            'paystack' => new PaystackGateway(),
            'cheqpay' => new CheqpayGateway(),
        ];
    }

    public static function get(string $key): PaymentGateway
    {
        $g = self::all()[$key] ?? null;
        if (! $g) {
            throw new \InvalidArgumentException("Unknown payment gateway: {$key}");
        }
        return $g;
    }

    /** Only the gateways that have keys configured. */
    public static function enabled(): array
    {
        return array_filter(self::all(), fn (PaymentGateway $g) => $g->configured());
    }
}
