
    @if(!blank($merchant->user->branch))
        {{ $merchant->user->branch->name.' ('.$merchant->user->branch->address.')' }}
    @else
    @endif

