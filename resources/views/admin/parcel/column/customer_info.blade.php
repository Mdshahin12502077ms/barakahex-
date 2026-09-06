<span>{{ @$parcel->customer_name }}</span></br>
<span>{{  isDemoMode() ? '**************' : @$parcel->customer_phone_number ?? '' }}</span></br>
<span>{{ @$parcel->customer_address }}</span></br>
<span width="50%">{{ __('location') . ': ' . __($parcel->location) }}</span>
