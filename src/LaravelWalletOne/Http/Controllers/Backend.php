<?php

namespace Pdazcom\LaravelWalletOne\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Pdazcom\LaravelWalletOne\Events\SuccessPayment;
use Pdazcom\LaravelWalletOne\WalletOne;

class Backend extends Controller
{
    public function payments(Request $request)
    {
        event(new SuccessPayment($request->all()));
        return WalletOne::response("OK", "Order #{$request->get('WMI_PAYMENT_NO')} payed success!");
    }
}