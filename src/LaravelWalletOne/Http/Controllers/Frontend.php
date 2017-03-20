<?php

namespace Pdazcom\LaravelWalletOne\Http\Controllers;

use Illuminate\Routing\Controller;

class Frontend extends Controller
{
    public function form()
    {
        $data = [];

        $data['fields'] = app('walletone')->getFields();
        return view('walletone::form', $data);
    }
}