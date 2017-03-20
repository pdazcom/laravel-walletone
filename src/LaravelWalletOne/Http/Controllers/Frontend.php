<?php

namespace Pdazcom\LaravelWalletOne\Http\Controllers;

use Illuminate\Routing\Controller;

class Frontend extends Controller
{
    public function form()
    {
        return view('walletone::form');
    }
}