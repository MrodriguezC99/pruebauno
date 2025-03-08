<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /* CREAR CLIENTE */
    public function crearCustomer($email, $nombre, $descripcion = null)
    {
        return Customer::create([
            'email' => $email,
            'name' => $nombre,
            'description' => $descripcion ?? 'Cliente creado desde Laravel',
        ]);
    }



    /* CREAR PAGO */
    public function crearPago($monto, $moneda, $token)
    {
        return Charge::create([
            'amount' => $monto * 100, // En centavos
            'currency' => $moneda,
            'source' => $token,
            'description' => 'Pago con Laravel y Stripe',
        ]);
    }






}
