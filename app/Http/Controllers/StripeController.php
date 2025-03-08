<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use Stripe\Stripe;
use Stripe\PaymentMethod;


use Exception;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function pagar(Request $request)
    {

        $request->validate([
            'monto' => 'required|numeric|min:1',
            'moneda' => 'required|string',
            'token' => 'required|string'
        ]);


        try {
            $pago = $this->stripeService->crearPago(
                $request->monto,
                $request->moneda,
                $request->token
            );
            return response()->json(['success' => true, 'pago' => $pago]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function crear(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nombre' => 'required|string',
        ]);

        try {
            $customer = $this->stripeService->crearCustomer(
                $request->email,
                $request->nombre,
                $request->descripcion
            );

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create_pago(Request $request)
    {
        $request->validate([
            'card_number' => 'required|string',
            'exp_month' => 'required|integer',
            'exp_year' => 'required|integer',
            'cvc' => 'required|string',
        ]);

        try {
            // Configuramos la API Key
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Creamos el Payment Method
            $paymentMethod = PaymentMethod::create([
                'type' => 'card',
                'card' => [
                    'number' => $request->card_number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);

            return response()->json([
                'success' => true,
                'payment_method' => $paymentMethod,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
