<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Records every gateway payment (Paystack / CheqPay) for a school's subscription.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_payments', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->string('school_id')->index();
            $t->string('subscription_id')->nullable();
            $t->string('plan_id')->nullable();
            $t->string('gateway', 24);                 // paystack | cheqpay
            $t->string('reference')->unique();
            $t->integer('amount')->default(0);         // whole currency units
            $t->string('currency', 8)->default('NGN');
            $t->string('status', 16)->default('pending'); // pending | success | failed
            $t->string('email')->nullable();
            $t->text('meta')->nullable();              // JSON: gateway response
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
    }
};
