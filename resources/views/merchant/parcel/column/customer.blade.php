<div>
    <span>{{ @$parcel->customer_name }}</span><br>

    <span>{{  isDemoMode() ? '**************' : @$parcel->customer_phone_number ?? '' }}</span><br>

    <!-- <span>{{ @$parcel->customer_address }}</span><br> -->

    {{ __('location') . ': ' . __($parcel->location) }}
</div>