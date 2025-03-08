<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PruebaController;
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



Route::post('/register', [AuthController::class, 'register']); // Registra en la tabla de USUARIOS
Route::post('/login', [AuthController::class, 'login'])->name('login'); // Crear Token con las credenciales del USUARIO (correo, clave)
Route::get('/home', [HomeController::class, 'home']); // Verifica si el usuario esta logeado

// Ruta protegida con JWTAuth
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('/create-payment-method', [PaymentController::class, 'createPaymentMethod']); // Crear un metodo de pago
});


Route::post('/register-card', [CardController::class, 'registerCard']); // Registra tarjeta | Crear usuario en el dashboard
Route::get('/list-cards', [CardController::class, 'listCards']); // Lista las tarjtas asociadas al usuario Logeado
Route::post('/charge-card', [PaymentController::class, 'chargeCard']); // Realizar un cargo a la tarjeta (Pago)




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
