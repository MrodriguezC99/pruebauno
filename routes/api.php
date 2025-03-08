<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/* Registrar nuevo usuario al sistema */
Route::post('/register', [AuthController::class, 'register']);
/* Login */
//Route::post('/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


/* Route::middleware('auth:api')->get('/home', [HomeController::class, 'home']); */
Route::get('/home', [HomeController::class, 'home']);








Route::post('/payment-method', [PaymentController::class, 'createPaymentMethod']);





/* Route::middleware('auth:api')->post('/register-card', [CardController::class, 'registerCard']); */
Route::post('/register-card', [CardController::class, 'registerCard']); // Crear usuario en el dashboard

/* Route::post('/create-payment-method', [CardController::class, 'createPaymentMethod']); */



/* Route::post('/crear-customer', [StripeController::class, 'crear']);
Route::post('/create-payment-method', [StripeController::class, 'create_pago']);
Route::post('/pagar', [StripeController::class, 'pagar']); */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
