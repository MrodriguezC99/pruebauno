<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth:api'); // Protege el controlador con JWT
        $this->middleware('jwt.auth');
    }


    public function createPaymentMethod(Request $request)
    {

        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Usuario no autenticado. Verifique el token enviado.',
            ], 401);
        }


        // Validar que el frontend envíe el payment_method
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        // Configurar la API Key de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $user = Auth::user(); // Obtener usuario autenticado

            // Verificar si el usuario tiene un Customer en Stripe
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            // Asociar el Payment Method al Customer en Stripe
            $paymentMethod = PaymentMethod::retrieve($request->payment_method);
            $paymentMethod->attach(['customer' => $customer->id]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Método de pago creado y vinculado con éxito.',
                'payment_method_id' => $paymentMethod->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function chargeCard(Request $request)
    {
        try {
            $user = Auth::user();

            // Validar datos
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'card_id' => 'required|string'
            ]);

            if (!$user->stripe_customer_id) {
                return response()->json([
                    'status' => 'ERROR',
                    'message' => 'El usuario no tiene un cliente de Stripe asociado.'
                ], 404);
            }

            // Configurar Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Crear un PaymentIntent en Stripe
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => 5000, // Monto en centavos (ejemplo: 50.00 USD)
                'currency' => 'usd',
                'payment_method' => 'pm_1R0SzCH5L1svepCq0LqBZ4hE', // ⚠️ Asegúrate de enviar este dato
                'customer' => 'cus_RuHWlVUE0gfNsA', // Si usas un Customer, inclúyelo
                'confirm' => true, // Esto confirmará automáticamente el pago
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Pago realizado con éxito',
                'payment_intent' => $paymentIntent
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
