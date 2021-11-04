<?php

use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/order/{orderNumber}', [OrderController::class, 'getOrderWithDetails']);
Route::get('/order/{order}/details', [OrderDetailController::class, 'getOrderDetails']);
Route::post('/order/{order}/completed', [OrderController::class, 'complete']);
Route::post('/order/{order}/shipment', [OrderController::class, 'persistShipment']);
Route::get('/branch/{branch}/orders', [OrderController::class, 'filterByBranch']);
Route::post('/order/{order}/pickup', [DeliveryController::class, 'pickup']);
Route::post('/order/{order}/packed', [DeliveryController::class, 'packed']);
Route::post('/order/{order}/start_delivery', [DeliveryController::class, 'start']);
