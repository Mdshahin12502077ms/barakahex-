<div class="text-center">
    @if($parcel->status == 'pending')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('pending') }}</span><br>
    @elseif($parcel->status == 'deleted')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('deleted') }}</span><br>
    @elseif($parcel->status == 'cancel')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('cancelled') }}</span><br>
    @elseif($parcel->status == 'pickup-assigned')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('pickup_assigned') }}</span><br>
    @elseif($parcel->status == 're-schedule-pickup')
        <span class="badge text-info">{{ __('re_schedule_pickup') }}</span><br>
    @elseif($parcel->status == 'received-by-pickup-man')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('received_by_pickup_man') }}</span><br>
    @elseif($parcel->status == 'received')
        <span class="badge text-success">{{ __('received_by_warehouse') }}</span><br>
    @elseif($parcel->status == 'transferred-to-branch')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('transferred_to_branch') }}</span><br>
    @elseif($parcel->status == 'transferred-received-by-branch')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('transferred_received_by_branch') }}</span><br>
    @elseif($parcel->status == 'delivery-assigned')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('delivery_assigned') }}</span><br>
    @elseif($parcel->status == 're-schedule-delivery')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') ?? 'text-warning'}}">{{ __('re_schedule_delivery') }}</span><br>
    @elseif($parcel->status == 'returned-to-warehouse')
        @if($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially_delivered') }}</span><br>
        @endif
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('returned_to_warehouse') }}</span><br>
    @elseif($parcel->status == 'return-assigned-to-merchant')
        @if($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially_delivered') }}</span><br>
        @endif
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('return_assigned_to_merchant') }}</span><br>
    @elseif($parcel->status == 'partially-delivered')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially_delivered') }}</span><br>
    @elseif($parcel->status == 'delivered')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('delivered') }}</span><br>
    @elseif($parcel->status == 'delivered-and-verified')
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('delivered_and_verified') }}</span><br>
    @elseif($parcel->status == 'returned-to-merchant')
        @if($parcel->is_partially_delivered)
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('partially_delivered') }}</span><br>
        @endif
        <span
            class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('returned_to_merchant') }}</span><br>
    @elseif($parcel->status == 're-request')
        <span class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{ __('re_request') }}</span><br>
    @endif
    <span class="badge text-info">{{__($parcel->parcel_type)}}</span><br>

    @if($parcel->short_url != '')
        <div class="d-inline align-items-center gap-2 link-ellips">
            <label class="text-nowrap ">{{ __('tracking_url') . ': ' }}</label>
            <button type="button" data-text="{{ $parcel->short_url }}"
                class="copy-to-clipboard btn btn-default text-info mx-0 px-0 border-0">{{__($parcel->short_url)}}</button>
    @endif

        @if($parcel->location != 'dhaka' && ($parcel->status == 'delivery-assigned' || $parcel->status == 're-schedule-delivery') && $parcel->third_party_id != '')
            <span
                class="badge {{ config('parcel_status.' . $parcel->status . '.color') }}">{{__('third_party')}}</span><br><br>
        @endif
    </div>