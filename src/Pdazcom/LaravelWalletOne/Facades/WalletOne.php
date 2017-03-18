<?php

namespace Pdazcom\LaravelWalletOne\Facades;

use Illuminate\Support\Facades\Facade;

class WalletOne extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'walletone';
    }
}