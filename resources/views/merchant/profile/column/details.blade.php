<span>{{ __($query->details) }}</span><br>
<span>
    @if (@$query->parcel != '')
        {{ __('id') }}:#{{ __(@$query->parcel->parcel_no) }}
    @endif
    @if (@$query->parcel->customer_invoice_no != '')
        {{ __('invno') }}:#{{ __($query->parcel->customer_invoice_no) }}
    @endif
</span>
