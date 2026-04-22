<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'authentification (Laravel Breeze)
| Ces routes sont incluses dans web.php via require __DIR__.'/auth.php'
|--------------------------------------------------------------------------
*/

// Connexion
Route::middleware('guest')->group(function () {
    Route::get('connexion',  [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('connexion', [AuthenticatedSessionController::class, 'store']);

    Route::get('inscription',  [RegisteredUserController::class, 'create'])->name('register');
    Route::post('inscription', [RegisteredUserController::class, 'store']);

    Route::get('mot-de-passe-oublie', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('mot-de-passe-oublie', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reinitialiser-mot-de-passe/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reinitialiser-mot-de-passe', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');
});

// Déconnexion
Route::middleware('auth')->group(function () {
    Route::post('deconnexion', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
