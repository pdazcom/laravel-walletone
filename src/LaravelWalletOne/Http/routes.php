<?php

// frontend routes
Route::group([
    'prefix' => 'walletone',
    'namespace' => 'Pdazcom\LaravelWalletOne\Http\Controllers',
    'middleware' => ['web']
], function () {
    Route::any('/form', ['as' => 'walletone_form', 'uses' => 'Frontend@form']);
});

// backend payments routes
Route::group([
    'prefix' => 'walletone',
    'namespace' => 'Pdazcom\LaravelWalletOne\Http\Controllers',
    'middleware' => [\Pdazcom\LaravelWalletOne\Http\Middleware\WalletonePay::class]
], function () {
    Route::post('/payments', ['as' => 'walletone_payment', 'uses' => 'Backend@payments']);
});