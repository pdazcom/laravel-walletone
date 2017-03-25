# Laravel WalletOne

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Implementation of WalletOne payments for laravel 5. There are form of payment and a middleware for processing requests 
for confirmation of payments from the walletone service. Based on two events - SuccessPayment and FailedPayment.

## Install

Via Composer

``` bash
$ composer require pdazcom/laravel-walletone
```

Then in config/app.php add service-provider and facade alias:

```
'providers' => [
    ...
    Pdazcom\LaravelWalletOne\Providers\WalletoneServiceProvider::class,
    ...
];

'aliases' => [
    ...
    Pdazcom\LaravelWalletOne\Providers\WalletoneServiceProvider::class,
    ...
];
```

## Usage

First of all you need to run:
```
php artisan vendor:publish --provider='Pdazcom\LaravelWalletOne\Providers\WalletoneServiceProvider' 
```

and then fill `config/wallet-one.php` file.

Then you can use `\WalletOne` facade.

To add options use `\WalletOne::addWalletOptions($options)`

```
$options = [
   'WMI_DESCRIPTION' => 'Pay for account balance',
   'WMI_PAYMENT_AMOUNT' => 100,
];

\WalletOne::addWalletOptions($options)
```

To get fields for payment form use `\WalletOne::getFields()`. And send it to your view:
```$xslt
$fields = \WalletOne::getFields()
```

To include payment form to your page just include it to view:
```
@include('wallet-one:form', $fields)
```

To process requests of WalletOne payment notifications just create listeners of two events:
`Pdazcom\LaravelWalletOne\Events\FailedPayment` and `Pdazcom\LaravelWalletOne\Events\SuccessPayment`

```
// FailedPaymentListener
public function handle(FailedPayment $event)
{
    $postData = $event->postData;
    $exception = $event->exception
}
```
```
// SuccessPaymentListener
public function handle(SuccessPayment $event)
{
    $postData = $event->postData;
}
```

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email kostya.dn@gmail.com instead of using the issue tracker.

## Credits

- [Konstantin A.][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/pdazcom/laravel-walletone.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/pdazcom/laravel-walletone/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/pdazcom/laravel-walletone.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/pdazcom/laravel-walletone.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/pdazcom/laravel-walletone.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/pdazcom/laravel-walletone
[link-travis]: https://travis-ci.org/pdazcom/laravel-walletone
[link-scrutinizer]: https://scrutinizer-ci.com/g/pdazcom/laravel-walletone/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/pdazcom/laravel-walletone
[link-downloads]: https://packagist.org/packages/pdazcom/laravel-walletone
[link-author]: https://github.com/pdazcom
[link-contributors]: ../../contributors