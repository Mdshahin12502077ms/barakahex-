<div>
    @php
        $account_details = json_decode($withdraw->account_details);
    @endphp
    @if (@$withdraw->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::BANK->value)
        <span>{{ __('bank_name') . ': ' . @$account_details[0] }}</span><br>
        <span>{{ __('branch') . ': ' . @$account_details[1] }}</span><br>
        <span>{{ __('account_holder') . ': ' . @$account_details[2] }}</span><br>
        <span>{{ __('account_no') . ': ' . @$account_details[3] }}</span>
        @if (@$account_details[4] != '')
            <br>
            <span>{{ __('routing_no') . ': ' . @$account_details[4] }}
        @endif
        </span>
    @elseIf(@$withdraw->merchantPaymentAccount->type == \App\Enums\PaymentMethodType::MFS->value)
        <span>{{ __('payment_method') . ': ' . __(@$account_details[0]) }}
        </span>
            <br><span>{{ __('account_type') . ': ' . __(@$account_details[2]) }}
            </span> <br>
            <span>{{ __('account_number') . ': ' . @$account_details[1] }}
            </span>
    @else
        <span>{{ __('payment_method').': '. __(@$withdraw->payment_method_type) }} </span>
    @endif
</div>
