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
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create/{room}', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
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