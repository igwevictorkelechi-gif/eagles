<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;

class MarketingController extends Controller
{
    public function home()
    {
        return view('marketing.home');
    }

    public function features()
    {
        return view('marketing.features');
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('marketing.pricing', ['plans' => $plans]);
    }

    public function faq()
    {
        return view('marketing.faq');
    }

    public function contact()
    {
        return view('marketing.contact');
    }
}
