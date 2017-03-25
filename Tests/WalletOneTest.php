<?php
/**
 * Created by PhpStorm.
 * User: Kostya
 * Date: 20.03.2017
 * Time: 15:05
 */

namespace Pdazcom\LaravelWalletOne\Tests;


use Pdazcom\LaravelWalletOne\Exceptions\WalletOneException;
use Pdazcom\LaravelWalletOne\WalletOne;


class WalletOneTest extends TestCase
{
    public function optionsProvider()
    {
        return [
            // data set #0
            [
                [
                    'WMI_MERCHANT_ID' => 123456,
                    'WMI_PAYMENT_AMOUNT' => 100,
                    'WMI_CURRENCY_ID' => 'RUB',
                    'WMI_DESCRIPTION' => "Some description",
                    'WMI_SUCCESS_URL' => 'some/url',
                    'WMI_FAIL_URL' => 'some/fail_url',
                ],
                null
            ],
            // data set #1
            [
                [],
                \ErrorException::class
            ]
        ];
    }

    /**
     * @param $options
     * @dataProvider optionsProvider
     */
    public function testCreate($options)
    {
        $walletone = new WalletOne("secretKey", $options);

        static::assertInstanceOf(WalletOne::class, $walletone);
    }

    /**
     * @param $options
     * @param $expectsException
     * @dataProvider optionsProvider
     */
    public function testGetFields($options, $expectsException)
    {
        $walletone = new WalletOne("secretKey", $options);

        if (!empty($expectsException)) {
            $this->expectException($expectsException);
        }

        $fields = $walletone->getFields();
        static::assertCount(count($options) + 1, $fields);
        static::assertArrayHasKey('WMI_SIGNATURE', $fields);
    }

    /**
     * @param $options
     * @dataProvider optionsProvider
     */
    public function testAddWalletOption($options)
    {
        $walletone = new WalletOne("secretKey", $options);
        $walletone->addWalletOptions(["SOME_ADDITIONAL_OPTION" => "TEST_VALUE"]);

        $walletOptions = self::getProtected($walletone, 'walletOptions');
        static::assertArrayHasKey('SOME_ADDITIONAL_OPTION', $walletOptions);
        static::assertEquals('TEST_VALUE', $walletOptions['SOME_ADDITIONAL_OPTION']);
    }

    public function testGetSignature()
    {
        $options = ["some" => "thing", 'something' => 'else'];
        $walletone = new WalletOne('secretKey', $options);
        $expectsSignature = base64_encode(pack("H*", sha1("thingelse" . 'secretKey')));
        $sigature = self::callMethod($walletone, 'getSignature', ['secretKey', $options]);

        static::assertEquals($expectsSignature, $sigature);
    }

    public function checkSignatureProvider()
    {
        return [
            [ //data set #0
                "secretKey",
                [
                    "some" => "thing",
                    "data" => "singelse",
                    'WMI_SIGNATURE' => base64_encode(pack("H*", sha1("singelsethingACCEPTED" . 'secretKey'))),
                    'WMI_ORDER_STATE' => 'ACCEPTED'
                ],
                null,
                null,
            ],
            [ //data set #1
                "secretKey",
                [
                    "some" => "thing",
                    "data" => "singelse",
                    'WMI_SIGNATURE' => base64_encode(pack("H*", sha1("singelsething" . 'secretKey')))
                ],
                "Unknown order state",
                WalletOne::ERROR_UNKNOWN,
            ],
            [ //data set #2
                'secretKey',
                [],
                "Empty POST data received",
                WalletOne::ERROR_UNKNOWN,
            ],
            [ //data set #3
                '',
                [
                    'some' => 'thing',
                    'WMI_SIGNATURE' => "wrong_signature"
                ],
                "Secret key or signature method is not configured",
                WalletOne::ERROR_UNKNOWN,
            ],
            [ //data set #4
                "secretKey",
                [
                    'some' => 'thing',
                    'WMI_SIGNATURE' => "wrong_signature"
                ],
                "Signature is wrong",
                WalletOne::ERROR_SIGNATURE,
            ]
        ];
    }

    /**
     * @param $secretKey
     * @param $data
     * @param $expectsExceptionMessage
     * @param $expectsExceptionCode
     * @dataProvider checkSignatureProvider
     */
    public function testCheckSignature($secretKey, $data, $expectsExceptionMessage, $expectsExceptionCode)
    {
        if (!empty($expectsExceptionMessage)) {
            $this->expectException(WalletOneException::class);
            $this->expectExceptionMessage($expectsExceptionMessage);
            $this->expectExceptionCode($expectsExceptionCode);
        }

        $checked = WalletOne::checkSignature($secretKey, $data);
        static::assertTrue($checked);
    }

    public function currencyIDProvider()
    {
        return [
            [
                'RUB',
                643,
                false
            ],
            [
                'UAH',
                980,
                false
            ],
            [
                'WRONG',
                null,
                true
            ],
        ];
    }

    /**
     * @param $currencyCode
     * @param $expectedCurrencyID
     * @param $isError
     * @dataProvider currencyIDProvider
     */
    public function testCurrencyID($currencyCode, $expectedCurrencyID, $isError)
    {
        if ($isError) {
            $this->expectException(WalletOneException::class);
            $this->expectExceptionMessage('WalletOne do not support ' . $currencyCode);
            $this->expectExceptionCode(WalletOne::ERROR_HAVE_NOT_CURRENCY);
        }

        $currencyID = WalletOne::currencyID($currencyCode);
        static::assertEquals($expectedCurrencyID, $currencyID);
    }
}
