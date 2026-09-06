<div>
    @php
        $account_details = json_decode($payment->account_details);
    @endphp
        @if(@$payment->payments->type == 'bank')
            <span>{{ __('bank_name').': '.@$account_details[0] }} </span><br>
            <span>{{ __('branch').': '.@$account_details[1] }}</span><br>
            <span>{{ __('account_holder').': '.@$account_details[2] }}</span><br>
            <span>{{ __('account_no').': '.@$account_details[3] }}</span><br>
                @if(@$account_details[4] != '')
                    <span>{{ __('routing_no').': '.@$account_details[4] }}
                @endif
            </span>
        @elseif(@$payment->payments->type == 'mfs')
            <span>{{ __('payment_method').': '.__(@$account_details[0]) }} </span><br>
            <span>{{ __('account_type').': '.__(@$account_details[2]) }} </span><br>
            <span>{{ __('account_number').': '.@$account_details[1] }} </span><br>
        @else
            <span>{{ __('payment_method').': '. __(@$payment->payment_method_type) }} </span>
        @endif
</div>
