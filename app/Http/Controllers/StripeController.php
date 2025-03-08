<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Stripe\Customer;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $this->middleware('jwt.auth');
    }






}
