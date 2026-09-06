<div>
    @if(!blank($expense->receipt) && file_exists($expense->receipt))
        <a href="{{static_asset($expense->receipt)}}" target="_blank"> <i class="icon  las la-external-link-alt"></i> {{ __('receipt') }}</a>
    @else
        {{ __('not_available') }}
    @endif
</div>
