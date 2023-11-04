<?php

use Illuminate\Support\Facades\Route;

Route::post('/sso-register', 'Thuleen\SsoMfa\RegisterController@submitForm')->name('sso.submit.form.register');
Route::get('/sso-pendacc/{username}', 'Thuleen\SsoMfa\RegisterController@pendAcc')->name('sso.pending.create.ethacc');
