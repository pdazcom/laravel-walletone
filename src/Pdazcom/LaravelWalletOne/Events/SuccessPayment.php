<?php

namespace Pdazcom\LaravelWalletOne\Events;

class SuccessPayment
{
    public $postData = [];

    public function __construct(array $postData)
    {
        $this->postData = $postData;
    }
}