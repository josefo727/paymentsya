<?php

use App\Http\Controllers\Api\BankController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentResultController;
use App\Http\Controllers\Api\PaymentUrlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('order-info', OrderController::class)->middleware('setclient')->name('api.order-info');
Route::get('banks', BankController::class)->middleware('setclient')->name('api.bank-list');
Route::post('payment-url', PaymentUrlController::class)->middleware('setclient')->name('api.payment-url');
Route::post('payment-result', PaymentResultController::class)->name('api.payment-result');


