<?php

// Route group for authenticated users only

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('logout', 'Auth\LoginController@logout');
});

// Routes for guests only
Route::group(['middleware' => ['guest:api']], function () {
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend', 'Auth\VerificationController@resend');
    Route::post('login', 'Auth\LoginController@login');
});