<div>
    @if($bulk_payment->account)
        @if($bulk_payment->account->method == 'bank')
            <span>{{ __('payment_method').': '.__($bulk_payment->account->method) }} </span>
            <span>{{ __('bank_name').': '.__($bulk_payment->account->bank_name) }} </span> <br>
            <span>{{ __('branch').': '.__($bulk_payment->account->bank_branch) }}</span> <br>
            <span>{{ __('account_holder').': '.$bulk_payment->account->account_holder_name }}</span>
            <span>{{ __('account_no').': '.$bulk_payment->account->account_no }}</span>
        @elseif($bulk_payment->account->method == 'cash')
            <span>{{ __('payment_method').': '.__($bulk_payment->account->method) }} </span>
        @else
            <span>{{ __('payment_method').': '.__($bulk_payment->account->method) }} </span>
                <span>{{ __('account_holder').': '.$bulk_payment->account->account_holder_name }}</span><br>
                <span>{{ __('account_type').': '.__($bulk_payment->account->type) }} </span> <br>
                <span>{{ __('account_number').': '.$bulk_payment->account->number }} </span>
        @endif
    @endif
</div>
