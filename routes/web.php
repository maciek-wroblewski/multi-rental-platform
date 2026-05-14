<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RentalController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/me', function () {
    return Auth::user();
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->get('/my-items', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    return $user()->items()->with('category')->get();
});

Route::middleware('auth')->post('/rentals', [RentalController::class, 'store']);

Route::middleware('auth')->get('/my-rentals', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    return $user->rentals()->where('status', 'active')->with('item')->get();
});

Route::middleware('auth')->patch('/rentals/{rental}/return', [RentalController::class, 'returnRental']);

Route::middleware('auth')->patch('/rentals/{rental}/cancel', [RentalController::class, 'cancelRental']);

require __DIR__.'/auth.php';
