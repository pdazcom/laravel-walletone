<form id="walletone-form" method="post" url="{{ \Pdazcom\LaravelWalletOne\WalletOne::API_URL }}">
    @foreach($fields as $name => $value)
        @if($name == 'WMI_PAYMENT_AMOUNT')
            <input type="text" name="{{ $name }}" value="{{ $value }}" />
        @endif
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
    @endforeach

    <input type="submit" value="{{ config('wallet-one.buttonLabel') }}" />
</form>