<?php

namespace Pdazcom\LaravelWalletOne\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Pdazcom\LaravelWalletOne\Providers\WalletoneServiceProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('wallet-one.secretKey', 'secretKey');
        $app['config']->set('wallet-one.signatureMethod', 'sha1');
        $app['config']->set('wallet-one.buttonLabel', 'Pay');
        $app['config']->set('wallet-one.walletOptions', [
            'WMI_MERCHANT_ID' => '123456',
            'WMI_CURRENCY_ID' => 'RUB',
            'WMI_SUCCESS_URL' => 'site/payment-success',
            'WMI_FAIL_URL' => 'site/payment-fail',
        ]);
    }

    protected static function callMethod($obj, $name, array $args = [])
    {
        $class = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    protected static function getProtected($obj, $prop)
    {
        $reflection = new \ReflectionClass($obj);
        $property = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}