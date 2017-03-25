<?php

namespace Pdazcom\LaravelWalletOne\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Pdazcom\LaravelWalletOne\Events\FailedPayment;
use Pdazcom\LaravelWalletOne\Events\SuccessPayment;
use Pdazcom\LaravelWalletOne\Http\Controllers\Backend;
use Pdazcom\LaravelWalletOne\Http\Middleware\WalletonePay;

class WalletonePayTest extends TestCase
{
    public function handleDataProvider()
    {
        return [
            [ // data set #0
                [],
                true
            ],
            [ // data set #1
                [
                    'WMI_ORDER_STATE' => 'ACCEPTED',
                    "some" => "thing",
                    "data" => "singelse",
                    'WMI_SIGNATURE' => base64_encode(pack("H*", sha1("singelsethingACCEPTED" . 'secretKey')))
                ],
                false,
            ],
            [ // data set #2
                [
                    'WMI_ORDER_STATE' => 'ACCEPTED',
                    "some" => "thing",
                    "data" => "singelse",
                    'WMI_SIGNATURE' => base64_encode(pack("H*", sha1("singelsethingACCEPTED" . 'wrongKey')))
                ],
                true
            ]
        ];
    }

    /**
     * @param $requestData
     * @param $isError
     * @dataProvider handleDataProvider
     */
    public function testHandle($requestData, $isError)
    {
        $request = new Request($requestData);
        $middleware = new WalletonePay();

        if ($isError) {
            $this->expectsEvents(FailedPayment::class);
        } else {
            $this->expectsEvents(SuccessPayment::class);
        }

        $response = $middleware->handle($request, function ($request) {
            return (new Backend())->payments($request);
        });

        self::assertInstanceOf(Response::class, $response);
        self::assertStringStartsWith(
            "WMI_RESULT=" . ($isError ? "RETRY" : "OK") . "&WMI_DESCRIPTION=",
            $response->getContent()
        );
    }
}