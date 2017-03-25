<?php
/**
 * Created by PhpStorm.
 * User: Kostya
 * Date: 22.03.2017
 * Time: 14:35
 */

namespace Pdazcom\LaravelWalletOne\Tests;

class FrontendTest extends TestCase
{
    public function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('app.key', env("APP_KEY"));
    }

    public function testForm()
    {
        $response = $this->get('walletone/form', []);
        $response->assertStatus(200)->assertSee(config('wallet-one.buttonLabel'));
    }
}
