<form id="walletone-form" method="post" action="{{ \Pdazcom\LaravelWalletOne\WalletOne::API_URL }}">
    @foreach($fields as $name => $value)
        @if($name === 'WMI_PAYMENT_AMOUNT')
            <label for="{{ $name }}">{{ ucfirst($name) }}:</label>
            <input type="text" name="{{ $name }}" value="{{ $value }}" id="{{ $name }}"/>
            @continue
        @endif
        <input type="hidden" name="{{ $name }}" value="{{ $value }}" id="{{ $name }}"/>
    @endforeach

    <input type="submit" value="{{ config('wallet-one.buttonLabel') }}" />
</form>