<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FrontendController;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\ProductController;
use App\Http\Controllers\Api\Mobile\CartController;
use App\Http\Controllers\Api\Mobile\OrderController;


Route::group(['namespace' => 'Api','prefix'=>'v1','middleware' => 'api'], function(){
    
     Route::get('app-config', [FrontendController::class, 'appconfig']);
     Route::get('slider', [FrontendController::class, 'slider']);
     Route::get('category-menu', [FrontendController::class, 'categorymenu']);
     Route::get('hotdeal-product', [FrontendController::class, 'hotdealproduct']);
     Route::get('homepage-product', [FrontendController::class, 'homepageproduct']);
     Route::get('footer-menu-left', [FrontendController::class, 'footermenuleft']);
     Route::get('footer-menu-right', [FrontendController::class, 'footermenuright']);
     Route::get('social-media', [FrontendController::class, 'socialmedia']);
     Route::get('contactinfo', [FrontendController::class, 'contactinfo']);
     
    //  Home Page Api End =================================
    
    Route::get('category/{id}', [FrontendController::class, 'catproduct']);
    

});

// ============================================
// Mobile API Routes (Flutter App)
// ============================================

// Public Routes
Route::prefix('v1/mobile')->group(function () {
    
    // Authentication (Public)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // Products (Public)
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('featured', [ProductController::class, 'featured']);
        Route::get('hot-deals', [ProductController::class, 'hotDeals']);
        Route::get('category/{categoryId}', [ProductController::class, 'byCategory']);
        Route::get('{id}', [ProductController::class, 'show']);
    });

    // Order Tracking (Public)
    Route::get('orders/track/{invoiceId}', [OrderController::class, 'track']);
});

// Protected Routes (Require Authentication)
Route::prefix('v1/mobile')->middleware('auth:sanctum')->group(function () {
    
    // Authentication (Protected)
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
    });

    // Cart (Protected)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::get('count', [CartController::class, 'count']);
        Route::post('add', [CartController::class, 'add']);
        Route::put('{id}', [CartController::class, 'update']);
        Route::delete('{id}', [CartController::class, 'remove']);
        Route::delete('clear', [CartController::class, 'clear']);
    });

    // Orders (Protected)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('{id}', [OrderController::class, 'show']);
        Route::post('/', [OrderController::class, 'store']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
