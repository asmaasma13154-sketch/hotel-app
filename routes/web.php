<?php
use App\Http\Controllers\{
    HotelController, ReservationController,
    ChatbotController, AdminController
};
use Illuminate\Support\Facades\Route;

// Page d'accueil
Route::get('/', fn() => view('welcome'))->name('home');

// Dashboard (requis par Breeze après login)
Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard')->middleware('auth');

// Hôtels (publics)
Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index');
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');

// Routes authentifiées
Route::middleware(['auth'])->group(function () {

    // Réservations — groupées sous le même préfixe
    Route::prefix('reservations')->name('reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/create/{room}', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::patch('/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('cancel');
    });

    // Chatbot
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::delete('/chatbot/history', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
});

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::patch('/reservations/{reservation}/confirm', [AdminController::class, 'confirmReservation'])->name('reservations.confirm');
    Route::resource('/hotels', HotelController::class)->except(['index', 'show']);
    Route::resource('/rooms', \App\Http\Controllers\RoomController::class)->except(['index', 'show']);
});

require __DIR__.'/auth.php';