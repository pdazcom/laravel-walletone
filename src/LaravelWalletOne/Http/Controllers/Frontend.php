<?php

namespace Pdazcom\LaravelWalletOne\Http\Controllers;

use Illuminate\Routing\Controller;

class Frontend extends Controller
{
    public function form()
    {
        $data = [];
        $walletone = app('walletone');
        $walletone->addWalletOptions([
            'WMI_DESCRIPTION' => 'Pay for account balance',
            'WMI_PAYMENT_AMOUNT' => 100,
        ]);
        $data['fields'] = app('walletone')->getFields();
        return view('walletone::form', $data);
    }
}