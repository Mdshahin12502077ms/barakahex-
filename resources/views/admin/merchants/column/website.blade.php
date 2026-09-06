
<span>
    <span>{{  __('website') }} : @if(!blank($merchant->website))
        <a class="btn btn-link text-success text-right" href="{{ $merchant->website }}" title="{{ $merchant->website }}" style="width: 90px;" target="_blank"><i class="la la-link text-success"></i> {{ __('visit') }}</a>
        @endif
    </span>
    <span>{{  __('license') }} :
        <a href="{{ getFileLink('80X80', $merchant->trade_license) }}" target="_blank"> <i class="la la-link"></i> {{ __('trade_license') }}</a>
    </span>
</span>

