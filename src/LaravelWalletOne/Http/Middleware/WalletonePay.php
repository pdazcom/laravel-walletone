<?php

namespace Pdazcom\LaravelWalletOne\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pdazcom\LaravelWalletOne\Events\FailedPayment;
use Pdazcom\LaravelWalletOne\Exceptions\WalletOneException;
use Pdazcom\LaravelWalletOne\WalletOne;

class WalletonePay
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $signatureSuccess = WalletOne::checkSignature(config('wallet-one.secretKey'),
                $request->all(), config('wallet-one.signatureMethod')
            );
        } catch (WalletOneException $e) {
            event(new FailedPayment($request->all(), $e));
            return WalletOne::response("Retry", $e->getMessage());
        }

        if (!$signatureSuccess) {
            event(new FailedPayment($request->all()));
            return WalletOne::response("Retry", "Unknown error");
        }

        return $next($request);
    }
}