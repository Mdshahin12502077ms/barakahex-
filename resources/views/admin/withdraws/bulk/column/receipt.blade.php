<div>
    @if(!blank(@$bulk_payment->receipt) && file_exists(@$bulk_payment->receipt))
        <a href="{{static_asset($bulk_payment->receipt)}}" target="_blank"> <i class="icon  las la-external-link-alt"></i> {{ __('receipt') }}</a>
    @else
        {{ __('not_available') }}
    @endif
</div>

