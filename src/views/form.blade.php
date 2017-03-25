<form id="walletone-form" method="post" action="{{ \Pdazcom\LaravelWalletOne\WalletOne::API_URL }}">
    @foreach($fields as $name => $value)
        <label for="{{ $name }}">{{ ucfirst($name) }}:</label>
        <input type="{{ $name === 'WMI_PAYMENT_AMOUNT' ? 'text' : 'hidden' }}" name="{{ $name }}" value="{{ $value }}" id="{{ $name }}"/>
    @endforeach

    <input type="submit" value="{{ config('wallet-one.buttonLabel') }}" />
</form>