<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\FavoriteController;

/////////////////////////////// AUTHENTICATION ///////////////////////////////

// تسجيل مستخدم جديد
Route::post('/register', [AuthController::class, 'register']);

// تسجيل الدخول
Route::post('/login', [AuthController::class, 'login']);

// جلب بيانات المستخدم عبر التوكن
Route::post('/get-user', [AuthController::class, 'getUser']);

// العمليات التي تتطلب توثيق (sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/user/profile', [UserController::class, 'getProfile']);
    Route::post('/user/update-image', [UserController::class, 'updatePro
    fileImage']);

    // بديل أو إضافي لمسار جلب بيانات المستخدم
    Route::get('/user', function (Request $request) {
        return response()->json($request->user(), 200);
    });
});

/////////////////////////////// END AUTH ///////////////////////////////



/////////////////////////////// PRODUCTS ///////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/details', [ProductController::class, 'index']);
    Route::get('/products/filter', [ProductController::class, 'indexProduct']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/{id}/similar', [ProductController::class, 'getSimilarProducts']);
});

/////////////////////////////// END PRODUCTS ///////////////////////////////



/////////////////////////////// ORDERS ///////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    // جلب الطلبات حسب الحالة
    Route::get('/orders/{status}', [OrderController::class, 'getOrdersByStatus']);

    // تنفيذ عملية الشراء
    Route::post('/checkout', [OrderController::class, 'checkout']);
});

/////////////////////////////// END ORDERS ///////////////////////////////



/////////////////////////////// FAVORITES ///////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/{productId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{productId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites', [FavoriteController::class, 'index']);
});

/////////////////////////////// END FAVORITES ///////////////////////////////



/////////////////////////////// CART ///////////////////////////////

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add/{productId}', [CartController::class, 'store']);
    Route::put('/cart/update/{cartItemId}', [CartController::class, 'update']);
    Route::post('/cart/increment/{cartItemId}', [CartController::class, 'increment']);
    Route::post('/cart/decrement/{cartItemId}', [CartController::class, 'decrement']);
    Route::delete('/cart/remove/{cartItemId}', [CartController::class, 'destroy']);
});

/////////////////////////////// END CART ///////////////////////////////

