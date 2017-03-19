<?php

namespace Pdazcom\LaravelWalletOne;

use Pdazcom\LaravelWalletOne\Exceptions\WalletOneException;

class WalletOne
{
    const BASE64_PREFIX = 'BASE64:';
    const MD5 = 'md5';
    const SHA1 = 'sha1';

    protected static $CURRENCY_ID = [
        'RUB' => 643,
        'ZAR' => 710,
        'USD' => 840,
        'EUR' => 978,
        'UAH' => 980,
        'KZT' => 398,
        'BYR' => 974,
        'TJS' => 972
    ];

    const ERROR_SIGNATURE = 101;
    const ERROR_UNKNOWN = 201;
    const ERROR_HAVE_NOT_CURRENCY = 301;

    const API_URL = 'https://wl.walletone.com/checkout/checkout/Index';
    public $secretKey = '';
    public $buttonLabel = '';
    public $walletOptions = [];

    /**
     * @var string Default sha1
     */
    public $signatureMethod = self::SHA1;

    public function __construct($secretKey, array $options = [], $signatureMethod = self::SHA1)
    {
        $this->secretKey = $secretKey;
        $this->signatureMethod = $signatureMethod;

        $this->checkOptions($options);
        $this->addWalletOptions($options);
    }

    /**
     * @param array $options
     * @throws \ErrorException
     */
    protected function checkOptions(array $options = [])
    {
        $walletOptions = $options ?: $this->walletOptions;

        $required = [
            'WMI_MERCHANT_ID',
            'WMI_PAYMENT_AMOUNT',
            'WMI_CURRENCY_ID',
            'WMI_DESCRIPTION',
            'WMI_SUCCESS_URL',
            'WMI_FAIL_URL'
        ];

        if ($walletOptions) {
            foreach ($required as $key => $val) {
                if (array_key_exists($val, $walletOptions)) {
                    unset($required[$key]);
                }
            }
        }

        if (count($required)) {
            throw new \ErrorException('Error configuration WalletOne, need set - ' . implode(', ', $required));
        }
    }

    /**
     * @param array $options this parameter will be merge with walletOptions
     * @return $this
     */
    public function addWalletOptions($options = [])
    {
        $this->walletOptions = self::array_merge_recursive_distinct($options, $this->walletOptions);
        return $this;
    }

    /**
     * @param array $options
     * @return array
     * @throws \ErrorException
     */
    public function getFields($options = [])
    {
        $fields = self::array_merge_recursive_distinct($options, $this->walletOptions);

        $this->checkOptions($fields);

        if (isset($fields['WMI_DESCRIPTION'])) {
            $fields['WMI_DESCRIPTION'] = self::BASE64_PREFIX . base64_encode($fields['WMI_DESCRIPTION']);
        }
        if (isset($fields['WMI_SUCCESS_URL'])) {
            $fields['WMI_SUCCESS_URL'] = url($fields['WMI_SUCCESS_URL']);
        }
        if (isset($fields['WMI_FAIL_URL'])) {
            $fields['WMI_FAIL_URL'] = url($fields['WMI_FAIL_URL']);
        }

        $fields['WMI_PAYMENT_AMOUNT'] = number_format($fields['WMI_PAYMENT_AMOUNT'], 2, '.', '');

        if (isset($this->secretKey) && isset($this->signatureMethod)) {
            $signature = self::getSignature($this->secretKey, $fields, $this->signatureMethod);
            $fields["WMI_SIGNATURE"] = $signature;
        }

        return $fields;
    }

    /**
     * Process signature from given POST data
     *
     * @param $secretKey
     * @param array $data
     * @param string $signatureMethod
     * @return string
     */
    protected static function getSignature($secretKey, $data, $signatureMethod = self::SHA1)
    {
        unset($data['WMI_SIGNATURE']);

        foreach ($data as $name => $val) {
            if (is_array($val)) {
                usort($val, "strcasecmp");
                $data[$name] = $val;
            }
        }
        uksort($data, "strcasecmp");
        $fieldValues = "";
        foreach ($data as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $v = iconv("utf-8", "windows-1251", $v);
                    $fieldValues .= urldecode($v);
                }
            } else {
                $value = iconv("utf-8", "windows-1251", $value);
                $fieldValues .= urldecode($value);
            }
        }

        $signature = base64_encode(pack("H*", $signatureMethod($fieldValues . $secretKey)));

        return $signature;
    }

    /**
     * @param $secretKey
     * @param array $data
     * @param string $signatureMethod
     * @return bool
     * @throws \ErrorException
     */
    public static function checkSignature($secretKey, array $data, $signatureMethod = self::SHA1)
    {
        if (empty($data)) {
            throw new WalletOneException('Empty POST data received', self::ERROR_UNKNOWN);
        }

        if (empty($secretKey) || empty($signatureMethod)) {
            throw new WalletOneException('Secret key or signature method is not configured', self::ERROR_UNKNOWN);
        }

        if (in_array($signatureMethod, [self::SHA1, self::MD5])) {

            $signature = self::getSignature($secretKey, $data, $signatureMethod);
            if ($signature === $data["WMI_SIGNATURE"]) {
                return static::checkPayment($data);
            }

            throw new WalletOneException("Signature is wrong", self::ERROR_SIGNATURE);
        }

        throw new WalletOneException("Wrong signature method", self::ERROR_UNKNOWN);
    }

    /**
     * @param array $post
     * @return bool
     * @throws WalletOneException
     */
    protected static function checkPayment(array $post)
    {
        if (strtoupper($post["WMI_ORDER_STATE"]) === "ACCEPTED") {
            return true;
        }

        throw new WalletOneException($post["WMI_ORDER_STATE"], self::ERROR_UNKNOWN);
    }

    /**
     * Returns int currency code by given ISO 4217 currency code
     *
     * @param $code
     * @return mixed
     * @throws WalletOneException
     */
    public static function CurrencyID($code)
    {
        if (isset(self::$CURRENCY_ID[$code])) {
            return self::$CURRENCY_ID[$code];
        }

        throw new WalletOneException('WalletOne do not support ' . $code, self::ERROR_HAVE_NOT_CURRENCY);
    }

    public static function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::array_merge_recursive_distinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    public static function response($state = "OK", $description = "", $code = 200)
    {
        $content = "WMI_RESULT=" . strtoupper($state) .
            "&WMI_DESCRIPTION=" . urlencode($description);
        return response($content, $code);
    }
}