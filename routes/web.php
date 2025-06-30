<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Volt::route('/', 'guest.beranda');

// ======================
// 👤 GUEST ROUTES
// ======================
Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
    Volt::route('/register', 'auth.register');
    Volt::route('/forgot-password', 'auth.forgot-password')->name('password.request');
    Volt::route('/reset-password/{token}', 'auth.password-reset')->name('password.reset');
});


// ======================
// 🔓 LOGOUT
// ======================
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

// ======================
// 🔐 AUTHENTICATED ROUTES
// ======================
Route::middleware('auth')->group(function () {

    // 📧 EMAIL VERIFICATION
    Volt::route('/email/verify', 'auth.verify-email')->middleware('throttle:6,1')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'Email berhasil diverifikasi!');
    })->middleware('signed')->name('verification.verify');

    Volt::route('/dashboard', 'index');
    // ======================
    // 🛡️ ADMIN ROUTES - akses penuh
    // ======================
    Route::middleware('role:1')->group(function () {
        // User Management
        Volt::route('/users', 'users.index');
        Volt::route('/users/create', 'users.create');
        Volt::route('/users/{user}/edit', 'users.edit');

        Volt::route('/roles', 'roles.index');

        Volt::route('/kriteria', 'kriteria.index');
        Volt::route('/kriteria/create', 'kriteria.create');
        Volt::route('/kriteria/{kriteria}/edit', 'kriteria.edit');

        Volt::route('/cafe', 'cafe.index');

        Volt::route('/alternatif', 'alternatif.index');
        Volt::route('/alternatif/create', 'alternatif.create');
        Volt::route('/alternatif/{id}/edit', 'alternatif.edit');
    });

    Route::middleware('role:2')->group(function () {

        Volt::route('/rankings', 'guest.rangking');
        Volt::route('/rekomendasis', 'guest.rekomendasi');
        Volt::route('/editProfile', 'guest.editprofile');
    });
});
