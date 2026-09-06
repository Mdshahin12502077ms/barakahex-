<div>
    <span>
       @if(!blank($delivery_man->user->branch))
            {{ $delivery_man->user->branch->name.' ('.$delivery_man->user->branch->address.')' }}
        @else
            {{ __('not_available') }}
        @endif
    </span>
</div>
