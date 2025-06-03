<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;


// Public routes
Route::get('/', [MovieController::class, 'index'])->name('home');
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show'); 
Route::get('/movies/{movie}/showtimes/Payment', [PaymentController::class, 'Payment'])->name('showtimes.Payment');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
     Route::get('/showusers', [UserController::class, 'showusers'])->name('show.users');
    
    // Credit management (Staff only)
    Route::middleware('role:staff')->group(function () {
        Route::post('/users/{user}/credit', [UserController::class, 'addCredit'])->name('users.add-credit');
    });

    // Movie management (Admin/Manager only)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
        Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');
        Route::get('/movies/{movie}/edit', [MovieController::class, 'edit'])->name('movies.edit');
        Route::put('/movies/{movie}', [MovieController::class, 'update'])->name('movies.update');
        Route::delete('/movies/{movie}', [MovieController::class, 'destroy'])->name('movies.destroy');
    });

    // Showtime management (Admin/Manager only)
    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/movies/{movie}/showtimes/create', [ShowtimeController::class, 'create'])->name('showtimes.create');
        Route::post('/movies/{movie}/showtimes', [ShowtimeController::class, 'store'])->name('showtimes.store');
        Route::get('/showtimes/{showtime}/edit', [ShowtimeController::class, 'edit'])->name('showtimes.edit');
        Route::put('/showtimes/{showtime}', [ShowtimeController::class, 'update'])->name('showtimes.update');
        Route::delete('/showtimes/{showtime}', [ShowtimeController::class, 'destroy'])->name('showtimes.destroy');
    });

    // Booking routes (Customers only)
    Route::middleware('role:customer')->group(function () {
        Route::get('/showtimes/{showtime}/book', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/showtimes/{showtime}/book', [BookingController::class, 'store'])->name('bookings.store');
    });

    // Booking management (All authenticated users can view their bookings)
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // User management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Role management
        Route::get('/roles', [AdminController::class, 'roles'])->name('roles.index');
        Route::get('/roles/create', [AdminController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [AdminController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [AdminController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [AdminController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [AdminController::class, 'destroyRole'])->name('roles.destroy');


        
    });
});
