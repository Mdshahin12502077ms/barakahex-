
<div>
    <span>{{ __('unpaid_amount') . ': ' . (format_price($merchant->balance($merchant->id))) }}</span><br>
    <span>{{ __('default_payment') . ': ' . @$merchant->defaultAccount->paymentAccount->name}}</span><br>
    <span>{{ __('withdraw') . ': ' . __($merchant->withdraw) }}</span><br>
</div>
