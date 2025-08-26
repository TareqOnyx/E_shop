<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\http\controllers\Category_Controller;
use App\http\controllers\Product_Controller;
use App\http\controllers\Review_Controller;
use App\http\controllers\Payment_way_Controller;
use App\http\controllers\Payment_Controller;
use App\Http\Controllers\DeliveryWayController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Categories CRUD
Route::get('/categories',[Category_Controller::class,'index']);
Route::post('/categories',[Category_Controller::class,'store']);
Route::get('/categories/{id}',[Category_Controller::class,'show']);
Route::delete('/categories/{id}',[Category_Controller::class,'destroy']);
Route::put('/categories/{id}',[Category_Controller::class,'update']);

// Products CRUD
Route::get('/products',[Product_Controller::class,'index']);
Route::post('/products',[Product_Controller::class,'store']);
Route::get('/products/{id}',[Product_Controller::class,'show']);
Route::delete('/products/{id}',[Product_Controller::class,'destroy']);
Route::put('/products/{id}',[Product_Controller::class,'update']);

// Reviews CRUD
Route::get('/review',[Review_Controller::class,'index']);
Route::post('/review',[Review_Controller::class,'store']);
Route::get('/review/{id}',[Review_Controller::class,'show']);
Route::delete('/review/{id}',[Review_Controller::class,'destroy']);
Route::put('/review/{id}',[Review_Controller::class,'update']);

//  Routes للـ PaymentWays
Route::get('/payment-ways', [Payment_way_Controller::class, 'index']);
Route::post('/payment-ways', [Payment_way_Controller::class, 'store']);
Route::get('/payment-ways/{id}', [Payment_way_Controller::class, 'show']);
Route::put('/payment-ways/{id}', [Payment_way_Controller::class, 'update']);
Route::delete('/payment-ways/{id}', [Payment_way_Controller::class, 'destroy']);

//  Routes للـ Payments
Route::get('/payments', [Payment_Controller::class, 'index']);
Route::post('/payments', [Payment_Controller::class, 'store']);
Route::get('/payments/{id}', [Payment_Controller::class, 'show']);
Route::put('/payments/{id}', [Payment_Controller::class, 'update']);
Route::delete('/payments/{id}', [Payment_Controller::class, 'destroy']);

// Delivery Ways
Route::get('/delivery-ways', [DeliveryWayController::class, 'index']);
Route::post('/delivery-ways', [DeliveryWayController::class, 'store']);
Route::get('/delivery-ways/{id}', [DeliveryWayController::class, 'show']);
Route::put('/delivery-ways/{id}', [DeliveryWayController::class, 'update']);
Route::delete('/delivery-ways/{id}', [DeliveryWayController::class, 'destroy']);

// Deliveries
Route::get('/deliveries', [DeliveryController::class, 'index']);
Route::post('/deliveries', [DeliveryController::class, 'store']);
Route::get('/deliveries/{id}', [DeliveryController::class, 'show']);
Route::put('/deliveries/{id}', [DeliveryController::class, 'update']);
Route::delete('/deliveries/{id}', [DeliveryController::class, 'destroy']);

// Routes for Cart
Route::get('/carts/{userId}', [CartController::class, 'index']);
Route::post('/carts', [CartController::class, 'store']);
Route::put('/carts/{id}', [CartController::class, 'update']);
Route::delete('/carts/{id}', [CartController::class, 'destroy']);

// Routes for Wishlist
Route::get('/wishlists/{userId}', [WishlistController::class, 'index']);
Route::post('/wishlists', [WishlistController::class, 'store']);
Route::delete('/wishlists/{id}', [WishlistController::class, 'destroy']);

// Routes for Orders
Route::get('/orders/{userId}', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
