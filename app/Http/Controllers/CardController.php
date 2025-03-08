<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentMethod;
use App\Models\UserCard;


class CardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api'); // Protege el controlador con JWT
    }


    public function registerCard(Request $request)
    {
        // Obtener el usuario autenticado desde el token
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Usuario no autenticado. Verifique el token enviado.',
            ], 401);
        }

        // Validar que envíen los datos correctamente
        $request->validate([
            'payment_method' => 'required|string',
        ]);



        $user = Auth::user();
        // Configurar Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Crear el Customer en Stripe si no existe
            if (!$user->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            } else {
                $customer = Customer::retrieve($user->stripe_customer_id);
            }

            // Asignar el método de pago al Customer
            $paymentMethod = PaymentMethod::retrieve($request->payment_method);
            $paymentMethod->attach(['customer' => $customer->id]);

            // Obtener detalles de la tarjeta
            $card = $paymentMethod->card;

            // Guardar datos en la tabla user_card
            $userCard = UserCard::create([
                'user_id' => $user->id,
                'stripe_customer_id' => $customer->id,
                'stripe_card_id' => $paymentMethod->id,
                'last4' => $card->last4,
                'brand' => $card->brand,
            ]);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Tarjeta registrada exitosamente',
                'stripe_response' => $paymentMethod,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createPaymentMethod(Request $request)
    {
        // Validar que se envíen los datos correctamente
        $request->validate([
            'card_number' => 'required|string',
            'exp_month' => 'required|integer',
            'exp_year' => 'required|integer',
            'cvc' => 'required|string',
        ]);

        // Configurar la clave secreta de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Crear un PaymentMethod en Stripe
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
                'status' => 'SUCCESS',
                'message' => 'PaymentMethod creado exitosamente',
                'payment_method' => $paymentMethod->id,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
