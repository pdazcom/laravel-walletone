<form id="walletone-form" method="post" action="{{ \Pdazcom\LaravelWalletOne\WalletOne::API_URL }}">
    @foreach($fields as $name => $value)
        <input type="{{ $name === 'WMI_PAYMENT_AMOUNT' ? 'text' : 'hidden' }}" name="{{ $name }}" value="{{ $value }}" />
    @endforeach

    <input type="submit" value="{{ config('wallet-one.buttonLabel') }}" />
</form>