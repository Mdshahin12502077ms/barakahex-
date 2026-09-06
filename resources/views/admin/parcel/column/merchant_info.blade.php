<span>
    {{ @$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff' ? $parcel->merchant->company . ' (' . @$parcel->user->first_name . ' ' . @$parcel->user->last_name . ')' : @$parcel->merchant->company }}
</span></br>
<span>{{  isDemoMode() ? '**************' : @$parcel->pickup_shop_phone_number ?? '' }}</span></br>
<span>{{ @$parcel->pickup_address }}</span></br>
<span>{{ __('weight') . ': ' . $parcel->weight . __(setting('default_weight')) }}</span></br>
<span>{{ __('charge') . ': ' . format_price($parcel->total_delivery_charge) }}</span></br>
<span>{{ __('COD') . ': ' . format_price($parcel->price) }}</span></br>
<span>{{ __('payable') . ': ' . format_price($parcel->payable) }}</span></br>
<span>{{ __('selling_price') . ': ' . format_price($parcel->selling_price) }}</span>
