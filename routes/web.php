<?php

use Illuminate\Support\Facades\Route;

Route::post('/ssomfa-auth', 'Thuleen\SsoMfa\Http\Middleware\SsoMfaMiddleware@submitOtpForm')->name('ssomfa.submit.otp.form');
Route::view('/ssomfa-auth-warning', 'ssomfa::warning');
