<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/order/{order}/pick_up', [OrderController::class, 'pickup']);
    Route::get('/my_orders/{order}', [OrderController::class, 'getMyOrders']);
    Route::get('/order/{order}/delivery_complete', [OrderController::class, 'deliveryComplete']);
});

Route::group(['middleware' => ['auth', 'role:admin']], function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});
