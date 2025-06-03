<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ShowtimeController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\SystemNotificationController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Guest booking routes
Route::post('/guest/showtimes/{showtime}/book', [GuestBookingController::class, 'store']);

// Social authentication routes
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/bookings', [ProfileController::class, 'bookings']);

    // Movie routes
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{movie}', [MovieController::class, 'show']);

    // Showtime routes
    Route::get('/movies/{movie}/showtimes', [ShowtimeController::class, 'index']);
    Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show']);

    // Customer booking routes
    Route::middleware('role:customer')->group(function () {
        Route::get('/showtimes/{showtime}/available-seats', [BookingController::class, 'getAvailableSeats']);
        Route::post('/showtimes/{showtime}/book', [BookingController::class, 'store']);
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::get('/bookings/{booking}', [BookingController::class, 'show']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

        // Payment routes
        Route::post('/bookings/{booking}/payment', [PaymentController::class, 'processPayment']);
        Route::post('/profile/add-credit', [PaymentController::class, 'addCredit']);
    });

    // Admin routes
    Route::middleware('role:admin')->group(function () {
        // User management
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::get('/admin/users/{user}', [AdminController::class, 'userDetails']);
        Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspendUser']);
        Route::post('/admin/users/{user}/activate', [AdminController::class, 'activateUser']);
        Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser']);

        // Role management
        Route::get('/admin/roles', [AdminController::class, 'roles']);
        Route::post('/admin/roles', [AdminController::class, 'createRole']);
        Route::post('/admin/users/{user}/roles', [AdminController::class, 'assignRole']);
    });

    // Theater routes
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::apiResource('theaters', TheaterController::class);
    });

    // Offer routes
    Route::get('offers', [OfferController::class, 'index']);
    Route::post('offers/validate-promo', [OfferController::class, 'validatePromoCode']);
    
    Route::middleware(['role:admin|manager'])->group(function () {
        Route::apiResource('offers', OfferController::class)->except(['index']);
    });

    // System Notification routes
    Route::get('notifications', [SystemNotificationController::class, 'index']);
    Route::post('notifications/{notification}/mark-as-read', [SystemNotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-as-read', [SystemNotificationController::class, 'markAllAsRead']);
    
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('notifications', SystemNotificationController::class);
    });
}); 