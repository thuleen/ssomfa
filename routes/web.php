<?php

use Illuminate\Support\Facades\Route;

Route::post('/ssomfa-auth', 'Thuleen\SsoMfa\AuthController@submitOtpForm')->name('ssomfa.submit.otp.form');
