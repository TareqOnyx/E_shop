<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Category_Controller;
use App\Http\Controllers\Product_Controller;
use App\Http\Controllers\Review_Controller;
use App\Http\Controllers\Payment_way_Controller;
use App\Http\Controllers\Payment_Controller;
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
Route::get('/categories', [Category_Controller::class,'index']);
Route::post('/categories', [Category_Controller::class,'store']);
Route::get('/categories/{id}', [Category_Controller::class,'show']);
Route::put('/categories/{id}', [Category_Controller::class,'update']);
Route::delete('/categories/{id}', [Category_Controller::class,'destroy']);

// Products CRUD
Route::get('/products', [Product_Controller::class,'index']);
Route::post('/products', [Product_Controller::class,'store']);
Route::get('/products/{id}', [Product_Controller::class,'show']);
Route::put('/products/{id}', [Product_Controller::class,'update']);
Route::delete('/products/{id}', [Product_Controller::class,'destroy']);

// Product Stock Management
Route::post('/products/{id}/reduce-stock', [Product_Controller::class, 'reduceStock']);
Route::post('/products/{id}/increase-stock', [Product_Controller::class, 'increaseStock']);

// Reviews CRUD
Route::get('/review', [Review_Controller::class,'index']);
Route::post('/review', [Review_Controller::class,'store']);
Route::get('/review/{id}', [Review_Controller::class,'show']);
Route::put('/review/{id}', [Review_Controller::class,'update']);
Route::delete('/review/{id}', [Review_Controller::class,'destroy']);

// Payment Ways
Route::get('/payment-ways', [Payment_way_Controller::class, 'index']);
Route::post('/payment-ways', [Payment_way_Controller::class, 'store']);
Route::get('/payment-ways/{id}', [Payment_way_Controller::class, 'show']);
Route::put('/payment-ways/{id}', [Payment_way_Controller::class, 'update']);
Route::delete('/payment-ways/{id}', [Payment_way_Controller::class, 'destroy']);

// Payments
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

// Cart
Route::get('/carts/{userId}', [CartController::class, 'index']);
Route::post('/carts', [CartController::class, 'store']);
Route::put('/carts/{id}', [CartController::class, 'update']);
Route::delete('/carts/{id}', [CartController::class, 'destroy']);

// Wishlist
Route::get('/wishlists/{userId}', [WishlistController::class, 'index']);
Route::post('/wishlists', [WishlistController::class, 'store']);
Route::delete('/wishlists/{id}', [WishlistController::class, 'destroy']);

// Orders
Route::get('/orders/{userId}', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected routes (تحتاج token)
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/user/{id}', [AuthController::class, 'updateUser']);
    Route::delete('/user/{id}', [AuthController::class, 'deleteUser']);
});