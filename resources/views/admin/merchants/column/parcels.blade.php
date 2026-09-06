<span class="text-info">{{ __('parcels') . ': ' . $merchant->parcels->count() }}</span><br>
<span class="text-success">{{ __('delivered') . ': ' . $merchant->parcels->whereIn('status', ['delivered', 'delivered-and-verified'])->count() }}</span><br>
