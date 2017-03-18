<?php

namespace Pdazcom\LaravelWalletOne;

use Illuminate\Support\ServiceProvider;

class WalletoneServiceProvider extends ServiceProvider {

    public function boot()
    {

        // resolve config
        $this->publishes([__DIR__ . '/../../../config/wallet-one.php' => config_path('wallet-one.php')], 'config');
        //$this->publishes([__DIR__ . '/../../../public/' => public_path() . "/vendor/call-request/"], 'assets');

        // routing
        $this->loadRoutesFrom(__DIR__ . '../Http/routes.php');

        // resolving views
        $this->loadViewsFrom(__DIR__ . '/../../../views', 'walletone');

    }

    public function register()
    {
        $this->app->singleton('walletone', function ($app) {
            return new WalletOne(
                config('wallet-one.secretKey'),
                config('wallet-one.walletOptions'),
                config('wallet-one.signatureMethod')
            );
        });
    }

    public function provides()
    {
        return ['walletone'];
    }

}