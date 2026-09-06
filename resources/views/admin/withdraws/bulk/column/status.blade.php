<div>
    <a href="{{route('admin.withdraw.invoice.bulk', $bulk_payment->id)}}">
        @if($bulk_payment->status == 'pending')
            <span  class="badge text-warning">{{ __($bulk_payment->status) }}</span><br>
        @elseif($bulk_payment->status == 'processed')
            <span  class="badge text-success">{{ __($bulk_payment->status) }}</span>
        @endif
    </a>
    <div>
        @if(!blank(@$bulk_payment->receipt) && file_exists(@$bulk_payment->receipt))
            <a href="{{static_asset($bulk_payment->receipt)}}" target="_blank"> <i class="icon  las la-external-link-alt"></i> {{ __('receipt') }}</a>
        @else
            {{ __('not_available') }}
        @endif
    </div>
</div>

