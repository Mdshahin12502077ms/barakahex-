@if ($stock->type = 1)
    <span class="text-success">{{ __('stock_in') }}</span>
@else
    <span class="text-danger fs-6">{{ __('stock_out') }}</span>
@endif
