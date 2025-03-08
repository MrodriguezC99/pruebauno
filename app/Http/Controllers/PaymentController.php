<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PaymentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api'); // Protege el controlador con JWT
    }


    public function createPaymentMethod(Request $request)
    {


        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user(); // Obtener el usuario autenticado


        // Configurar la API de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Crear un cliente en Stripe si no existe
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

            // Obtener el PaymentMethod desde Stripe
            $paymentMethod = PaymentMethod::retrieve($request->payment_method);

            // Asociar el método de pago al cliente
            $paymentMethod->attach(['customer' => $customer->id]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Método de pago registrado exitosamente',
                'payment_method' => $paymentMethod,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
