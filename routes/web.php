<?php

use Illuminate\Support\Facades\Route;

Route::post('/ssomfa-auth', 'Thuleen\SsoMfa\Http\Middleware\SsoMfaMiddleware@submitOtpForm')->name('ssomfa.submit.otp.form');

// Route::group(['middleware' => ['web']], function () {
//     Route::post('/ssomfa-auth', 'Thuleen\SsoMfa\Http\Middleware\SsoMfaMiddleware@submitOtpForm')->name('ssomfa.submit.otp.form');
// });
